# 🐧 Hướng dẫn Deploy QuanLyKTX lên Ubuntu + Docker

> **Mục tiêu:** Máy ảo Ubuntu chạy Docker làm máy chủ. Máy thật Windows truy cập qua IP nội bộ `192.168.190.128:8080`.

---

## 📋 Tổng quan kiến trúc

```
┌─────────────────────────────────────┐
│     Máy thật Windows (Client)       │
│  Chrome → 192.168.190.128:8080/...  │
└──────────────────┬──────────────────┘
                   │ Mạng nội bộ (VMware NAT/Bridged)
┌──────────────────▼──────────────────┐
│     Máy ảo Ubuntu (Server)          │
│  ┌────────────────────────────────┐ │
│  │  Docker Compose                │ │
│  │  ┌──────────┐  ┌────────────┐ │ │
│  │  │  Apache  │  │  MySQL DB  │ │ │
│  │  │ :80→8080 │  │   :3306   │ │ │
│  │  └──────────┘  └────────────┘ │ │
│  └────────────────────────────────┘ │
└─────────────────────────────────────┘
```

---

## 🔧 BƯỚC 1: Kiểm tra IP máy ảo Ubuntu

Mở Terminal trên Ubuntu, chạy:

```bash
ip addr show
# hoặc
hostname -I
```

> Tìm dòng có địa chỉ như `192.168.190.128` — đây là IP bạn sẽ dùng từ Windows.
> Nếu IP khác với `192.168.190.128`, cần cập nhật lại file cấu hình trong code.

---

## 🔧 BƯỚC 2: Clone code từ GitHub

```bash
# Di chuyển đến thư mục home (hoặc nơi bạn muốn)
cd ~

# Clone repo về (thay YOUR_USERNAME và YOUR_REPO bằng thông tin của bạn)
git clone https://github.com/YOUR_USERNAME/YOUR_REPO.git webktx

# Đi vào thư mục vừa clone
cd webktx
```

Sau khi clone xong, kiểm tra cấu trúc:

```bash
ls -la
# Phải thấy: Dockerfile, docker-compose.yml, QuanLyKTX_API/, QuanLyKTX_user/
```

---

## 🔧 BƯỚC 3: Kiểm tra Docker đã cài đúng chưa

```bash
# Kiểm tra Docker
docker --version
# Kết quả mong đợi: Docker version 24.x.x hoặc cao hơn

# Kiểm tra Docker Compose
docker compose version
# Kết quả mong đợi: Docker Compose version v2.x.x

# Kiểm tra Docker daemon đang chạy
sudo systemctl status docker
```

Nếu Docker chưa chạy:

```bash
sudo systemctl start docker
sudo systemctl enable docker   # Tự khởi động cùng hệ thống
```

---

## 🔧 BƯỚC 4: Cho phép chạy Docker không cần sudo (tuỳ chọn)

```bash
sudo usermod -aG docker $USER
newgrp docker   # Áp dụng ngay không cần logout
```

---

## 🔧 BƯỚC 5: Build và khởi động hệ thống

```bash
# Đảm bảo bạn đang ở thư mục chứa docker-compose.yml
cd ~/webktx

# Build image và khởi động tất cả services (chạy ở background)
docker compose up --build -d
```

Lần đầu sẽ mất vài phút để tải image. Sau đó kiểm tra:

```bash
# Xem các container đang chạy
docker compose ps

# Kết quả mong đợi:
# NAME              STATUS          PORTS
# ktx_apache_web    Up              0.0.0.0:8080->80/tcp
# ktx_mysql_db      Up              0.0.0.0:3306->3306/tcp
```

---

## 🔧 BƯỚC 6: Kiểm tra Apache hoạt động từ chính Ubuntu

```bash
# Test ngay trên Ubuntu
curl http://localhost:8080/QuanLyKTX_user/
# Phải trả về HTML (không báo lỗi Connection refused)

curl http://localhost:8080/QuanLyKTX_API/
```

---

## 🔧 BƯỚC 7: Mở cổng firewall trên Ubuntu (QUAN TRỌNG)

```bash
# Kiểm tra firewall
sudo ufw status

# Nếu firewall đang active, mở cổng 8080
sudo ufw allow 8080/tcp

# Reload
sudo ufw reload

# Xác nhận
sudo ufw status | grep 8080
```

---

