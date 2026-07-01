# 📋 README - QuanLyKTX MVC

## 🎯 Phiên Bản MVC (Version 2.0)

Đây là phiên bản **tái thiết kế** của hệ thống Quản Lý Ký Túc Xá theo mô hình **MVC (Model-View-Controller)** với **Repository Pattern**.

### So Sánh Phiên Bản

| Tiêu Chí        | Phiên Bản Cũ             | Phiên Bản MVC (Mới)   |
| --------------- | ------------------------ | --------------------- |
| **Kiến Trúc**   | Procedural, lẫn lộn      | MVC rõ ràng           |
| **Tệ Tổ Chức**  | File riêng mỗi chức năng | Modules theo features |
| **Data Access** | Direct trong views       | Repository Pattern    |
| **Routing**     | Iframe, hard-coded       | Router tập trung      |
| **Tái Sử Dụng** | Khó khăn                 | Dễ dàng               |
| **Bảo Trì**     | Phức tạp                 | Đơn giản              |
| **Ghi Chú**     | Cho học tập              | Học tập + kiến trúc   |

---

## 🏗️ Kiến Trúc

### MVC + Repository Pattern

```
Layer 1: View (Presentation)
   ↓
Layer 2: Controller (Logic)
   ↓
Layer 3: Repository (Data Access)
   ↓
Database
```

### Cấu Trúc By Feature

```
Modules/
├── Auth/           ← Xác thực
├── Student/        ← Sinh viên
├── Room/           ← Phòng
├── Contract/       ← Hợp đồng
├── Payment/        ← Thanh toán
└── Incident/       ← Sự cố
```

Mỗi module có:

