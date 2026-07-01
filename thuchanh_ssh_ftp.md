# Hướng dẫn Thực hành SSH và FTP trên Ubuntu

Tài liệu này hướng dẫn chi tiết các bước để thực hiện yêu cầu cấu hình SSH và FTP cơ bản trên Ubuntu.

---

## PHẦN 1: SSH — Các cấu hình cơ bản

### 1.1. Đổi port SSH
Mặc định SSH dùng port 22, ta sẽ đổi sang một port khác (ví dụ: 2222) để tăng tính bảo mật.

```bash
sudo nano /etc/ssh/sshd_config
```
Tìm dòng có chữ `#Port 22`, bỏ dấu `#` và sửa thành:
```text
Port 2222
```
Sau đó khởi động lại dịch vụ SSH và mở tường lửa:
```bash
sudo systemctl restart ssh
sudo ufw allow 2222/tcp
```

### 1.2. Tạo người dùng không cho phép cài đặt (chỉ dùng môi trường hiện có)
Để tạo một user bị giới hạn, ta chỉ cần tạo user bình thường và **không** cấp quyền `sudo`.

```bash
# Tạo user mới (hệ thống sẽ hỏi mật khẩu và thông tin)
sudo adduser user_limited

# Kiểm tra xem user có nằm trong nhóm sudo không
groups user_limited
# Kết quả mong đợi: user_limited : user_limited (không có chữ sudo)
```
**Giải thích:** User này có thể kết nối SSH vào server, chạy các lệnh cơ bản (`ls`, `cd`, `git`, v.v.) nhưng không thể dùng lệnh `sudo` (ví dụ: `sudo apt install`) để cài đặt phần mềm hay thay đổi hệ thống.

### 1.3. Tạo người dùng cho phép full quyền (tương tự root)
Tạo user và cấp quyền `sudo` để user này có thể quản trị hệ thống.

```bash
# Tạo user mới
sudo adduser user_admin

# Cấp quyền sudo (full quyền quản trị)
sudo usermod -aG sudo user_admin

# Kiểm tra lại
groups user_admin
# Kết quả mong đợi: user_admin : user_admin sudo
```

### 1.4. Cho phép / Cấm truy cập SSH
Ta có thể cấu hình để chỉ cho phép một số user nhất định được phép SSH, hoặc cấm cụ thể ai đó.

```bash
sudo nano /etc/ssh/sshd_config
```
Thêm vào **cuối file** các cấu hình sau tùy mục đích:
```text
# Chỉ cho phép các user này SSH vào:
AllowUsers dunggnguyenn user_admin

# Hoặc nếu muốn cấm 1 user cụ thể:
DenyUsers user_limited
```
Sau đó khởi động lại SSH:
```bash
sudo systemctl restart ssh
```

---

## PHẦN 2: FTP — Các cấu hình cơ bản (sử dụng vsftpd)

Trước tiên, cài đặt FTP server (`vsftpd`):
```bash
sudo apt update
sudo apt install vsftpd -y
sudo systemctl start vsftpd
sudo systemctl enable vsftpd

# Backup lại file cấu hình gốc để phòng hờ
sudo cp /etc/vsftpd.conf /etc/vsftpd.conf.bak
```

### 2.1. Tạo người dùng cho phép truy cập (download và upload file) [1]
Mặc định vsftpd có thể chỉ cho download. Ta cần bật quyền ghi (write).

```bash
sudo adduser ftp_user1
sudo nano /etc/vsftpd.conf
```
Tìm và sửa (hoặc thêm) các dòng sau:
```text
write_enable=YES
local_enable=YES
chroot_local_user=NO
```
Khởi động lại FTP:
```bash
sudo systemctl restart vsftpd
```

### 2.2. Tạo người dùng cho phép như [1], nhưng chỉ trong 1 folder nào đấy
Để nhốt (chroot) user vào thư mục cá nhân của họ, không cho đi lang thang ra các thư mục khác của hệ thống:

```bash
sudo adduser ftp_user2

# (Tùy chọn) Tạo một thư mục con riêng để chứa file
sudo mkdir -p /home/ftp_user2/ftp_folder
sudo chown ftp_user2:ftp_user2 /home/ftp_user2/ftp_folder

sudo nano /etc/vsftpd.conf
```
Thêm hoặc sửa cấu hình:
```text
chroot_local_user=YES
allow_writeable_chroot=YES
```
Khởi động lại FTP:
```bash
sudo systemctl restart vsftpd
```