## 🔧 BƯỚC 8: Nhập dữ liệu Database (SQL)

Database MySQL vừa tạo sẽ trống. Cần import file SQL:

```bash
# Cách 1: Nếu bạn có file .sql trong repo
sudo docker exec -i ktx_mysql_db mysql \
  -u root -proot_password quanlykytucxa \
  < ~/ubuntu-website/database/quanlykytucxa.sql


# Cách 2: Vào MySQL shell để kiểm tra
docker exec -it ktx_mysql_db mysql -u root -proot_password

# Trong MySQL shell:
SHOW DATABASES;
USE quanlykytucxa;
SHOW TABLES;
EXIT;
```

---

## 🔧 BƯỚC 9: Truy cập từ máy thật Windows

Mở Chrome/Edge trên Windows và truy cập:

| Mục đích               | URL                                          |
| ---------------------- | -------------------------------------------- |
| 🎓 Giao diện Sinh Viên | `http://192.168.190.128:8080/QuanLyKTX_user` |
| ⚙️ Hệ thống Admin/API  | `http://192.168.190.128:8080/QuanLyKTX_API`  |

---

## 🐛 Xử lý sự cố thường gặp

### ❌ Không kết nối được từ Windows

```bash
# 1. Ping từ Windows kiểm tra mạng
ping 192.168.190.128

# 2. Nếu không ping được → vào VMware:
#    Settings → Network Adapter → chọn "Bridged" hoặc "NAT"
#    Sau đó restart máy ảo và kiểm tra lại IP

# 3. Kiểm tra Docker có đang bind đúng cổng không
docker ps
# Phải thấy: 0.0.0.0:8080->80/tcp  (không phải 127.0.0.1:8080)
```

### ❌ Lỗi kết nối Database

```bash
# Xem log của container Apache
docker compose logs web

# Xem log của MySQL
docker compose logs db

# Đợi MySQL khởi động xong (lần đầu cần ~30 giây)
docker compose logs db | tail -5
# Phải thấy: "ready for connections"
```

### ❌ Lỗi 404 khi vào trang

```bash
# Kiểm tra file có được mount vào container không
docker exec ktx_apache_web ls /var/www/html/
# Phải thấy: QuanLyKTX_API/  QuanLyKTX_user/  Dockerfile  docker-compose.yml

# Kiểm tra mod_rewrite đã bật chưa
docker exec ktx_apache_web apachectl -M | grep rewrite
# Phải thấy: rewrite_module (shared)
```

### ❌ Lỗi `.htaccess` không hoạt động

```bash
# Vào container kiểm tra cấu hình Apache
docker exec -it ktx_apache_web bash

# Bên trong container:
cat /etc/apache2/apache2.conf | grep AllowOverride
# Nếu thấy "AllowOverride None" → cần sửa Dockerfile
```

Nếu cần sửa, thêm vào **Dockerfile** trước `EXPOSE 80`:

```dockerfile
RUN echo '<Directory /var/www/html>\n    AllowOverride All\n    Options Indexes FollowSymLinks\n    Require all granted\n</Directory>' > /etc/apache2/conf-enabled/htaccess.conf
```

Rồi build lại:

```bash
docker compose down
docker compose up --build -d
```

---

## 📌 Các lệnh quản lý Docker thường dùng

```bash
# Khởi động lại toàn bộ hệ thống
docker compose restart

# Dừng tất cả
docker compose down

# Xem log realtime
docker compose logs -f

# Cập nhật code mới (khi pull từ git)
git pull
docker compose up --build -d   # Rebuild nếu Dockerfile thay đổi
# hoặc nếu chỉ sửa PHP:
docker compose restart web     # Không cần rebuild, chỉ restart Apache

# Vào bên trong container Apache để debug
docker exec -it ktx_apache_web bash
```

---

## ✅ Checklist hoàn thành

- [ ] Ubuntu có IP `192.168.190.128` (hoặc IP tương tự)
- [ ] Docker và Docker Compose đã cài
- [ ] Đã clone code từ GitHub vào `~/webktx`
- [ ] `docker compose up --build -d` chạy thành công
- [ ] `docker compose ps` hiển thị 2 container `Up`
- [ ] Firewall đã mở cổng `8080`
- [ ] Từ Windows, `ping 192.168.190.128` thành công
- [ ] Truy cập `http://192.168.190.128:8080/QuanLyKTX_user` mở được trang web
