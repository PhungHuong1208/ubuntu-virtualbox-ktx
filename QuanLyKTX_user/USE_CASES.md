# Use Cases - QuanLyKTX MVC

## 1. Mô tả chung (Overview)

Hệ thống Quản lý Ký Túc Xá (QuanLyKTX MVC) cung cấp các chức năng quản lý:

- Sinh viên
- Phòng ký túc xá
- Hợp đồng thuê
- Thanh toán
- Sự cố (báo hỏng)

Kiến trúc: MVC (Model-View-Controller) + Repository Pattern

---

## 2. Use Case: Đăng nhập (Authentication)

### Tên: Đăng nhập vào hệ thống

- Actor: Nhân viên quản lý
- Goal: Truy cập vào dashboard quản lý
- Steps:
  1. Người dùng truy cập `http://localhost/HellomynameisPencilan/QuanLyKTX_MVC/`
  2. Hệ thống hiển thị trang `auth/login`
  3. Người dùng nhập `username` + `password`
  4. Form POST tới `auth/login`
  5. `AuthController::login()` kiểm tra thông tin:
     - Gọi `AuthRepository::authenticate()`
  6. Nếu sai: trả về login với lỗi
  7. Nếu đúng: set session rồi redirect `auth/dashboard`

- Output: giao diện dashboard hiển thị menu module

---

## 3. Use Case: Quản lý Sinh viên

### 3.1. Xem danh sách sinh viên

- URL: `/student`
- Controller: `StudentController::index()`
- Repository: `StudentRepository->getAll()` hay `->search(keyword)`
- View: `Modules/Student/Views/list.php`

### 3.2. Thêm sinh viên mới

- URL GET: `/student/create` (form)
- URL POST: `/student/store` (lưu)
- `StudentController::store()`
- Kiểm tra bắt buộc: `masv`, `hoten`, `lop`
- Nếu `masv` tồn tại: báo lỗi
- Nếu thành công: redirect `/student`

### 3.3. Cập nhật sinh viên

- URL GET: `/student/edit/{masv}`
- URL POST: `/student/update/{masv}`

### 3.4. Xóa sinh viên

- URL: `/student/delete/{masv}`

---

## 4. Use Case: Quản lý Phòng

### 4.1. Xem danh sách phòng

- URL: `/room`
- `RoomController::index()`

### 4.2. Thêm phòng

- URL: `/room/create`
- URL post: `/room/store`

### 4.3. Sửa phòng

- URL: `/room/edit/{maphong}`
- URL post: `/room/update/{maphong}`

### 4.4. Xóa phòng

- URL: `/room/delete/{maphong}`

---

## 5. Use Case: Quản lý Hợp đồng

- URL: `/contract`
- `ContractController::index` hiển thị hợp đồng hiện có
- Tạo hợp đồng: `/contract/create`, `/contract/store`
- Chỉnh sửa hợp đồng: `/contract/edit/{mahopdong}`, `/contract/update/{mahopdong}`
- Xóa hợp đồng: `/contract/delete/{mahopdong}`

---

## 6. Use Case: Quản lý thanh toán

- URL: `/payment`
- Thêm thu/chi: `/payment/create`, `/payment/store`
- Xóa: `/payment/delete/{mathanhtoan}`

---

## 7. Use Case: Báo cáo sự cố

- URL: `/incident`
- Tạo sự cố mới: `/incident/create`, `/incident/store`
- Xóa: `/incident/delete/{masuco}`

---

## 8. Kiến trúc module

| Module   | Controller         | Repository         | Views                    |
| -------- | ------------------ | ------------------ | ------------------------ |
| Auth     | AuthController     | AuthRepository     | login.php, dashboard.php |
| Student  | StudentController  | StudentRepository  | list.php, form.php       |
| Room     | RoomController     | RoomRepository     | list.php, form.php       |
| Contract | ContractController | ContractRepository | list.php, form.php       |
| Payment  | PaymentController  | PaymentRepository  | list.php, form.php       |
| Incident | IncidentController | IncidentRepository | list.php, form.php       |

---

## 9. Kịch bản hành vi (Happy path)

### Đăng nhập → quản lý student

1. `/auth/login` → AuthController logic
2. Redirect `/auth/dashboard` → dashboard
3. Thực hiện `/student` → StudentController->index
4. Thêm `/student/create`, `/student/store`

---

## 10. Chú ý về routing

- `router.php` dùng URL path tách thành segment
- module/action/params
- `BASE_URL`: `http://localhost/HellomynameisPencilan/QuanLyKTX_MVC/`
- `.htaccess`: rewrite tất cả request về `index.php`

---

## 11. Kết luận

File này là tài liệu use case cho demo môn kiến trúc phần mềm; module tương ứng đã build và chi tiết. Vui lòng dùng `QUICKSTART.md` cộng `.htaccess` để chạy chính xác.
