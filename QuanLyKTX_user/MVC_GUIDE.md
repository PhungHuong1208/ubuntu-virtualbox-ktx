# 📚 Hướng Dẫn Kiến Trúc MVC - QuanLyKTX

## 📖 Tổng Quan

Dự án **QuanLyKTX_MVC** là phiên bản tái cấu trúc của hệ thống quản lý ký túc xá theo mô hình **MVC (Model-View-Controller)** với **Repository Pattern** cho Data Access Layer.

### Mục Đích:

- 🎓 Cung cấp ví dụ thực tế về kiến trúc phần mềm
- 🔄 Tách biệt các lớp logic (Model, View, Controller)
- 🛡️ Dễ bảo trì, mở rộng, và tái sử dụng code
- 📚 Là tài liệu học tập cho môn "Kiến Trúc Phần Mềm"

---

## 🏗️ Cấu Trúc Thư Mục

```
QuanLyKTX_MVC/
│
├── Config/
│   └── Database.php              # Cấu hình và kết nối cơ sở dữ liệu
│
├── Core/
│   ├── Controller.php            # Base Controller - kế thừa từ đây
│   ├── Model.php                 # Base Model - cung cấp các phương thức DB
│   └── Repository.php            # Base Repository - Data Access Layer
│
├── Modules/                      # Các chức năng chính
│   ├── Auth/
│   │   ├── Controllers/
│   │   │   └── AuthController.php
│   │   ├── Models/
│   │   │   ├── AuthModel.php
│   │   │   └── AuthRepository.php
│   │   └── Views/
│   │       ├── login.php
│   │       └── dashboard.php
│   │
│   ├── Student/
│   │   ├── Controllers/
│   │   │   └── StudentController.php
│   │   ├── Models/
│   │   │   ├── StudentModel.php
│   │   │   └── StudentRepository.php
│   │   └── Views/
│   │       ├── list.php
│   │       └── form.php
│   │
│   ├── Room/                     # Tương tự Student
│   ├── Contract/                 # Tương tự Student
│   ├── Payment/                  # Tương tự Student
│   └── Incident/                 # Tương tự Student
│
├── Public/
│   ├── css/
│   │   └── style.css
│   └── js/
│
├── index.php                     # Entry point chính
├── router.php                    # Router đơn giản
└── README.md
```

---

## 🎯 Các Thành Phần Chính

### 1️⃣ **Config/Database.php** - Quản Lý Kết Nối DB

```php
// Singleton pattern - chỉ có một instance kết nối
$conn = Database::getInstance();
```

**Tính năng:**

- Kết nối đến MySQL/MySQLi
- Singleton pattern - reuse cùng một kết nối
- Thiết lập charset UTF-8

---

### 2️⃣ **Core/Controller.php** - Base Controller

Tất cả các Controller khác kế thừa từ `Controller`:

```php
class StudentController extends Controller {
    protected function view($view, $data = []) { }
    protected function redirect($url) { }
    protected function getInput($key, $default = null) { }
    protected function getSession($key, $default = null) { }
}
```

**Phương thức chính:**

- `view()` - Render HTML view
- `redirect()` - Chuyển hướng URL
- `getInput()` - Lấy dữ liệu từ GET/POST
- `isPost() / isGet()` - Kiểm tra loại request
- `json()` - Gửi JSON response
- Session management methods

---

### 3️⃣ **Core/Model.php** - Base Model

Cung cấp các phương thức truy cập dữ liệu (cấp độ thấp):

```php
class StudentModel extends Model {
    protected function fetchAll($sql, $params, $types) { }
    protected function fetchOne($sql, $params, $types) { }
    protected function execute($sql, $params, $types) { }
}
```

**Đặc điểm:**

- Prepared statements - chống SQL injection
- Tách từng truy vấn thành phương thức riêng
- Sử dụng mysqli binding params

---

### 4️⃣ **Core/Repository.php** - Data Access Layer

Repository Pattern - tách biệt truy cập dữ liệu:

