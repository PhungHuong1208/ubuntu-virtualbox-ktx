# 📋 Dự Án Quản Lý Ký Túc Xá (KTX)

> **Tổng hợp tài liệu kỹ thuật** — Mô tả kiến trúc, luồng dữ liệu và cấu trúc thư mục toàn bộ hệ thống.

---

## 🗂️ Tổng Quan Hệ Thống

Dự án bao gồm **2 module** hoạt động độc lập, giao tiếp với nhau qua HTTP API:

| Module | Đường dẫn | Vai trò |
|---|---|---|
| **QuanLyKTX_API** | `/webktx/QuanLyKTX_API/` | Backend trung tâm — quản lý admin, xử lý DB, cung cấp API |
| **QuanLyKTX_user** | `/webktx/QuanLyKTX_user/` | Frontend sinh viên — giao diện web, gọi API từ module trên |

**Môi trường triển khai:** XAMPP (localhost) hoặc Docker (MySQL host = `db`)
**Ngôn ngữ:** PHP thuần (không dùng framework), MySQL/MySQLi
**Database:** `quanlykytucxa`

---

## 🏗️ Kiến Trúc Tổng Thể

### Mô hình: Hybrid API (SSR + REST)

```
┌─────────────────────────────────────────────────┐
│                  QuanLyKTX_API                  │
│                                                 │
│  ┌──────────────────────────────────────────┐  │
│  │         Public/index.php (Entry Point)    │  │
│  │    ┌──────────────┬───────────────────┐  │  │
│  │    │  URL: /api/* │  URL: /room, /... │  │  │
│  │    │  → JSON API  │  → HTML (SSR)     │  │  │
│  │    └──────────────┴───────────────────┘  │  │
│  └──────────────────────────────────────────┘  │
│                                                 │
│  Controller → Service → Repository → MySQL DB   │
└─────────────────────────────────────────────────┘
                        ▲
                    HTTP API
                (file_get_contents)
                        │
┌─────────────────────────────────────────────────┐
│               QuanLyKTX_user                    │
│                                                 │
│   index.php → UserRouter.php → Controller       │
│   Controller → Model (gọi API) → View (HTML)   │
│                                                 │
│   Sinh viên: login / xem phòng / xem hợp đồng  │
│              báo cáo sự cố / đổi mật khẩu      │
└─────────────────────────────────────────────────┘
```

**Đặc điểm nổi bật:**
- **Single Entry Point** tại `Public/index.php` — mọi request đều qua đây
- **Hybrid**: cùng một Controller vừa trả về HTML (admin web), vừa trả về JSON (API cho sinh viên / mobile)
- **Session tách biệt**: Admin dùng `PHPSESSID`, sinh viên dùng `USER_KTX_STUDENT`

---

## 📦 Module 1 — QuanLyKTX_API

### Kiến Trúc 4 Tầng (MVC + Service + Repository)

```
Request
  │
  ▼
[Controller]  ─ Nhận request, trả về View hoặc JSON
  │
  ▼
[Service]     ─ Business Logic (validate, quy tắc nghiệp vụ)
  │
  ▼
[Repository]  ─ Data Access Layer (chứa toàn bộ SQL)
  │
  ▼
[Database]    ─ MySQL (kết nối qua Config/Database.php)
```

### Cấu Trúc Thư Mục

