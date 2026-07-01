
CREATE DATABASE quanlykytucxa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quanlykytucxa;

-- Bảng tài khoản Admin (độc lập, không liên kết)
CREATE TABLE taikhoan_admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng Sinh Viên
CREATE TABLE sinhvien (
    id INT PRIMARY KEY AUTO_INCREMENT,
    masv VARCHAR(20) UNIQUE NOT NULL,
    hoten VARCHAR(100) NOT NULL,
    lop VARCHAR(20),
    gioitinh VARCHAR(10),
    cccd VARCHAR(20),
    sodienthoai VARCHAR(15),
    email VARCHAR(100),
    diachi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng tài khoản User (sinh viên)
CREATE TABLE taikhoan_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    masv VARCHAR(20) UNIQUE NOT NULL,          -- dùng masv làm khóa chính
    password VARCHAR(255) NOT NULL,        -- mật khẩu đăng nhập
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (masv) REFERENCES sinhvien(masv)
);

-- Bảng Phòng
CREATE TABLE phong (
    id INT PRIMARY KEY AUTO_INCREMENT,
    maphong VARCHAR(20) UNIQUE NOT NULL,
    sophong VARCHAR(10),
    toa VARCHAR(5),
    succhua INT DEFAULT 8,
    phonghientai INT DEFAULT 0,
    gia DECIMAL(10,2),
    trangthai VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng Hợp Đồng
CREATE TABLE hopdong (
    mahopdong VARCHAR(20) PRIMARY KEY,
    masv VARCHAR(20),
    maphong VARCHAR(20),
    batdau DATE,
    hethan DATE,
    trangthai VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (masv) REFERENCES sinhvien(masv),
    FOREIGN KEY (maphong) REFERENCES phong(maphong)
);

-- Bảng Thanh Toán
CREATE TABLE thanhtoan (
    mathanhtoan INT PRIMARY KEY AUTO_INCREMENT,
    maphong VARCHAR(20),
    sotien DECIMAL(10,2),
    ngaytra DATE,
    trangthai VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maphong) REFERENCES phong(maphong)
);

-- Bảng Sự Cố
CREATE TABLE suco (
    masuco INT PRIMARY KEY AUTO_INCREMENT,
    maphong VARCHAR(20),
    mota TEXT,
    ngaybao DATE,
    trangthai VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (maphong) REFERENCES phong(maphong)
);
```

````

#### C. Kiểm Tra Cấu Hình Database

Edit file `Config/Database.php` nếu cần:

```php
private $host = 'localhost';      // DB host
private $db_name = 'quanlykytucxa'; // DB name
private $username = 'root';       // DB user
private $password = '';           // DB password
````

### 3️⃣ **Chạy Ứng Dụng**

```
http://localhost/HellomynameisPencilan/QuanLyKTX_MVC/
```

### 4️⃣ **Đăng Nhập**

- **Username**: `admin`
- **Password**: `admin123`

---

## 📚 Cấu Trúc URL

### Sinh Viên (Student Module)

```
GET  http://localhost/.../student              → Danh sách
GET  http://localhost/.../student/create       → Form thêm
POST http://localhost/.../student/store        → Lưu thêm
GET  http://localhost/.../student/edit/SV001   → Form sửa
POST http://localhost/.../student/update/SV001 → Lưu sửa
GET  http://localhost/.../student/delete/SV001 → Xóa
```

### Phòng (Room Module)

```
GET  http://localhost/.../room                → Danh sách
GET  http://localhost/.../room/create         → Form thêm
POST http://localhost/.../room/store          → Lưu thêm
GET  http://localhost/.../room/edit/P101      → Form sửa
POST http://localhost/.../room/update/P101    → Lưu sửa
GET  http://localhost/.../room/delete/P101    → Xóa
```

### Hợp Đồng, Thanh Toán, Sự Cố (tương tự)

```
/contract/...
/payment/...
/incident/...
```

---

## 🎯 File Cấu Trúc Chính

```
QuanLyKTX_MVC/
├── Config/Database.php           ← Cấu hình DB
├── Core/
│   ├── Controller.php            ← Base controller
│   ├── Model.php                 ← Base model
│   └── Repository.php            ← Base repository
├── Modules/
│   ├── Auth/
│   │   ├── Controllers/AuthController.php
│   │   └── Views/login.php, dashboard.php
│   ├── Student/
│   │   ├── Controllers/StudentController.php
│   │   ├── Repositories/StudentRepository.php
│   │   └── Views/list.php, form.php
│   ├── Room/
│   ├── Contract/
│   ├── Payment/
│   └── Incident/
├── index.php                     ← Entry point
└── router.php                    ← Router
```

---

## 🔍 Ví Dụ: Thêm Sinh Viên

### Flow:

```
1. User click "Thêm Sinh Viên"
   → GET /student/create

2. StudentController::create()
   → view('form', [])
   → Render form.php

3. User điền thông tin + submit
   → POST /student/store

4. StudentController::store()
   → Validate input
   → $studentRepo->create($data)
   → Redirect /student

5. LIST danh sách sinh viên
```

### Code:

```php
// Modules/Student/Controllers/StudentController.php
public function create() {
    $this->view('form', ['title' => 'Thêm Sinh Viên Mới']);
}

public function store() {
    $data = [
        'masv' => $this->getInput('masv'),
        'hoten' => $this->getInput('hoten'),
        // ...
    ];

    if ($this->studentRepo->create($data)) {
        $_SESSION['success'] = 'Thêm thành công!';
        $this->redirect(BASE_URL . 'student');
    }
}
```