- **Controllers/** - Xử lý request
- **Models/** - Business logic
- **Repositories/** - Data access
- **Views/** - Giao diện

---

## 📦 Cài Đặt

### 1. Copy tệp

```bash
C:\xampp\htdocs\HellomynameisPencilan\QuanLyKTX_MVC\
```

### 2. Tạo Database

Xem file `QUICKSTART.md` phần "Tạo Database"

### 3. Truy cập

```
http://localhost/HellomynameisPencilan/QuanLyKTX_MVC/
```

### 4. Đăng nhập

```
Username: admin
Password: admin123
```

---

## ✨ Tính Năng

### ✅ Hiện Có

- Xác thực người dùng (login/logout)
- Quản lý sinh viên (CRUD + search)
- Quản lý phòng (CRUD)
- Quản lý hợp đồng (list + CRUD)
- Quản lý thanh toán (list)
- Quản lý sự cố (list)
- Dashboard tập trung
- Responsive design

### 🔄 Ví Dụ Lập Trình

**Student Module** được thiết kế đầy đủ nhất:

- List → Search
- Create + Store
- Edit + Update
- Delete
- Validation

**Sử dụng làm template** cho các module khác.

---

## 📚 Tài Liệu

| Tệp               | Nội Dung                                 |
| ----------------- | ---------------------------------------- |
| **MVC_GUIDE.md**  | Hướng dẫn chi tiết MVC + design patterns |
| **QUICKSTART.md** | Hướng dẫn bắt đầu nhanh                  |
| **README.md**     | Tệp này                                  |

---

## 🎓 Mục Đích Học Tập

Dự án này được thiết kế để dạy các khái niệm:

1. **MVC Architecture** - Tách biệt logic
2. **Design Patterns**:
   - Repository Pattern (DAL)
   - Singleton Pattern (Database)
   - Base/Abstract Classes
   - Dependency Injection

3. **Best Practices**:
   - Separation of Concerns
   - DRY (Don't Repeat Yourself)
   - SOLID Principles

4. **Web Development**:
   - Routing
   - Session Management
   - Form Handling & Validation
   - Database Operations

---

## 🚀 Cách Thêm Module Mới

### Bước 1: Tạo thư mục

```bash
Modules/NewModule/
├── Controllers/
├── Models/
├── Repositories/
└── Views/
```

### Bước 2: Tạo Repository

```php
class NewModuleRepository extends Repository {
    protected $table = 'table_name';
}
```

### Bước 3: Tạo Controller

```php
class NewModuleController extends Controller {
    public function index() { }
    public function create() { }
    public function store() { }
}
```

### Bước 4: Tạo Views

- list.php
- form.php

### Bước 5: Thêm Menu

Edit `Modules/Auth/Views/dashboard.php`

**Done!** Router sẽ tự nhận diện module mới.

---

## 🔗 URL Patterns

### Format: `/module/action/param1/param2`

### Student Module

```
/student              → StudentController::index()
/student/create       → StudentController::create()
/student/store        → StudentController::store()
/student/edit/SV001   → StudentController::edit('SV001')
/student/update/SV001 → StudentController::update('SV001')
/student/delete/SV001 → StudentController::delete('SV001')
```

### Room, Contract, Payment, Incident (tương tự)

---

## 📊 Thống Kê Dự Án

| Loại          | Số Lượng |
| ------------- | -------- |
| Files         | 30+      |
| Lines of Code | 2000+    |
| Modules       | 6        |
| Controllers   | 6        |
| Repositories  | 6        |
| Views         | 15+      |
| Classes       | 12+      |

---

## 🎯 So Sánh Code

### ❌ Cách Cũ (Procedural)

```php
// File: QlySinhVien/InfSinhvien.php
<?php
$con = new mysqli("localhost", "root", "", "quanlykytucxa");
$result = $con->query("SELECT * FROM sinhvien");
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>" . $row['masv'] . "</td>...</tr>";
}
?>
```

**Vấn đề:**

- DB logic trong view
- Không tái sử dụng
- Khó bảo trì

---

### ✅ Cách Mới (MVC)

```php
// File: Modules/Student/Controllers/StudentController.php
class StudentController extends Controller {
    public function index() {
        $students = $this->studentRepo->getAll();
        $this->view('list', ['students' => $students]);
    }
}

// File: Modules/Student/Views/list.php
<?php foreach ($students as $s): ?>
    <tr><td><?= htmlspecialchars($s['masv']) ?></td>...</tr>
<?php endforeach; ?>
```

**Lợi Ích:**

- Logic tách biệt
- Dễ tái sử dụng
- Dễ bảo trì & mở rộng

---

## 🔐 Security Notes

⚠️ **Mục Đích Giáo Dục:**

Dự án này **KHÔNG sử dụng**:

- Password hashing (để đơn giản hóa)
- CSRF tokens (có thể thêm sau)
- XSS protection (có thể thêm sau)

**Để production, bạn cần:**

- `password_hash()` & `password_verify()`
- CSRF protection tokenss
- Input sanitization (`htmlspecialchars()`)
- Prepared statements ✓ (có sẵn)

---

## 📞 Lấy Thông Tin Input

### Từ GET/POST

```php
$value = $this->getInput('key', 'default');
```

### Từ Session

```php
$userId = $this->getSession('user_id');
$this->setSession('key', 'value');
```

### Redirect

```php
$this->redirect(BASE_URL . 'student');
```

### Render View

```php
$this->view('list', ['data' => $data]);
```

---

## 🧪 Testing

### Test Login

```
URL: http://localhost/.../
Username: admin
Password: admin123
```

### Test Student CRUD

```
1. List: http://localhost/.../student
2. Add: http://localhost/.../student/create
3. Edit: http://localhost/.../student/edit/SV001
4. Delete: http://localhost/.../student/delete/SV001
```

---

## 🎓 Học Từ Dự Án

### Core Concepts

✓ MVC Pattern
✓ Repository Pattern
✓ Layer Architecture
✓ Routing System
✓ Base Classes & Inheritance
✓ Dependency Injection

### Web Technologies

✓ PHP OOP
✓ MySQL & Prepared Statements
✓ HTML/CSS
✓ Session Management
✓ URL Routing
✓ Form Handling

### Software Engineering

✓ Separation of Concerns
✓ Code Reusability
✓ Maintainability
✓ Scalability
✓ Design Patterns

---

## 🎯 Lộ Trình Cải Thiện

### Level 1: Hiểu MVC

- [x] Phân tích code hiện có
- [x] Hiểu flow request
- [x] Hiểu Repository Pattern

### Level 2: Sử Dụng MVC

- [ ] Sửa lỗi & thêm tính năng
- [ ] Thêm module mới
- [ ] Tối ưu code

### Level 3: Nâng Cao

- [ ] Thêm validation layer
- [ ] Thêm authentication security
- [ ] Thêm caching
- [ ] Migrate sang framework (Laravel, Symfony)

---

## 🆘 Troubleshooting

| Vấn Đề              | Giải Pháp                                                             |
| ------------------- | --------------------------------------------------------------------- |
| Login không được    | Kiểm tra database taikhoan, username = 'admin', password = 'admin123' |
| Danh sách trống     | Kiểm tra database có dữ liệu không, insert dữ liệu mẫu                |
| View không tìm thấy | Kiểm tra tên file đúng, đường dẫn đúng                                |
| Controller lỗi      | Kiểm tra class name, extends Controller                               |
| Database error      | Kiểm tra Config/Database.php credentials                              |

---

## 📌 Ghi Chú Quan Trọng

1. **Base Path**: Tất cả URL sử dụng `BASE_URL` constant
2. **File Paths**: Tất cả sử dụng `__DIR__` để đảm bảo path đúng
3. **Security**: Input sử dụng `htmlspecialchars()` khi output
4. **Database**: Tất cả queries sử dụng prepared statements
5. **Session**: Khởi tạo tại index.php, check ở controller

---

## 👨‍💻 Author

**GitHub Copilot**  
Date: March 2026

---

## 📄 License

Educational Purpose - Free to use and modify

---

## 🙏 Credits

Dự án này là phiên bản MVC của hệ thống quản lý ký túc xá do nhóm sinh viên tạo ra cho môn học.

**Mục đích**: Cung cấp ví dụ thực tế về kiến trúc phần mềm cho các sinh viên học lập trình web.

---

**Hãy bắt đầu với QUICKSTART.md! 🚀**
