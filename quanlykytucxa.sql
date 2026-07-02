-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 11:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quanlykytucxa`
--

-- --------------------------------------------------------

--
-- Table structure for table `hopdong`
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
-- Dumping data for table `hopdong`
--

INSERT INTO `hopdong` (`mahopdong`, `masv`, `maphong`, `batdau`, `hethan`, `trangthai`, `created_at`) VALUES
('HD001', '74DCTT22211', 'A101', '2026-05-03', '2026-05-08', 'Đã Chấm Dứt', '2026-05-08 16:02:36'),
('HD002', '74DCKT12345', 'A101', '2026-05-13', '2026-05-30', 'Đang Hoạt Động', '2026-05-08 18:41:00');

--
-- Triggers `hopdong`
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
-- Table structure for table `phong`
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
-- Dumping data for table `phong`
--

INSERT INTO `phong` (`id`, `maphong`, `sophong`, `toa`, `succhua`, `phonghientai`, `gia`, `trangthai`, `created_at`) VALUES
(1, 'A101', '101', 'A', 8, 1, 1500000.00, 'Trống', '2026-05-07 13:02:57'),
(2, 'A102', '102', 'A', 8, 0, 1200000.00, 'Trống', '2026-05-08 07:01:28'),
(3, 'B306', '306', 'B', 8, 0, 1800000.00, 'Trống', '2026-05-08 07:01:51'),
(6, 'C102', '102', 'C', 8, 0, 20000.00, 'Trống', '2026-05-09 08:10:24');

-- --------------------------------------------------------

--
-- Table structure for table `sinhvien`
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
-- Dumping data for table `sinhvien`
--

INSERT INTO `sinhvien` (`id`, `masv`, `hoten`, `lop`, `gioitinh`, `cccd`, `sodienthoai`, `email`, `diachi`, `created_at`) VALUES
(9, '74DCTT22211', 'A', 'a', 'Nam', '', '3463443', 'sda@gmail.com', '32524', '2026-05-08 16:00:50'),
(10, '74DCKT12345', 'B', 'KT', 'Nam', '020304007525', '0903073518', 'b@gmail.com', 'Hà Nội', '2026-05-08 17:15:33');

--
-- Triggers `sinhvien`
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
-- Table structure for table `suco`
--

CREATE TABLE `suco` (
  `masuco` int(11) NOT NULL,
  `masv` varchar(30) DEFAULT NULL,
  `maphong` varchar(20) DEFAULT NULL,
  `mota` text DEFAULT NULL,
  `ngaybao` date DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suco`
--

INSERT INTO `suco` (`masuco`, `masv`, `maphong`, `mota`, `ngaybao`, `trangthai`, `created_at`) VALUES
(14, NULL, 'A101', 'H', '2026-05-08', 'Chờ Xử Lý', '2026-05-08 13:45:08'),
(16, '74DCTT22211', 'A101', 'éefsefsef', '2026-05-08', 'Mới gửi', '2026-05-08 16:07:41'),
(17, '74DCKT12345', 'A101', 'hỏng đèn', '2026-05-09', 'Chờ Xử Lý', '2026-05-09 08:18:30');

-- --------------------------------------------------------

--
-- Table structure for table `suco_yeucau`
--

