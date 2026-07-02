# Danh Sách 5 Kịch Bản Kiểm Thử Bảo Mật (Định Dạng Từng Bước Chi Tiết)

## Ứng Dụng Quản Lý Ký Túc Xá `webktx` Sử Dụng OWASP ZAP

Dưới đây là 5 kịch bản kiểm thử bảo mật được viết lại hoàn toàn theo định dạng kịch bản chạy thực tế (tương tự như mẫu thử thách Juice-shop), áp dụng trực tiếp vào cấu trúc và các chức năng của ứng dụng ký túc xá `webktx` của bạn.

---

### Kịch Bản 1: Lỗi Phân Quyền IDOR Xem Hồ Sơ Sinh Viên Khác

**Nhiệm vụ:** Tìm cách xem thông tin cá nhân chi tiết (Họ tên, CCCD, Số điện thoại, Email, Địa chỉ) của sinh viên `SV002` trong khi bạn chỉ được cấp tài khoản của sinh viên `SV001`.

- **Các bước thực hiện:**
  - Đầu tiên, ta đăng nhập vào Cổng sinh viên (`QuanLyKTX_user`) bằng tài khoản của sinh viên `SV001`.
  - Nhấn vào mục **Chỉnh Sửa Hồ Sơ**. Giao diện sẽ hiển thị các thông tin cá nhân của `SV001`.
  - Trong giao diện của OWASP ZAP, tìm đến tab **History** ở phía dưới, tìm request API vừa được gọi:
    `GET http://localhost/webktx/QuanLyKTX_API/Routes/apiUser.php?action=student&masv=SV001`
  - Nhấp chuột phải vào request này trong ZAP -> Chọn **Open/Resend with Request Editor...**
  - Tại giao diện Request Editor, tìm đến dòng URL và sửa tham số `masv=SV001` thành `masv=SV002`.
  - Nhấn nút **Send** ở góc trên bên trái của cửa sổ Request Editor để gửi yêu cầu đi.
- **Kết quả:**
  - Tại tab **Response** trong ZAP, ta nhận được dữ liệu JSON chứa toàn bộ thông tin cá nhân nhạy cảm của sinh viên `SV002` (Họ tên, CCCD, SĐT, Email, Địa chỉ) kèm mã phản hồi `200 OK`.
  - Điều này chứng tỏ ứng dụng bị lỗi IDOR nghiêm trọng do API backend không kiểm tra xem session hiện tại có quyền truy cập mã sinh viên gửi lên hay không.

---

### Kịch Bản 2: Lỗi Bypass Xác Thực (Broken Authentication) Gọi API Trực Tiếp

**Nhiệm vụ:** Tìm cách lấy thông tin phòng ở và hợp đồng của một sinh viên bất kỳ mà không cần phải thực hiện đăng nhập vào hệ thống.

- **Các bước thực hiện:**
  - Đầu tiên, ta mở trình duyệt và truy cập trang xem thông tin phòng của sinh viên ở địa chỉ `/QuanLyKTX_user/room/index` (hoặc `/QuanLyKTX_user/contract/index`).
  - Trình duyệt sẽ tự động đá ta về trang đăng nhập `/auth` do Frontend có bộ lọc Session kiểm tra đăng nhập. Tuy nhiên, API backend mới là nơi chứa dữ liệu thực sự.
  - Ta mở ZAP và truy cập thẳng vào API endpoint của phòng ở bằng Request Editor:
    `GET http://localhost/webktx/QuanLyKTX_API/Routes/apiUser.php?action=room&masv=SV001`
  - Trong phần cấu hình **Header** của request trong Request Editor, ta **xóa bỏ hoàn toàn** dòng cookie session nếu có (ví dụ: `Cookie: USER_KTX_STUDENT=xxxxx`). Dòng này đại diện cho việc ta gửi một yêu cầu nặc danh hoàn toàn không đăng nhập.
  - Nhấn nút **Send** để gửi request đi.
- **Kết quả:**
  - API backend vẫn trả về kết quả JSON chi tiết về số phòng, tòa nhà, giá phòng và lịch sử hợp đồng của sinh viên `SV001` một cách bình thường.
  - Ta đã bypass (vượt qua) thành công cơ chế đăng nhập của ứng dụng mà không cần tài khoản hay session, do API backend không thực hiện xác thực người dùng mà phó thác hoàn toàn cho ứng dụng Frontend.

---

### Kịch Bản 3: Tấn Công Mã Độc Lưu Trữ (Stored XSS) Qua Báo Cáo Sự Cố

**Nhiệm vụ:** Chèn một đoạn mã độc Javascript vào nội dung báo cáo sự cố để khi Quản trị viên (Admin) phê duyệt danh sách sự cố, mã độc đó sẽ tự động chạy trên trình duyệt của Admin và đánh cắp thông tin.

- **Các bước thực hiện:**
  - Đầu tiên, đăng nhập tài khoản sinh viên và truy cập chức năng **Gửi Yêu Cầu Sự Cố** (`/incident/baocao`).
  - Trong ô mô tả sự cố, nhập nội dung kiểm thử ban đầu: `Hỏng vòi nước phòng`.
  - Trên giao diện của OWASP ZAP, nhấp vào nút hình tròn màu đỏ **Set break on all requests and responses** (phím tắt `Ctrl + B`) để kích hoạt chế độ chặn bắt (chặn gói tin trước khi gửi lên server).
  - Trở lại trình duyệt, nhấn nút **Gửi Yêu Cầu**.
  - Giao diện của ZAP sẽ lập tức hiện thông tin request POST đang bị chặn tại tab **Break**. Ta tìm đến phần body của dữ liệu gửi đi (dạng `masv=SV001&maphong=P101&mota=H%E1%BB%8Fng...`).
  - Thay đổi giá trị của tham số `mota` thành đoạn mã độc Javascript sau:
    `<script>alert('XSS_ADMIN_DORM')</script>`
  - Trên thanh công cụ của ZAP, nhấn nút **Submit and step to next request** (phím mũi tên xanh) để đẩy gói tin đã sửa đổi lên server, sau đó tắt nút Break đỏ đi.
  - Bây giờ, đăng nhập vào tài khoản Admin của ký túc xá và truy cập trang **Danh Sách Sự Cố** (`/QuanLyKTX_API/incident`).