### 2.3. Tạo người dùng cho phép như [1], nhưng trong nhiều hơn 1 folder nào đấy
Để user có thể đi qua lại giữa nhiều thư mục, ta phải **tắt tính năng chroot**.

```bash
sudo adduser ftp_user3

# Tạo các thư mục dùng chung
sudo mkdir -p /ftp_share/folder1 /ftp_share/folder2
sudo chown ftp_user3:ftp_user3 /ftp_share/folder1 /ftp_share/folder2

sudo nano /etc/vsftpd.conf
```
Đảm bảo cấu hình là:
```text
chroot_local_user=NO
```
*(User này sẽ có thể truy cập `folder1`, `folder2` hoặc bất kỳ thư mục nào họ có quyền đọc/ghi).*

### 2.4. Tạo người dùng chỉ cho phép download file, nhưng không được upload file
Ta tạo user và thiết lập quyền `write_enable=NO` **riêng** cho user này.

```bash
sudo adduser ftp_readonly

# Tạo thư mục chứa cấu hình riêng cho từng user
sudo mkdir -p /etc/vsftpd/user_conf

# Bỏ quyền upload của ftp_readonly
echo "write_enable=NO" | sudo tee /etc/vsftpd/user_conf/ftp_readonly

# Khai báo với vsftpd sử dụng thư mục cấu hình riêng này
echo "user_config_dir=/etc/vsftpd/user_conf" | sudo tee -a /etc/vsftpd.conf

sudo systemctl restart vsftpd
```

### 2.5. Tạo người dùng chỉ cho phép upload file, ko cho phép download file
Cách thực hiện: Cấp quyền ghi trên thư mục nhưng **tắt quyền đọc**, kết hợp cấu hình `download_enable=NO`.

```bash
sudo adduser ftp_writeonly

# Tạo thư mục upload
sudo mkdir -p /home/ftp_writeonly/upload

# Phân quyền 311: Có quyền Write (2) và Execute (1) = 3, KHÔNG có quyền Read (4)
sudo chmod 311 /home/ftp_writeonly/upload

# Cấu hình riêng cho user này: Tắt download, Bật upload
echo "write_enable=YES
download_enable=NO" | sudo tee /etc/vsftpd/user_conf/ftp_writeonly

sudo systemctl restart vsftpd
```

### 2.6. Tạo người dùng có full quyền truy cập tất cả các folder
Tạo user, cấp quyền `sudo` và đảm bảo vsftpd không giới hạn (chroot) user này.

```bash
sudo adduser ftp_full
sudo usermod -aG sudo ftp_full

# Đảm bảo cấu hình không giới hạn quyền đọc/ghi
echo "write_enable=YES
download_enable=YES" | sudo tee /etc/vsftpd/user_conf/ftp_full

sudo systemctl restart vsftpd
```
*(Lưu ý: FTP user có full quyền hệ thống là một rủi ro bảo mật lớn).*

---

### 2.7. Làm thế nào để chuyển đổi quyền của 1 user nhanh nhất?

**Cách nhanh nhất và chuẩn xác nhất** với vsftpd là sử dụng tính năng **User Config (Cấu hình riêng theo từng user)** kết hợp với lệnh ghi đè nội dung file.

Ví dụ, để chuyển đổi quyền của `ftp_user1`:

1.  **Muốn cấp quyền Full (Upload + Download):**
    ```bash
    echo "write_enable=YES
    download_enable=YES" | sudo tee /etc/vsftpd/user_conf/ftp_user1
    ```
2.  **Muốn đổi sang Read-only (Chỉ Download):**
    ```bash
    echo "write_enable=NO
    download_enable=YES" | sudo tee /etc/vsftpd/user_conf/ftp_user1
    ```
3.  **Khởi động lại dịch vụ** để áp dụng thay đổi ngay lập tức:
    ```bash
    sudo systemctl restart vsftpd
    ```

**Giải thích:**
Tính năng `user_config_dir` của vsftpd cho phép ghi đè các cấu hình chung. Khi cần đổi quyền, người quản trị chỉ cần chạy 1 lệnh `echo` duy nhất để sửa file cấu hình của riêng user đó mà không làm ảnh hưởng đến bất kỳ user nào khác, không cần phải cấu hình lại các nhóm (groups) rườm rà của Linux.