CREATE TABLE `suco_yeucau` (
  `id` int(11) NOT NULL,
  `masv` varchar(20) DEFAULT NULL,
  `maphong` varchar(20) DEFAULT NULL,
  `mota` text DEFAULT NULL,
  `ngaybao` date DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT 'cho_duyet',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan_admin`
--

CREATE TABLE `taikhoan_admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taikhoan_admin`
--

INSERT INTO `taikhoan_admin` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'admin123', '2026-05-07 12:10:39');

-- --------------------------------------------------------

--
-- Table structure for table `taikhoan_user`
--

CREATE TABLE `taikhoan_user` (
  `id` int(11) NOT NULL,
  `masv` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `taikhoan_user`
--

INSERT INTO `taikhoan_user` (`id`, `masv`, `password`, `created_at`) VALUES
(6, '74DCTT22211', '123456', '2026-05-08 16:00:50'),
(7, '74DCKT12345', '123456', '2026-05-08 17:15:33');

-- --------------------------------------------------------

--
-- Table structure for table `thanhtoan`
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
-- Dumping data for table `thanhtoan`
--

INSERT INTO `thanhtoan` (`mathanhtoan`, `maphong`, `sotien`, `ngaytra`, `trangthai`, `created_at`) VALUES
(3, 'A101', 1500000.00, '2026-05-03', 'Đã Thanh Toán', '2026-05-08 07:32:17'),
(4, 'B306', 1800000.00, '2026-03-12', 'Đã Thanh Toán', '2026-05-08 07:32:25'),
(7, 'A102', 2000000.00, '2026-05-07', 'Đã Thanh Toán', '2026-05-08 17:50:55');

-- --------------------------------------------------------

--
-- Table structure for table `tiendien`
--

CREATE TABLE `tiendien` (
  `matd` int(11) NOT NULL,
  `maphong` varchar(20) NOT NULL,
  `giadien` varchar(20) DEFAULT NULL,
  `ngay` date NOT NULL,
  `trangthai` varchar(50) DEFAULT 'Chưa thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tiendien`
--

INSERT INTO `tiendien` (`matd`, `maphong`, `giadien`, `ngay`, `trangthai`) VALUES
(1, 'A101', '3250.8', '2026-05-13', 'Chưa thanh toán'),
(3, 'B306', '3973.2000000000003', '2026-05-08', 'Đã thanh toán'),
(5, 'A101', '3431.3999999999996', '2026-05-08', 'Đã thanh toán');

-- --------------------------------------------------------

--
-- Table structure for table `tiennuoc`
--

CREATE TABLE `tiennuoc` (
  `matn` int(11) NOT NULL,
  `maphong` varchar(20) NOT NULL,
  `gianuoc` varchar(20) DEFAULT NULL,
  `ngay` date NOT NULL,
  `trangthai` varchar(50) DEFAULT 'Chưa thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tiennuoc`
--

INSERT INTO `tiennuoc` (`matn`, `maphong`, `gianuoc`, `ngay`, `trangthai`) VALUES
(1, 'A102', '116000', '2026-05-08', 'Đã thanh toán'),
(2, 'B306', '48000', '2026-05-11', 'Chưa thanh toán'),
(3, 'A102', '13600', '2026-05-08', 'Chưa thanh toán');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hopdong`
--
ALTER TABLE `hopdong`
  ADD PRIMARY KEY (`mahopdong`),
  ADD KEY `masv` (`masv`),
  ADD KEY `maphong` (`maphong`);

--
-- Indexes for table `phong`
--
ALTER TABLE `phong`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `maphong` (`maphong`);

--
-- Indexes for table `sinhvien`
--
ALTER TABLE `sinhvien`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `masv` (`masv`);

--
-- Indexes for table `suco`
--
ALTER TABLE `suco`
  ADD PRIMARY KEY (`masuco`),
  ADD KEY `maphong` (`maphong`),
  ADD KEY `fk_suco_sinhvien` (`masv`);

--
-- Indexes for table `suco_yeucau`
--
ALTER TABLE `suco_yeucau`
  ADD PRIMARY KEY (`id`),
  ADD KEY `masv` (`masv`),
  ADD KEY `maphong` (`maphong`);

--
-- Indexes for table `taikhoan_admin`
--
ALTER TABLE `taikhoan_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `taikhoan_user`
--
ALTER TABLE `taikhoan_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `masv` (`masv`);

--
-- Indexes for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`mathanhtoan`),
  ADD KEY `maphong` (`maphong`);

--
-- Indexes for table `tiendien`
--
ALTER TABLE `tiendien`
  ADD PRIMARY KEY (`matd`),
  ADD KEY `maphong` (`maphong`);

--
-- Indexes for table `tiennuoc`
--
ALTER TABLE `tiennuoc`
  ADD PRIMARY KEY (`matn`),
  ADD KEY `maphong` (`maphong`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `phong`
--
ALTER TABLE `phong`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sinhvien`
--
ALTER TABLE `sinhvien`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `suco`
--
ALTER TABLE `suco`
  MODIFY `masuco` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `suco_yeucau`
--
ALTER TABLE `suco_yeucau`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taikhoan_admin`
--
ALTER TABLE `taikhoan_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taikhoan_user`
--
ALTER TABLE `taikhoan_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  MODIFY `mathanhtoan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tiendien`
--
ALTER TABLE `tiendien`
  MODIFY `matd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tiennuoc`
--
ALTER TABLE `tiennuoc`
  MODIFY `matn` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `hopdong`
--
ALTER TABLE `hopdong`
  ADD CONSTRAINT `hopdong_ibfk_1` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`),
  ADD CONSTRAINT `hopdong_ibfk_2` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`);

--
-- Constraints for table `suco`
--
ALTER TABLE `suco`
  ADD CONSTRAINT `fk_suco_sinhvien` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`) ON DELETE SET NULL,
  ADD CONSTRAINT `suco_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`);

--
-- Constraints for table `suco_yeucau`
--
ALTER TABLE `suco_yeucau`
  ADD CONSTRAINT `suco_yeucau_ibfk_1` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`),
  ADD CONSTRAINT `suco_yeucau_ibfk_2` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`);

--
-- Constraints for table `taikhoan_user`
--
ALTER TABLE `taikhoan_user`
  ADD CONSTRAINT `taikhoan_user_ibfk_1` FOREIGN KEY (`masv`) REFERENCES `sinhvien` (`masv`);

--
-- Constraints for table `thanhtoan`
--
ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `thanhtoan_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`);

--
-- Constraints for table `tiendien`
--
ALTER TABLE `tiendien`
  ADD CONSTRAINT `tiendien_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tiennuoc`
--
ALTER TABLE `tiennuoc`
  ADD CONSTRAINT `tiennuoc_ibfk_1` FOREIGN KEY (`maphong`) REFERENCES `phong` (`maphong`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