```
QuanLyKTX_API/
│
├── Public/                      # Entry point duy nhất
│   ├── index.php                # ⭐ Router chính (Hybrid API)
│   ├── .htaccess                # Rewrite rules
│   └── css/, image/             # Static assets
│
├── Config/
│   └── Database.php             # Singleton kết nối MySQL (MySQLi)
│
├── Core/                        # Base classes
│   ├── Controller.php           # Base controller: view(), jsonResponse(), requireAuth()
│   ├── Repository.php           # Base repository: getAll(), findById(), delete(), fetchAll()
│   └── Model.php                # (trống/placeholder)
│
├── Routes/
│   ├── api.php                  # Map URL → Controller@Method (GET/POST/PUT/DELETE)
│   └── apiUser.php              # API endpoint riêng cho QuanLyKTX_user (action-based)
│
├── Controllers/                 # 8 controllers
│   ├── AuthController.php       # Đăng nhập / đăng xuất admin
│   ├── RoomController.php       # Quản lý phòng (CRUD + export/import CSV)
│   ├── StudentController.php    # Quản lý sinh viên (CRUD)
│   ├── ContractController.php   # Quản lý hợp đồng (CRUD)
│   ├── PaymentController.php    # Quản lý thanh toán (CRUD)
│   ├── IncidentController.php   # Quản lý sự cố (CRUD)
│   ├── UserController.php       # API endpoints phục vụ sinh viên
│   └── UtilityController.php    # Tiện ích (export, import, báo cáo...)
│
├── Services/                    # Business Logic Layer — 9 services
│   ├── AuthService.php          # Xác thực tài khoản admin
│   ├── RoomService.php          # Logic phòng (validate mã phòng/tòa)
│   ├── StudentService.php       # Logic sinh viên
│   ├── ContractService.php      # Logic hợp đồng (validate ngày, phòng)
│   ├── PaymentService.php       # Logic thanh toán
│   ├── IncidentService.php      # Logic sự cố
│   ├── UtilityService.php       # Tiện ích tổng hợp
│   ├── ExportService.php        # Xuất CSV
│   └── ImportService.php        # Nhập CSV
│
├── Models/                      # Repository Layer (Data Access) — 8 files
│   ├── AuthRepository.php       # SQL cho taikhoan_admin + taikhoan_user
│   ├── RoomRepository.php       # SQL cho bảng phong
│   ├── StudentRepository.php    # SQL cho bảng sinhvien
│   ├── ContractRepository.php   # SQL cho bảng hopdong
│   ├── PaymentRepository.php    # SQL cho bảng thanhtoan
│   ├── IncidentRepository.php   # SQL cho bảng suco
│   ├── UserRepository.php       # SQL đặc thù cho user API
│   └── UtilityRepository.php    # SQL báo cáo, thống kê
│
├── Views/                       # HTML templates (Server-Side Rendering)
│   ├── auth/                    # login.php, dashboard.php
│   ├── room/                    # list.php, form.php, danhsach.php, import.php
│   ├── student/                 # list.php, form.php, import.php
│   ├── contract/                # list.php, form.php
│   ├── payment/                 # list.php, form.php
│   ├── incident/                # list.php, form.php, import.php
│   └── utility/                 # các view tiện ích
│
└── index.php                    # Redirect đơn giản về Public/
```

### API Endpoints (RESTful)

| Method | Endpoint | Controller@Action |
|--------|----------|-------------------|
| `POST` | `/api/auth/login` | `AuthController@login` |
| `POST` | `/api/auth/logout` | `AuthController@logout` |
| `GET` | `/api/rooms` | `RoomController@index` |
| `GET` | `/api/rooms/{id}` | `RoomController@edit` |
| `GET` | `/api/rooms/{id}/students` | `RoomController@danhsach` |
| `POST` | `/api/rooms` | `RoomController@store` |
| `PUT` | `/api/rooms/{id}` | `RoomController@update` |
| `DELETE` | `/api/rooms/{id}` | `RoomController@delete` |
| `GET` | `/api/students` | `StudentController@index` |
| `GET` | `/api/students/{id}` | `StudentController@edit` |
| `POST` | `/api/students` | `StudentController@store` |
| `PUT` | `/api/students/{id}` | `StudentController@update` |
| `DELETE` | `/api/students/{id}` | `StudentController@delete` |
| `GET` | `/api/contracts` | `ContractController@index` |
| `GET` | `/api/contracts/{id}` | `ContractController@edit` |
| `POST` | `/api/contracts` | `ContractController@store` |
| `PUT` | `/api/contracts/{id}` | `ContractController@update` |
| `DELETE` | `/api/contracts/{id}` | `ContractController@delete` |
| `GET` | `/api/payments` | `PaymentController@index` |
| `GET` | `/api/incidents` | `IncidentController@index` |
| `GET` | `/api/user/profile` | `UserController@getProfile` |
| `GET` | `/api/user/room` | `UserController@getRoom` |
| `GET` | `/api/user/contracts` | `UserController@getContracts` |
| `GET` | `/api/user/incidents` | `UserController@getIncidents` |
| `PUT` | `/api/user/profile` | `UserController@updateProfile` |
| `PUT` | `/api/user/password` | `UserController@updatePassword` |
| `POST` | `/api/user/incidents` | `UserController@createIncident` |

