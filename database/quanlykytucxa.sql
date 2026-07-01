-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th7 01, 2026 lúc 10:58 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanlykytucxa`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hopdong`
--

CREATE TABLE `hopdong` (
  `mahopdong` varchar(20) NOT NULL,
  `masv` varchar(20) DEFAULT NULL,
  `maphong` varchar(20) DEFAULT NULL,
  `batdau` date DEFAULT NULL,
  `hethan` date DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `hopdong`
--

INSERT INTO `hopdong` (`mahopdong`, `masv`, `maphong`, `batdau`, `hethan`, `trangthai`, `created_at`) VALUES
('HD001', '74DCTT001', 'P101', '2026-01-01', '2026-12-31', 'Đang Hoạt Động', '2026-04-05 03:06:09'),
('HD003', '74DCTT003', 'P201', '2026-03-01', '2026-12-31', 'Đang Hoạt Động', '2026-04-05 03:06:09'),
('HD004', '74DCTT0021', 'P201', '2026-04-26', '2026-07-01', 'Đang Hoạt Động', '2026-05-09 13:47:13'),
('HD005', 'SV001', 'C101', '2026-06-02', '2027-10-23', 'Đang Hoạt Động', '2026-06-23 02:08:35'),
('HD006', '74DCTT0030', 'D102', '2026-06-01', '2026-06-28', 'Đang Hoạt Động', '2026-06-28 15:51:18');

--
-- Bẫy `hopdong`
--
DELIMITER $$
CREATE TRIGGER `trg_hopdong_after_delete` AFTER DELETE ON `hopdong` FOR EACH ROW BEGIN
    IF OLD.trangthai = 'Đang Hoạt Động' THEN
        UPDATE phong SET phonghientai = GREATEST(0, phonghientai - 1) WHERE maphong = OLD.maphong;
    END IF;
    UPDATE phong SET trangthai = IF(phonghientai >= succhua, 'Đầy', 'Trống') WHERE maphong = OLD.maphong;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hopdong_after_insert` AFTER INSERT ON `hopdong` FOR EACH ROW BEGIN
    IF NEW.trangthai = 'Đang Hoạt Động' THEN
        UPDATE phong SET phonghientai = phonghientai + 1 WHERE maphong = NEW.maphong;
    END IF;
    UPDATE phong SET trangthai = IF(phonghientai >= succhua, 'Đầy', 'Trống') WHERE maphong = NEW.maphong;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_hopdong_after_update` AFTER UPDATE ON `hopdong` FOR EACH ROW BEGIN
    -- 1. Nếu thay đổi phòng
    IF OLD.maphong <> NEW.maphong THEN
        IF OLD.trangthai = 'Đang Hoạt Động' THEN
            UPDATE phong SET phonghientai = GREATEST(0, phonghientai - 1) WHERE maphong = OLD.maphong;
        END IF;
        IF NEW.trangthai = 'Đang Hoạt Động' THEN
            UPDATE phong SET phonghientai = phonghientai + 1 WHERE maphong = NEW.maphong;
        END IF;
    -- 2. Nếu không đổi phòng nhưng đổi trạng thái
    ELSEIF OLD.trangthai <> NEW.trangthai THEN
        IF OLD.trangthai = 'Đang Hoạt Động' THEN
            UPDATE phong SET phonghientai = GREATEST(0, phonghientai - 1) WHERE maphong = OLD.maphong;
        ELSEIF NEW.trangthai = 'Đang Hoạt Động' THEN
            UPDATE phong SET phonghientai = phonghientai + 1 WHERE maphong = NEW.maphong;
        END IF;
    END IF;

    -- Cập nhật trạng thái
    UPDATE phong SET trangthai = IF(phonghientai >= succhua, 'Đầy', 'Trống') WHERE maphong = OLD.maphong;
    UPDATE phong SET trangthai = IF(phonghientai >= succhua, 'Đầy', 'Trống') WHERE maphong = NEW.maphong;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phong`
--

CREATE TABLE `phong` (
  `id` int(11) NOT NULL,
  `maphong` varchar(20) NOT NULL,
  `sophong` varchar(10) DEFAULT NULL,
  `toa` varchar(5) DEFAULT NULL,
  `succhua` int(11) DEFAULT 8,
  `phonghientai` int(11) DEFAULT 0,
  `gia` decimal(10,2) DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `phong`
--

INSERT INTO `phong` (`id`, `maphong`, `sophong`, `toa`, `succhua`, `phonghientai`, `gia`, `trangthai`, `created_at`) VALUES
(1, 'P101', '101', 'A', 8, 1, 1200000.00, 'Trống', '2026-04-05 03:06:09'),
(2, 'P102', '102', 'A', 8, 0, 1200000.00, 'Bảo Trì', '2026-04-05 03:06:09'),
(3, 'P201', '201', 'B', 8, 2, 1500000.00, 'Bảo Trì', '2026-04-05 03:06:09'),
(9, 'C101', '101', 'C', 8, 1, 1200000.00, 'Trống', '2026-05-09 15:41:37'),
(10, 'D102', '102', 'D', 8, 1, 200000.00, 'Trống', '2026-06-28 15:50:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sinhvien`
--

CREATE TABLE `sinhvien` (
  `id` int(11) NOT NULL,
  `masv` varchar(20) NOT NULL,
  `hoten` varchar(100) NOT NULL,
  `lop` varchar(20) DEFAULT NULL,
  `gioitinh` varchar(10) DEFAULT NULL,
  `cccd` varchar(20) DEFAULT NULL,
  `sodienthoai` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `diachi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sinhvien`
--

INSERT INTO `sinhvien` (`id`, `masv`, `hoten`, `lop`, `gioitinh`, `cccd`, `sodienthoai`, `email`, `diachi`, `created_at`) VALUES
(1, '74DCTT001', 'Nguyễn Văn A', 'CNTT', 'Nam', '012345678901', '0918798764', 'nguyenvana@example.com', '123 Đường ABC, Quận ABC, Hà Nộii', '2026-04-05 02:59:34'),
(2, '74DCTT002', 'Tran Thi B', 'CNTT2', 'Nữ', '012345678902', '0912345678', 'NguyenThiB@example.com', 'Hà Nội', '2026-04-05 02:59:34'),
(3, '74DCTT003', 'Le Van C', 'CNTT3', 'Nam', '012345678903', '0903456789', 'c@example.com', 'Nam Định', '2026-04-05 02:59:34'),
(5, '74DCTT0024', 'Nguyễn Văn B', '74DCTT28', 'Nữ', '022156765761', '087777754', 'vanb@example.com', 'Hồ Chí Minh', '2026-04-21 14:07:50'),
(6, '74DCTT32109', 'Bành Thị Lòi Le', 'CNTT36', 'Nam', '0739825173', '08882573118', 'loile@gmail.com', 'Hà Nội', '2026-05-09 09:26:35'),
(8, '74DCTT0021', 'Nguyễn Văn C', 'CNTT1', 'Nam', '012345678902', '0877865134', 'NguyenVanC@example.com', 'Hà Nội', '2026-05-09 10:02:45'),
(16, 'SV001', 'Nguyễn Tiến D', 'CT08C', 'Nam', '012345678901', '0901234567', 'NguyenVanC@example.com', 'Hà Nội', '2026-06-23 02:04:59'),
(17, '74DCTT0030', 'Nguyễn Thị Dung', 'CTD0021', 'Nữ', '02211231233', '087794552', 'nguyenthid@gmail.com', 'Hà Tây', '2026-06-28 15:44:42');

--
-- Bẫy `sinhvien`
--
DELIMITER $$
CREATE TRIGGER `after_sinhvien_insert` AFTER INSERT ON `sinhvien` FOR EACH ROW BEGIN
    INSERT INTO taikhoan_user (masv, password)
    VALUES (NEW.masv, '123456');
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_sinhvien_delete` BEFORE DELETE ON `sinhvien` FOR EACH ROW BEGIN
    -- Xóa tài khoản liên kết với mã sinh viên sắp bị xóa
    DELETE FROM taikhoan_user WHERE masv = OLD.masv;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suco`
--

CREATE TABLE `suco` (
  `masuco` int(11) NOT NULL,
  `maphong` varchar(20) DEFAULT NULL,
  `masv` varchar(20) DEFAULT NULL,
  `mota` text DEFAULT NULL,
  `ngaybao` date DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `suco`
--

INSERT INTO `suco` (`masuco`, `maphong`, `masv`, `mota`, `ngaybao`, `trangthai`, `created_at`) VALUES
(1, 'P101', NULL, 'Hỏng bóng đèn', '2026-01-10', 'Đã xử lý', '2026-04-05 03:06:09'),
(2, 'P102', NULL, 'Rò rỉ nước', '2026-02-12', 'Đang xử lý', '2026-04-05 03:06:09'),
(3, 'P201', NULL, 'Ổ cắm điện hỏng', '2026-03-08', 'Đã xử lý', '2026-04-05 03:06:09'),
(5, 'P101', NULL, 'Hỏng bàn', '2026-01-11', 'Đang xử lý', '2026-05-07 13:49:40'),
(6, 'P101', '74DCTT001', 'Hỏng đèn', '2026-05-09', 'Mới gửi', '2026-05-09 10:15:31'),
(7, 'P101', '74DCTT001', 'Hỏng bóng đèn trong phòng', '2026-05-09', 'Mới gửi', '2026-05-09 14:59:56'),
(9, 'P101', '74DCTT001', 'Hỏng bóng đèn trong phòng', '2026-05-09', 'Mới gửi', '2026-05-09 15:29:32'),
(10, 'P101', '74DCTT001', 'Hỏng bóng đèn trong phòng', '2026-05-09', 'Mới gửi', '2026-05-09 15:29:38'),
(11, 'P101', '74DCTT001', 'Hỏng bóng đèn trong phòng', '2026-05-09', 'Mới gửi', '2026-05-09 15:44:52'),
(12, 'P101', NULL, 'Sập giường', '2026-06-28', 'Chờ Xử Lý', '2026-06-28 15:55:24'),
(13, 'D102', '74DCTT0030', 'qưeqweqwe', '2026-06-28', 'Mới gửi', '2026-06-28 16:30:03');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan_admin`
--

CREATE TABLE `taikhoan_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan_admin`
--

INSERT INTO `taikhoan_admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'admin123', '2026-04-05 02:59:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `taikhoan_user`
--

CREATE TABLE `taikhoan_user` (
  `id` int(11) NOT NULL,
  `masv` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `taikhoan_user`
--

INSERT INTO `taikhoan_user` (`id`, `masv`, `password`, `created_at`) VALUES
(4, '74DCTT001', '1234', '2026-04-05 03:06:09'),
(5, '74DCTT002', '123', '2026-04-05 03:06:09'),
(6, '74DCTT003', '123456', '2026-04-05 03:06:09'),
(7, '74DCTT0024', '123', '2026-04-21 14:07:50'),
(8, '74DCTT32109', '123456', '2026-05-09 09:26:35'),
(9, '74DCTT0021', '123456', '2026-05-09 10:02:45'),
(10, 'SV001', '123456', '2026-06-23 02:04:59'),
(11, '74DCTT0030', '123456', '2026-06-28 15:44:42');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `thanhtoan`
--

CREATE TABLE `thanhtoan` (
  `mathanhtoan` int(11) NOT NULL,
  `maphong` varchar(20) DEFAULT NULL,
  `sotien` decimal(10,2) DEFAULT NULL,
  `ngaytra` date DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `thanhtoan`
--

INSERT INTO `thanhtoan` (`mathanhtoan`, `maphong`, `sotien`, `ngaytra`, `trangthai`, `created_at`) VALUES
(2, 'P102', 1200000.00, '2026-02-05', 'Đã trả', '2026-04-05 03:06:09'),
(4, 'P101', 1200000.00, '2026-07-01', 'Đã Thanh Toán', '2026-06-28 15:53:02');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tiendien`
--

CREATE TABLE `tiendien` (
  `matd` int(11) NOT NULL,
  `maphong` varchar(20) NOT NULL,
  `giadien` varchar(20) DEFAULT NULL,
  `ngay` date NOT NULL,
  `trangthai` varchar(50) DEFAULT 'Chưa thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tiendien`
--

INSERT INTO `tiendien` (`matd`, `maphong`, `giadien`, `ngay`, `trangthai`) VALUES
(1, 'D102', '183600', '2026-06-28', 'Đã thanh toán');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tiennuoc`
--

CREATE TABLE `tiennuoc` (
  `matn` int(11) NOT NULL,
  `maphong` varchar(20) NOT NULL,
  `gianuoc` varchar(20) DEFAULT NULL,
  `ngay` date NOT NULL,
  `trangthai` varchar(50) DEFAULT 'Chưa thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `hopdong`
--
ALTER TABLE `hopdong`
  ADD PRIMARY KEY (`mahopdong`),
  ADD KEY `masv` (`masv`),
  ADD KEY `maphong` (`maphong`);

--
-- Chỉ mục cho bảng `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `maphong` (`maphong`);

--
-- Chỉ mục cho bảng `sinhvien`
--
ALTER TABLE `sinhvien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `masv` (`masv`);

--
-- Chỉ mục cho bảng `suco`
--
ALTER TABLE `suco`
  ADD PRIMARY KEY (`masuco`),
  ADD KEY `maphong` (`maphong`);

--
-- Chỉ mục cho bảng `taikhoan_admin`
--
ALTER TABLE `taikhoan_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `taikhoan_user`
--
ALTER TABLE `taikhoan_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `masv` (`masv`);

--
-- Chỉ mục cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`mathanhtoan`),
  ADD KEY `maphong` (`maphong`);

--
-- Chỉ mục cho bảng `tiendien`
--
ALTER TABLE `tiendien`
  ADD PRIMARY KEY (`matd`),
  ADD KEY `maphong` (`maphong`);

--
-- Chỉ mục cho bảng `tiennuoc`
--
ALTER TABLE `tiennuoc`
  ADD PRIMARY KEY (`matn`),
  ADD KEY `maphong` (`maphong`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `phong`
--
ALTER TABLE `phong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `sinhvien`
--
ALTER TABLE `sinhvien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT cho bảng `suco`
--
ALTER TABLE `suco`
  MODIFY `masuco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `taikhoan_admin`
--
ALTER TABLE `taikhoan_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `taikhoan_user`
--
ALTER TABLE `taikhoan_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  MODIFY `mathanhtoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `tiendien`
--
ALTER TABLE `tiendien`
  MODIFY `matd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `tiennuoc`
--
ALTER TABLE `tiennuoc`
  MODIFY `matn` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `hopdong`
--
ALTER TABLE `hopdong`
  ADD CONSTRAINT `hopdong_ibfk_1` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`),
  ADD CONSTRAINT `hopdong_ibfk_2` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`);

--
-- Các ràng buộc cho bảng `suco`
--
ALTER TABLE `suco`
  ADD CONSTRAINT `suco_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`);

--
-- Các ràng buộc cho bảng `taikhoan_user`
--
ALTER TABLE `taikhoan_user`
  ADD CONSTRAINT `taikhoan_user_ibfk_1` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`);

--
-- Các ràng buộc cho bảng `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `thanhtoan_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`);

--
-- Các ràng buộc cho bảng `tiendien`
--
ALTER TABLE `tiendien`
  ADD CONSTRAINT `tiendien_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Các ràng buộc cho bảng `tiennuoc`
--
ALTER TABLE `tiennuoc`
  ADD CONSTRAINT `tiennuoc_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;