```php
class StudentRepository extends Repository {
    public function getAll() { }           // Lấy tất cả
    public function findByMaSV($masv) { }  // Tìm theo ID
    public function search($keyword) { }   // Search
    public function create($data) { }      // Thêm mới
    public function update($id, $data) { } // Cập nhật
    public function delete($id) { }        // Xóa
}
```

**Lợi ích:**

- Tập trung logic truy cập dữ liệu
- Dễ dàng thay đổi cách lưu trữ (DB → Cache, File, API)
- Tái sử dụng dễ dàng

---

### 5️⃣ **Modules/[Feature]/Controllers/** - Business Logic

Controller xử lý logic nghiệp vụ:

```php
class StudentController extends Controller {
    private $studentModel;
    private $studentRepo;

    public function __construct() {
        $this->studentModel = new StudentModel();
        $this->studentRepo = new StudentRepository();
    }

    public function index() {
        // Lấy dữ liệu từ repository
        $students = $this->studentRepo->getAll();
        // Render view
        $this->view('list', ['students' => $students]);
    }

    public function store() {
        // Validate input
        if (empty($masv)) {
            $_SESSION['error'] = 'MSSV không được để trống!';
            return;
        }
        // Kiểm tra duplicate
        if ($this->studentRepo->existsMaSV($masv)) {
            $_SESSION['error'] = 'MSSV đã tồn tại!';
            return;
        }
        // Lưu vào DB
        $this->studentRepo->create($data);
        // Redirect
        $this->redirect(BASE_URL . 'student');
    }
}
```

---

### 6️⃣ **Modules/[Feature]/Views/** - Giao Diện

View chỉ chứa HTML & minimal PHP:

```php
<!-- Modules/Student/Views/list.php -->
<table>
    <thead>
        <tr>
            <th>MSSV</th>
            <th>Họ Tên</th>
            <th>Hành Động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= htmlspecialchars($student['masv']) ?></td>
                <td><?= htmlspecialchars($student['hoten']) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>student/edit/<?= $student['masv'] ?>">Sửa</a>
                    <a href="<?= BASE_URL ?>student/delete/<?= $student['masv'] ?>">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

---

### 7️⃣ **router.php** - Routing Tập Trung

Một router đơn giản để chuyển hướng request:

```
URL: /student/index → StudentController::index()
URL: /student/create → StudentController::create()
URL: /student/store → StudentController::store()
URL: /student/edit/SV001 → StudentController::edit('SV001')
URL: /student/update/SV001 → StudentController::update('SV001')
URL: /student/delete/SV001 → StudentController::delete('SV001')
```

Format: `/module/action/param1/param2...`

---

## 🔄 Flow Xử Lý Request

### Ví dụ: Hiển thị danh sách sinh viên

```
1. User truy cập: http://localhost/QuanLyKTX_MVC/student

2. index.php
   ↓
3. router.php phân tích URL
   ├─ Module: 'student'
   ├─ Action: 'index' (mặc định)
   └─ Params: []

4. Load StudentController
   ↓
5. Gọi $controller->index()
   ↓
6. StudentController::index()
   ├─ Kiểm tra login
   ├─ Lấy keyword từ GET
   ├─ Gọi $studentRepo->search($keyword)
   ├─ $studentRepo truy vấn DB
   └─ Return data

7. Controller gọi $this->view('list', $data)
   ↓
8. View render HTML
   ├─ Loop through $students
   └─ Output table