- **Kết quả:**
  - Ngay khi trang danh sách sự cố của Admin được tải, một hộp thoại thông báo (alert) hiển thị chữ `XSS_ADMIN_DORM` sẽ hiện ra trên màn hình trình duyệt của Admin.
  - Đoạn mã độc đã được chèn thành công vào cơ sở dữ liệu và tự động thực thi khi Admin tải trang. Kẻ tấn công có thể lợi dụng lỗi này để đọc Cookie Admin (`document.cookie`) và chiếm đoạt quyền điều hành hệ thống.

---

### Kịch Bản 4: Tấn Công Giả Mạo Yêu Cầu (CSRF) Thay Đổi Hồ Sơ Sinh Viên

**Nhiệm vụ:** Tạo một trang web giả mạo để lừa sinh viên đã đăng nhập bấm vào, từ đó tự động thay đổi Email và Số điện thoại trong hồ sơ của họ sang thông tin của kẻ tấn công mà họ không hề hay biết.

- **Các bước thực hiện:**
  - Đầu tiên, đăng nhập tài khoản sinh viên trên trình duyệt phụ đóng vai trò là "nạn nhân".
  - Trên trình duyệt proxy của ZAP, thực hiện hành động cập nhật hồ sơ với email mới bất kỳ để ZAP bắt được request gửi đi.
  - Tìm request cập nhật hồ sơ dạng `POST http://localhost/webktx/QuanLyKTX_API/Routes/apiUser.php?action=student_update` trong tab **History** của ZAP.
  - Nhấp chuột phải vào request POST này -> Chọn **Generate CSRF PoC...**
  - ZAP sẽ sinh ra một cấu trúc HTML của form giả lập gửi POST chứa các tham số như `email`, `sodienthoai`, `diachi` cùng giá trị do kẻ tấn công cấu hình trước (ví dụ: đổi email thành `kẻ_tấn_công@gmail.com`).
  - Copy đoạn mã HTML này và lưu thành một file tên là `attack.html`.
  - Giả lập nạn nhân đang đăng nhập hệ thống, mở file `attack.html` trên cùng trình duyệt của nạn nhân và nhấn submit form giả mạo.
- **Kết quả:**
  - Yêu cầu được gửi đi thành công mà không gặp bất kỳ lỗi nào. Khi nạn nhân tải lại trang thông tin cá nhân của mình, Email đã bị đổi thành `kẻ_tấn_công@gmail.com`.
  - Lỗ hổng xảy ra do biểu mẫu cập nhật hồ sơ của ứng dụng không sử dụng Token chống CSRF (Anti-CSRF Token) để xác thực tính hợp pháp của request được gửi từ chính hệ thống.

---

### Kịch Bản 5: Parameter Fuzzing Tìm SQL Injection Trên Bộ Lọc Tìm Kiếm Của Admin

**Nhiệm vụ:** Sử dụng công cụ Fuzzer của ZAP để tự động gửi hàng loạt ký tự đặc biệt phá vỡ cú pháp truy vấn vào ô tìm kiếm nhằm dò tìm lỗi SQL Injection (tiêm lệnh SQL).

- **Các bước thực hiện:**
  - Đầu tiên, đăng nhập quyền Admin và truy cập trang **Danh Sách Sự Cố** (`/QuanLyKTX_API/incident`).
  - Tại ô tìm kiếm, nhập từ khóa tìm kiếm thử nghiệm: `test` và nhấn **Tìm Kiếm**.
  - Trong tab **History** của ZAP, tìm request tìm kiếm vừa thực hiện:
    `GET http://localhost/webktx/QuanLyKTX_API/Routes/api.php?search=test&status=` (hoặc route tương tự của trang tìm kiếm).
  - Nhấp chuột phải vào request này -> Chọn **Fuzz...**
  - Trong cửa sổ Fuzzer hiện ra, bôi đen từ khóa `test` trong tham số `search` -> Nhấn nút **Add...** để chọn điểm chèn dữ liệu test.
  - Trong cửa sổ Payloads, nhấn **Add...** -> Chọn **Type:** `Payload Builders` hoặc chọn danh sách có sẵn từ thư viện ZAP chứa các ký tự SQL Injection cơ bản (ví dụ: `'`, `"`, `' OR '1'='1`, `1' UNION SELECT...`).
  - Nhấp vào **Start Fuzzer**. ZAP sẽ bắt đầu bắn hàng loạt request với các payload khác nhau lên server.
- **Kết quả:**
  - Trong tab Fuzzer kết quả phía dưới, lọc các request có trạng thái lỗi HTTP hoặc có kích thước phản hồi (Size) thay đổi bất thường.
  - Nếu ta thấy một số request trả về lỗi thông tin cú pháp SQL của MySQL (ví dụ: _MySQL server version for the right syntax to use..._), điều đó có nghĩa là câu lệnh SQL truy vấn trong ứng dụng đang được nối chuỗi trực tiếp mà không dùng prepared statement hoặc chưa filter kỹ đầu vào, tạo điều kiện cho phép thực hiện SQL Injection thành công.