### API Endpoint Dành Riêng Cho Sinh Viên (Action-based)

Endpoint: `GET/POST /Routes/apiUser.php?action=<action>`

| Action | Method | Mô tả |
|--------|--------|--------|
| `login` | POST | Đăng nhập sinh viên (masv, password) |
| `student` | GET | Lấy thông tin cá nhân (masv) |
| `student_update` | POST | Cập nhật thông tin cá nhân |
| `change_password` | POST | Đổi mật khẩu (masv, old_password, new_password) |
| `room` | GET | Xem thông tin phòng (masv) |
| `contract` | GET | Xem hợp đồng (masv) |
| `incident` | GET | Xem lịch sử sự cố (masv) |
| `reportIncident` | POST | Gửi báo cáo sự cố mới |

---

## 📦 Module 2 — QuanLyKTX_user

### Kiến Trúc (MVC + API Model)

```
Request (Sinh Viên)
  │
  ▼
index.php  →  UserRouter.php
                  │
                  ▼ (phân tích URL ?url=module/action)
              Controller  (kế thừa Core/Controller.php)
                  │
                  ▼
              Model  (gọi HTTP đến QuanLyKTX_API/Routes/apiUser.php)
                  │
                  ▼ (nhận JSON → parse)
              View  (render HTML)
```

> **Lưu ý quan trọng:** Module này **KHÔNG kết nối trực tiếp vào database**.
> Toàn bộ dữ liệu lấy qua HTTP API từ `QuanLyKTX_API`.

### Cấu Trúc Thư Mục

```
QuanLyKTX_user/
│
├── index.php                    # Entry point, load UserRouter.php
├── UserRouter.php               # Router: phân tích URL → gọi Controller
├── .htaccess                    # Rewrite URL
│
├── Core/
│   └── Controller.php           # Base controller: view(), redirect(), session helpers
│
├── Controllers/                 # 5 controllers (chỉ cho sinh viên)
│   ├── AuthController.php       # Đăng nhập, đăng xuất, đổi mật khẩu, dashboard
│   ├── StudentController.php    # Xem & cập nhật thông tin cá nhân
│   ├── RoomController.php       # Xem thông tin phòng
│   ├── ContractController.php   # Xem hợp đồng
│   └── IncidentController.php   # Xem sự cố & gửi báo cáo mới
│
├── Models/                      # "API Models" — gọi HTTP thay vì SQL
│   ├── Auth.php                 # Gọi API: login, changePassword, getNameByMaSV
│   ├── Student.php              # Gọi API: getProfile, updateProfile
│   ├── Room.php                 # Gọi API: getRoom
│   ├── Contract.php             # Gọi API: getContracts
│   └── Incident.php             # Gọi API: getIncidents, reportIncident
│
├── Views/                       # HTML views cho sinh viên
│   ├── Auth/
│   │   ├── login.php            # Trang đăng nhập
│   │   ├── dashboard.php        # Trang chủ sau đăng nhập
│   │   └── doimk.php            # Trang đổi mật khẩu
│   ├── Student/
│   │   └── list.php             # Xem thông tin cá nhân
│   ├── Room/
│   │   └── list.php             # Xem thông tin phòng
│   ├── Contract/
│   │   └── list.php             # Xem hợp đồng
│   └── Incident/
│       ├── list.php             # Xem lịch sử sự cố
│       └── baocao.php           # Form gửi sự cố mới
│
└── Public/                      # CSS, ảnh static
```

---

## 🔄 Luồng Dữ Liệu Chi Tiết

### Luồng 1: Admin đăng nhập (Web SSR)

```
Trình duyệt
  → POST /auth/login
  → Public/index.php  (URI = "auth/login")
  → AuthController@login()
  → AuthService@authenticate(username, password)
  → AuthRepository → SQL: SELECT * FROM taikhoan_admin
  → Lưu $_SESSION['username']
  → Redirect → /auth/dashboard → View HTML
```