9. Browser hiển thị kết quả
```

---

## 🏃 URL Patterns

### Student Module

| Action    | URL                     | Method   |
| --------- | ----------------------- | -------- |
| Danh sách | `/student`              | GET      |
| Form thêm | `/student/create`       | GET      |
| Lưu thêm  | `/student/store`        | POST     |
| Form sửa  | `/student/edit/SV001`   | GET      |
| Lưu sửa   | `/student/update/SV001` | POST     |
| Xóa       | `/student/delete/SV001` | GET/POST |

### Room Module (tương tự)

| Action    | URL               |
| --------- | ----------------- |
| Danh sách | `/room`           |
| Thêm      | `/room/create`    |
| Sửa       | `/room/edit/P101` |

### Các Module Khác

- **Contract**: `/contract/...`
- **Payment**: `/payment/...`
- **Incident**: `/incident/...`

---

## 🔍 So Sánh: Cũ vs Mới

### ❌ Cách Cũ (Procedural - Trực Tiếp)

```php
// Tệp: InfSinhvien.php
<?php
$con = new mysqli("localhost", "root", "", "quanlykytucxa");
$result = $con->query("SELECT * FROM sinhvien");

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['masv'] . "</td>";
    echo "<td>" . $row['hoten'] . "</td>";
    echo "</tr>";
}
?>
```

**Vấn đề:**

- DB logic lẫn trong view file
- Không tái sử dụng được
- Khó bảo trì khi cần thay đổi
- SQL injection risk (nếu có filter)

---

### ✅ Cách Mới (MVC + Repository)

```php
// Controller: Modules/Student/Controllers/StudentController.php
class StudentController extends Controller {
    private $studentRepo;

    public function __construct() {
        $this->studentRepo = new StudentRepository();
    }

    public function index() {
        $students = $this->studentRepo->getAll(); // ← Tách logic!
        $this->view('list', ['students' => $students]);
    }
}

// Repository: Modules/Student/Repositories/StudentRepository.php
class StudentRepository extends Repository {
    public function getAll() {
        $sql = "SELECT * FROM sinhvien ORDER BY hoten";
        return $this->fetchAll($sql);
    }
    // Có thể tái sử dụng từ các controller khác!
}