---

## 🛠️ Các Công Việc Cần Hoàn Thành

### ✅ Hoàn Thành

- [x] Auth Module (xác thực)
- [x] Student Module (CRUD + search)
- [x] Room Module (CRUD)
- [x] Contract Module (list + CRUD)
- [x] Payment Module (list)
- [x] Incident Module (list)

### ⏳ Cần Hoàn Thành (tùy chọn)

- [ ] Thêm form.php cho Contract, Payment, Incident
- [ ] Thêm search/filter
- [ ] Thêm pagination
- [ ] Thêm export Excel
- [ ] Thêm Dashboard với biểu đồ
- [ ] Thêm Validation Layer
- [ ] Thêm Authentication security (password hashing)

---

## 📝 Mẹo

### Để thêm module mới, làm:

1. **Tạo thư mục**: `Modules/NewFeature/`
2. **Tạo Repository**:

   ```php
   class NewFeatureRepository extends Repository {
       protected $table = 'table_name';
       // Kế thừa từ base class:
       // getAll(), findById(), create(), update(), delete()
   }
   ```

3. **Tạo Controller**:

   ```php
   class NewFeatureController extends Controller {
       public function index() { ... }
       public function create() { ... }
       public function store() { ... }
   }
   ```

4. **Tạo Views**: `list.php`, `form.php`

5. **Thêm link**: edit `Modules/Auth/Views/dashboard.php`

Router sẽ tự động nhận diện! ✨

---

## 🆘 Troubleshooting

### ❌ "Database Connection Failed"

- Kiểm tra MySQL chạy chưa
- Kiểm tra credentials trong `Config/Database.php`
- Kiểm tra database `quanlykytucxa` tồn tại

### ❌ "View file not found"

- Kiểm tra file view tồn tại
- Kiểm tra tên module đúng cách (Student → student)

### ❌ "Controller không tìm thấy"

- Kiểm tra tên controller: `Student` (case-sensitive)
- Kiểm tra file: `StudentController.php`
- Kiểm tra class: `class StudentController extends Controller`

### ❌ "Method không tồn tại"

- Kiểm tra action method có public
- Kiểm tra tên action: `index`, `create`, `store`, etc.

---

## 📚 Tài Liệu Thêm

- **MVC_GUIDE.md** - Hướng dẫn chi tiết về MVC
- **README.md** - Tổng quan dự án

---

**Chúc bạn thành công! 🎉**

Nếu có vấn đề, hãy kiểm tra:

1. Browser console (F12)
2. Server log (terminal)
3. Database (phpmyadmin)
4. Code comment trong file

## 3 dòng dữ liệu thêm vào mỗi table

-- tài khoản
INSERT INTO taikhoan_admin (username, password) VALUES
('admin', 'admin123');

-- sinh viên
INSERT INTO sinhvien (masv, hoten, lop, gioitinh, cccd, sodienthoai, email, diachi)
VALUES
('74DCTT001', 'Nguyen Van A', 'CNTT1', 'Nam', '012345678901', '0901234567', 'a@example.com', 'Hà Nội'),
('74DCTT002', 'Tran Thi B', 'CNTT2', 'Nữ', '012345678902', '0902345678', 'b@example.com', 'Hải Phòng'),
('74DCTT003', 'Le Van C', 'CNTT3', 'Nam', '012345678903', '0903456789', 'c@example.com', 'Nam Định');

--INSERT INTO taikhoan_user (masv, password)
VALUES
('SV001', '$2y$10$abc123hashedpasswordexample1'),
('SV002', '$2y$10$abc123hashedpasswordexample2'),
('SV003', '$2y$10$abc123hashedpasswordexample3');

-- phòng
INSERT INTO phong (maphong, sophong, toa, succhua, phonghientai, gia, trangthai)
VALUES
('P101', '101', 'A', 8, 5, 1200000, 'Trống'),
('P102', '102', 'A', 8, 8, 1200000, 'Đầy'),
('P201', '201', 'B', 6, 4, 1500000, 'Trống');

-- hợp đồng
INSERT INTO hopdong (mahopdong, masv, maphong, batdau, hethan, trangthai)
VALUES
('HD001', '74DCTT001', 'P101', '2026-01-01', '2026-12-31', 'Còn hiệu lực'),
('HD002', '74DCTT002', 'P102', '2026-02-01', '2026-12-31', 'Còn hiệu lực'),
('HD003', '74DCTT003', 'P201', '2026-03-01', '2026-12-31', 'Còn hiệu lực');

-- thanh toán
INSERT INTO thanhtoan (maphong, sotien, ngaytra, trangthai)
VALUES
('P101', 1200000, '2026-01-05', 'Đã trả'),
('P102', 1200000, '2026-02-05', 'Đã trả'),
('P201', 1500000, '2026-03-05', 'Đã trả');

-- sự cố
INSERT INTO suco (maphong, mota, ngaybao, trangthai)
VALUES
('P101', 'Hỏng bóng đèn', '2026-01-10', 'Đã xử lý'),
('P102', 'Rò rỉ nước', '2026-02-12', 'Đang xử lý'),
('P201', 'Ổ cắm điện hỏng', '2026-03-08', 'Đã xử lý');
