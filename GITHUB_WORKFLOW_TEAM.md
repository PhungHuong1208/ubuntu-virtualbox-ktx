# Quy Trình Làm Việc Nhóm Trên GitHub (Dự Án Quản Lý Ký Túc Xá)

Tài liệu này hướng dẫn chi tiết quy trình quản lý mã nguồn (Git Flow) và phân chia công việc cho team **5 thành viên** trên dự án Web Quản Lý Ký Túc Xá (gồm `QuanLyKTX_API` và `QuanLyKTX_user`).

---

## 1. Các Nhánh (Branches) Cơ Bản Trên GitHub

Tuyệt đối KHÔNG ai được code và đẩy trực tiếp lên nhánh `main`.

- **`main` (hoặc `master`)**: Nhánh Production. Chứa mã nguồn hoàn chỉnh, không lỗi, dùng để báo cáo hoặc deploy chạy thực tế.
- **`develop` (hoặc `dev`)**: Nhánh Development. Nơi gom code của 5 thành viên lại với nhau. Mọi người đều phải cập nhật code mới nhất từ nhánh này về máy.
- **`feature/[tên-tính-năng]`**: Nhánh cá nhân để làm một tính năng mới. Tách ra từ `develop` và merge lại vào `develop`.
- **`bugfix/[tên-lỗi]`**: Nhánh cá nhân để sửa lỗi trong quá trình test. Tách ra từ `develop`.

---

## 2. Quy Trình 5 Bước Làm Việc Hằng Ngày

Bất cứ khi nào bạn được giao một Task (ví dụ: làm giao diện danh sách phòng), hãy làm đúng theo 5 bước sau:

**Bước 1: Chuyển sang nhánh `develop` và cập nhật code mới nhất từ team.**

```bash
git checkout develop
git pull origin develop
```

**Bước 2: Tạo nhánh `feature` riêng cho công việc của bạn.**

```bash
git checkout -b feature/ui-danh-sach-phong
```

**Bước 3: Code và Lưu lại (Commit).** _(Chỉ sửa các file được phân công)_

```bash
git add .
git commit -m "feat(room): Hoàn thành thiết kế giao diện danh sách phòng"
```

**Bước 4: Đẩy code lên GitHub.**

```bash
git push origin feature/ui-danh-sach-phong
```

**Bước 5: Tạo Pull Request (PR).**
Lên trang web GitHub, bấm tạo Pull Request từ nhánh của bạn vào nhánh `develop`. Chờ Leader review code và bấm nút **Merge**.

---

## 3. Phân Chia Công Việc Cụ Thể Cho 5 Thành Viên

Dưới đây là chi tiết phân công các thư mục/file cụ thể để **tránh bị Conflict** (xung đột code).

### 🧑‍💻 Người 1: Backend API (Auth, User & Student)

_Chịu trách nhiệm API đăng nhập, phân quyền, quản lý thông tin người dùng và sinh viên._

## Vũ

- **Nhánh thường dùng:** `feature/api-auth`, `feature/api-student`
- **Khu vực code:** Thư mục `QuanLyKTX_API/`
  - `Controllers/AuthController.php`
  - `Controllers/UserController.php`
  - `Controllers/StudentController.php`
  - `Models/AuthRepository.php`
  - `Models/UserRepository.php`
  - `Models/StudentRepository.php`

### 🧑‍💻 Người 2: Backend API (Nghiệp vụ: Room, Contract, Incident, Payment)

_Chịu trách nhiệm API quản lý phòng, hợp đồng thuê, sự cố báo hỏng và thanh toán._

## Dũng

- **Nhánh thường dùng:** `feature/api-room-contract`, `feature/api-incident-payment`
- **Khu vực code:** Thư mục `QuanLyKTX_API/`
  - `Controllers/RoomController.php`
  - `Controllers/ContractController.fephp`
  - `Controllers/IncidentController.php`
  - `Controllers/PaymentController.php`
  - `Models/RoomRepository.php`
  - `Models/ContractRepository.php`
  - `Models/IncidentRepository.php`
  - `Models/PaymentRepository.php`

### 🎨 Người 3: Frontend (Giao diện Auth, Student & Incident)

_Chịu trách nhiệm làm giao diện và gọi API từ Người 1 và Người 2._

# Hương

- **Nhánh thường dùng:** `feature/ui-auth-student`, `feature/ui-incident`
- **Khu vực code:** Thư mục `QuanLyKTX_user/`
  - `Controllers/AuthController.php`
  - `Controllers/StudentController.php`
  - `Controllers/IncidentController.php`
  - Thư mục `Views/auth/*`
  - Thư mục `Views/student/*`
  - Thư mục `Views/incident/*`

### 🎨 Người 4: Frontend (Giao diện Room, Contract & Payment)

# Đạt

_Chịu trách nhiệm làm giao diện và gọi API danh sách phòng, hợp đồng, thanh toán._

- **Nhánh thường dùng:** `feature/ui-room-contract`, `feature/ui-payment`
- **Khu vực code:** Thư mục `QuanLyKTX_user/`
  - `Controllers/RoomController.php`
  - `Controllers/ContractController.php`
  - Thư mục `Views/room/*`
  - Thư mục `Views/contract/*`

### 🦸‍♂️ Người 5: Team Leader / Kiến trúc sư hệ thống

_Chịu trách nhiệm kết nối, cấu trúc Routing và duyệt code (Code Review)._

# Lan

- **Nhánh thường dùng:** `feature/core-setup`, `feature/routing`
- **Khu vực code:**
  - **Bên `QuanLyKTX_API/`:**
    - `Core/*` (Cấu trúc DB, Controller gốc)
    - `Routes/*` (Viết các endpoint định tuyến API)
    - `Config/*`
  - **Bên `QuanLyKTX_user/`:**
    - `Core/*`
    - `UserRouter.php` (Phân luồng URL trình duyệt)
- **Nhiệm vụ Đặc Biệt:**
  - Người này sẽ **KHÔNG** push thẳng lên nhánh develop mà làm nhiệm vụ review code.
  - Khi 4 người kia tạo PR, Leader vào đọc code. Nếu ổn thì nhấn **Merge Pull Request**. Nếu không ổn, yêu cầu sửa lại.
  - Người này chịu trách nhiệm hỗ trợ xử lý khi xảy ra Conflict.

---

## ⚠️ Nguyên Tắc Quan Trọng Khi Code Nhóm

1.  **Chỉ sửa file của mình:** Mỗi người chỉ mở và sửa các file thuộc phạm vi phân công của mình. Tuyệt đối không mở file của người khác ra sửa.
2.  **Đợi API:** Khi Frontend (Người 3, 4) cần dùng API, phải đợi Backend (Người 1, 2) code xong, Leader merge lên nhánh `develop` thì Frontend mới chạy lệnh `git pull origin develop` để lấy code mới về và kết nối giao diện.
3.  **Báo cáo Leader khi sửa file chung:** Nếu bắt buộc phải sửa chung một file như `Routes.php`, hãy báo ngay cho Leader (Người 5) để quản lý, tránh việc code đè lên nhau.
