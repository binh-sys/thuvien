<?php
session_start();
require_once('ketnoi.php');

// Trả về JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['manguoidung'])) {
        echo json_encode([
            'status' => 'error',
            'message' => '⚠️ Bạn cần đăng nhập để thêm vào yêu thích.'
        ]);
        exit;
    }

    $manguoidung = $_SESSION['manguoidung'];
    $masach = isset($_POST['masach']) ? intval($_POST['masach']) : 0;

    if ($masach <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Dữ liệu không hợp lệ.'
        ]);
        exit;
    }

    // Kiểm tra sách đã tồn tại trong danh sách yêu thích chưa
    $stmt = $ketnoi->prepare("SELECT * FROM yeuthich WHERE manguoidung = ? AND masach = ?");
    if (!$stmt) {
        throw new Exception("Lỗi truy vấn: " . $ketnoi->error);
    }

    $stmt->bind_param("ii", $manguoidung, $masach);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Nếu đã có -> xóa
        $del = $ketnoi->prepare("DELETE FROM yeuthich WHERE manguoidung = ? AND masach = ?");
        if (!$del) {
            throw new Exception("Lỗi truy vấn xóa: " . $ketnoi->error);
        }
        $del->bind_param("ii", $manguoidung, $masach);
        $del->execute();

        echo json_encode([
            'status' => 'removed',
            'message' => 'Đã bỏ khỏi danh sách yêu thích 💔'
        ]);
    } else {
        // Nếu chưa có -> thêm mới
        $ins = $ketnoi->prepare("INSERT INTO yeuthich (manguoidung, masach) VALUES (?, ?)");
        if (!$ins) {
            throw new Exception("Lỗi truy vấn thêm: " . $ketnoi->error);
        }
        $ins->bind_param("ii", $manguoidung, $masach);
        $ins->execute();

        echo json_encode([
            'status' => 'added',
            'message' => 'Đã thêm vào danh sách yêu thích ❤️'
        ]);
    }

    // Đóng kết nối
    $ketnoi->close();
} catch (Exception $e) {
    // Nếu có lỗi bất ngờ
    echo json_encode([
        'status' => 'error',
        'message' => '❌ Lỗi hệ thống: ' . $e->getMessage()
    ]);
}