### Luồng 2: Gọi RESTful API (JSON)

```
Client (Mobile / Postman / curl)
  → POST /api/auth/login  (Content-Type: application/json)
  → Public/index.php  (phát hiện URI bắt đầu "api/")
  → Load Routes/api.php → match pattern → AuthController@login()
  → AuthService@authenticate()
  → AuthRepository → SQL
  → jsonResponse({ success: true, token: "session_id" })
```

### Luồng 3: Sinh viên xem thông tin (QuanLyKTX_user → API)

```
Sinh viên (trình duyệt)
  → GET /webktx/QuanLyKTX_user/?url=student/list
  → index.php → UserRouter.php
  → StudentController@list()
  → Student model
      → file_get_contents("http://localhost/.../apiUser.php?action=student&masv=SV001")
  → QuanLyKTX_API/Routes/apiUser.php
      → UserController@getProfile()
      → StudentRepository@findById("SV001") → SQL
      → JSON: { status: "success", data: { hoten: "...", lop: "..." } }
  → Student model parse JSON → trả về array
  → StudentController truyền data vào View
  → Render HTML → trình duyệt sinh viên thấy thông tin
```

### Luồng 4: Sinh viên báo cáo sự cố

```
Sinh viên (submit form)
  → POST /webktx/QuanLyKTX_user/?url=incident/baocao
  → IncidentController@baocao()
  → Incident model
      → file_get_contents("...apiUser.php?action=reportIncident", POST data)
  → apiUser.php → UserController@createIncident()
  → IncidentRepository@insertRequest() → INSERT INTO suco
  → JSON: { status: "success", message: "Yêu cầu sự cố đã được gửi" }
  → IncidentController nhận kết quả → redirect / thông báo thành công
```

---

## 🔐 Cơ Chế Bảo Mật

### Phân quyền theo tầng

| Tầng | Cách bảo vệ |
|------|-------------|
| **Admin (API module)** | `requireAuth()` gọi trong `__construct()` của mỗi Controller |
| **Sinh viên (user module)** | Kiểm tra `$_SESSION['user_id']` trong mỗi action |
| **Session tách biệt** | Admin = `PHPSESSID`, Sinh viên = `USER_KTX_STUDENT` |

### Hàm `requireAuth()` — Core/Controller.php

```php
protected function requireAuth() {
    if (!isset($_SESSION['username'])) {
        if (isset($_GET['api'])) {
            // Gọi qua API → trả về 401 JSON
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
        }
        // Truy cập trình duyệt → redirect về login
        $this->redirect(BASE_URL . 'auth/login');
    }
}
```

### Chống SQL Injection

Tất cả SQL trong Repository đều dùng **Prepared Statements** với `bind_param()`:

```php
$stmt = $this->db->prepare("SELECT * FROM phong WHERE maphong = ?");
$stmt->bind_param('s', $id);
$stmt->execute();
```

---

## 🗄️ Kết Nối Database

**File:** `QuanLyKTX_API/Config/Database.php`

```php
// Cấu hình mặc định (Docker)
$host = 'db';             // service MySQL trong docker-compose
$user = 'root';
$pass = 'root_password';
$db   = 'quanlykytucxa';
$port = 3306;
```

> ⚠️ **Khi chạy XAMPP localhost:** đổi `$host` từ `'db'` thành `'localhost'` và `$pass` thành `''`.

**Pattern Singleton** — kết nối MySQL được tạo 1 lần duy nhất, dùng chung toàn ứng dụng.

---

## 📊 Tính Năng Chính

### Module Quản Trị (QuanLyKTX_API — Admin)

| Tính năng | Mô tả |
|-----------|-------|
| Đăng nhập / Đăng xuất | Xác thực admin bằng username/password |
| Quản lý Phòng | CRUD + xem danh sách sinh viên trong phòng |
| Quản lý Sinh Viên | CRUD sinh viên |
| Quản lý Hợp Đồng | CRUD hợp đồng thuê phòng |
| Quản lý Thanh Toán | CRUD lịch sử thanh toán |
| Quản lý Sự Cố | Xem và cập nhật trạng thái sự cố |
| Export / Import CSV | Xuất và nhập dữ liệu hàng loạt |
| Báo cáo / Thống kê | Tổng hợp qua UtilityController |

