<?php
namespace Services;

class ImportService {
    /**
     * Đọc file CSV và trả về mảng dữ liệu (Mapping tự động Header với Value)
     * @param array $file $_FILES['file']
     * @return array
     */
    public function parseCsv($file) {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'error' => 'Vui lòng chọn file hợp lệ.'];
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        if (strtolower($ext) !== 'csv') {
            return ['success' => false, 'error' => 'Chỉ hỗ trợ định dạng .csv'];
        }

        $handle = fopen($file['tmp_name'], "r");
        if ($handle !== FALSE) {
            $data = [];
            $header = fgetcsv($handle, 1000, ",");
            
            // Xử lý BOM nếu file có chứa thẻ BOM ở đầu
            if (isset($header[0]) && substr($header[0], 0, 3) == "\xef\xbb\xbf") {
                $header[0] = substr($header[0], 3);
            }
            
            // Đọc từng dòng
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // Chỉ lấy các dòng có số lượng cột khớp với Header
                if (count($header) == count($row)) {
                    $data[] = array_combine($header, $row);
                } else {
                    // Nếu số cột không khớp, có thể cảnh báo nhưng tạm thời ta bỏ qua dòng rác
                    continue; 
                }
            }
            fclose($handle);
            return ['success' => true, 'data' => $data];
        }
        
        return ['success' => false, 'error' => 'Lỗi trong quá trình mở file.'];
    }
}