// View: Modules/Student/Views/list.php
<table>
    <?php foreach ($students as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['masv']) ?></td>
            <td><?= htmlspecialchars($row['hoten']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
```

**Lợi ích:**

- Logic tách biệt rõ ràng
- Dễ test, debug, bảo trì
- Tái sử dụng được
- Dễ mở rộng (thêm module mới)

---

## 📝 Hướng Dẫn Thêm Module Mới

### Bước 1: Tạo cấu trúc thư mục

```
Modules/Product/
├── Controllers/
│   └── ProductController.php
├── Models/
│   ├── ProductModel.php
│   └── ProductRepository.php
└── Views/
    ├── list.php
    └── form.php
```

### Bước 2: Tạo Repository

```php
// ProductRepository.php
class ProductRepository extends Repository {
    protected $table = 'sanpham';

    // Có sẵn từ base class:
    // - getAll()
    // - getById()
    // - count()
    // - create(), update(), delete()
}
```

### Bước 3: Tạo Controller

```php
// ProductController.php
class ProductController extends Controller {
    private $productRepo;

    public function __construct() {
        $this->ensureLoggedIn();
        $this->productRepo = new ProductRepository();
    }

    public function index() {
        $products = $this->productRepo->getAll();
        $this->view('list', [
            'title' => 'Danh Sách Sản Phẩm',
            'products' => $products
        ]);
    }

    // ... thêm các action khác
}
```

### Bước 4: Tạo View

```php
// list.php
<table>
    <tr>
        <th>ID</th>
        <th>Tên Sản Phẩm</th>
        <th>Giá</th>
    </tr>
    <?php foreach ($products as $product): ?>
        <tr>
            <td><?= $product['id'] ?></td>
            <td><?= htmlspecialchars($product['name']) ?></td>
            <td><?= number_format($product['price']) ?> VND</td>
        </tr>
    <?php endforeach; ?>
</table>
```

### Bước 5: Thêm link vào Menu

Chỉnh sửa `Modules/Auth/Views/dashboard.php` để thêm link:

```html
<li><a href="<?= BASE_URL ?>product">📦 Sản Phẩm</a></li>
```

**Done! 🎉** Router sẽ tự động nhận diện module mới.

---

## 🎓 Các Mẫu Thiết Kế (Design Patterns) Được Sử Dụng

### 1. MVC Pattern

- **Model**: StudentModel, StudentRepository
- **View**: list.php, form.php
- **Controller**: StudentController

### 2. Repository Pattern

- Tách business logic khỏi data access
- Dễ thay đổi source dữ liệu (DB → Cache → API)

### 3. Singleton Pattern

- Database::getInstance() - chỉ một instance kết nối
- Tiết kiệm resources

### 4. Base/Abstract Classes

- `Controller` base class - cung cấp phương thức chung
- `Model` base class - phương thức DB chung
- `Repository` base class - CRUD operation chung

### 5. Dependency Injection

```php
public function __construct() {
    $this->studentRepo = new StudentRepository(); // ← Inject dependency
}
```

---

## 🚀 Cách Chạy Ứng Dụng

### Yêu Cầu:

- PHP 7.0+
- MySQL 5.5+
- XAMPP hoặc server tương tự

### Setup:

1. **Copy dự án vào htdocs:**

```
C:\xampp\htdocs\HellomynameisPencilan\QuanLyKTX_MVC\
```

2. **Tạo database:**

```sql
CREATE DATABASE quanlykytucxa;
USE quanlykytucxa;

-- Copy các bảng từ database cũ
-- (hoặc import từ file SQL)
```

3. **Truy cập:**

```
http://localhost/HellomynameisPencilan/QuanLyKTX_MVC/
```

4. **Đăng nhập:**

- Username: `admin`
- Password: `admin123`

---

## 📚 Tài Nguyên Học Tập

### Khái Niệm

- **MVC Pattern**: Separation of Concerns
- **Repository Pattern**: Data Access Abstraction
- **DRY (Don't Repeat Yourself)**: Tái sử dụng code
- **SOLID Principles**: Single Responsibility, etc.

### Cách Mở Rộng

1. Có thể thêm Service Layer (giữa Controller & Repository)
2. Có thể thêm Validation Layer
3. Có thể thêm Cache Layer
4. Có thể migrate sang OOP framework (Laravel, Symfony)

---

## 🎯 Lưu Ý Quan Trọng

✅ **Tốt:**

- Tách biệt logic rõ ràng
- Dễ test từng lớp riêng
- Dễ bàn giao code cho người khác
- Dễ mở rộng tính năng mới

⚠️ **Cần Cải Thiện:**

- Thêm bảo mật (password hashing, CSRF, XSS protection)
- Thêm validation layer
- Thêm caching
- Thêm logging
- Viết unit tests

---

## 📞 Cấu Trúc Tệp Tham Khảo

```
Tệp gốc cũ          →  Tệp mới (MVC)
QlySinhVien/        →  Modules/Student/
├─ InfSinhvien.php  →  Controllers/StudentController.php (index action)
├─ InsSinhvien.php  →  Controllers/StudentController.php (create/store action)
├─ UpdSinhvien.php  →  Controllers/StudentController.php (edit/update action)
└─ DeleteSV.php     →  Controllers/StudentController.php (delete action)
                    →  Models/StudentModel.php (business logic)
                    →  Repositories/StudentRepository.php (data access)
                    →  Views/list.php, form.php (presentation)
QlyPhong/           →  Modules/Room/
QlyHopDong/         →  Modules/Contract/
QlyThanhToan/       →  Modules/Payment/
Qlysuco/            →  Modules/Incident/
```

---

## 🎓 Tóm Tắt

| Aspect                   | Lợi Ích                                 |
| ------------------------ | --------------------------------------- |
| **Kiến trúc MVC**        | Tách biệt logic, dễ bảo trì             |
| **Repository Pattern**   | Tách DAL, dễ thay đổi source dữ liệu    |
| **Base Classes**         | Tái sử dụng code, giảm duplicate        |
| **Routing Tập Trung**    | Support clean URLs, dễ quản lý          |
| **By Feature Structure** | Dễ tìm file liên quan đến một tính năng |

---

**Viết bởi:** GitHub Copilot  
**Ngày:** 2026  
**Cho:** Môn Học Kiến Trúc Phần Mềm