### Module Sinh Viên (QuanLyKTX_user)

| Tính năng | Mô tả |
|-----------|-------|
| Đăng nhập / Đăng xuất | Bằng mã sinh viên (masv) và mật khẩu |
| Dashboard | Trang chủ sau khi đăng nhập |
| Xem thông tin cá nhân | Tên, lớp, CCCD, SĐT, email, địa chỉ |
| Cập nhật thông tin | Sửa thông tin cá nhân qua API |
| Đổi mật khẩu | Xác minh mật khẩu cũ trước khi đổi |
| Xem thông tin phòng | Phòng đang ở, tòa, sức chứa, giá thuê |
| Xem hợp đồng | Danh sách hợp đồng đã ký |
| Xem sự cố | Lịch sử sự cố đã báo cáo |
| Gửi báo cáo sự cố | Form báo cáo: tên phòng, mô tả, ngày |

---

## ⚙️ Cơ Chế Routing

### QuanLyKTX_API

`Public/index.php` tự động phân tích URI:

```
/auth/dashboard
  → segments = ['auth', 'dashboard']
  → new AuthController() → dashboard()

/api/rooms/P101
  → phát hiện prefix 'api/'
  → tra bảng Routes/api.php
  → pattern "api/rooms/{id}" match → RoomController@edit('P101')
  → trả về JSON
```

### QuanLyKTX_user

`UserRouter.php` dùng query string `?url=`:

```
?url=student/list  → StudentController@list()
?url=incident      → IncidentController@index()
(trống)            → AuthController@index()  (trang login)
```

---

## 🌿 Nhánh Git

| Nhánh | Mục đích |
|-------|---------|
| `main` | Nhánh chính, production |
| `feature/add-api-user` | Thêm API endpoint cho sinh viên |
| `feature/api-auth` | Phát triển tính năng xác thực |
| `feature/api-incident-payment` | API sự cố & thanh toán |
| `feature/api-room-contract` | API phòng & hợp đồng |
| `feature/api-student` | API sinh viên |
| `feature/test` | Nhánh thử nghiệm |

---

## 🚀 Hướng Dẫn Cài Đặt

### Với XAMPP (localhost)

1. Clone project vào `C:/xampp/htdocs/webktx/`
2. Mở phpMyAdmin → tạo database `quanlykytucxa` → import SQL schema
3. Sửa `QuanLyKTX_API/Config/Database.php`:
   ```php
   $host = 'localhost';  // đổi từ 'db'
   $pass = '';           // mặc định XAMPP không có password
   ```
4. Truy cập:
   - **Admin:** `http://localhost/webktx/QuanLyKTX_API/Public/`
   - **Sinh viên:** `http://localhost/webktx/QuanLyKTX_user/`

### Với Docker

1. Đảm bảo có `docker-compose.yml` với service `db` (MySQL)
2. Chạy `docker-compose up -d`
3. `Database.php` đã cấu hình sẵn `$host = 'db'` — không cần đổi

---

## ⚠️ Điểm Cần Lưu Ý & Cải Thiện

| # | Vấn đề | Mức độ |
|---|--------|--------|
| 1 | **Debug log** còn để lại: `debug.log`, `debug_global.log` trong `Public/` — nên xóa trước production | Trung bình |
| 2 | **Hack REQUEST_METHOD**: dòng `$_SERVER['REQUEST_METHOD'] = 'POST'` trong `index.php` để bypass PUT/DELETE — nên refactor | Trung bình |
| 3 | **UserController** gọi trực tiếp Repository (bỏ qua Service layer) — chưa đồng nhất kiến trúc | Thấp |
| 4 | **Token API** chỉ là `session_id()` — không phải JWT, không bảo mật đủ cho môi trường public | Cao |
| 5 | **CORS** `Access-Control-Allow-Origin: *` trong `apiUser.php` — cần giới hạn origin khi production | Cao |
| 6 | **URL hardcode** `http://localhost/...` trong `Models/Auth.php` — nên đưa vào file config riêng | Thấp |

---

*Tài liệu được tổng hợp từ mã nguồn thực tế — cập nhật lần cuối: **2026-06-28***
