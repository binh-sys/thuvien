<?php
session_start();
require_once('ketnoi.php');

// Nếu chưa đăng nhập
if (!isset($_SESSION['manguoidung'])) {
    echo json_encode(['status' => 'error', 'message' => '⚠️ Bạn cần đăng nhập để thêm vào yêu thích.']);
    exit;
}

$manguoidung = $_SESSION['manguoidung'];
$masach = isset($_POST['masach']) ? intval($_POST['masach']) : 0;

if ($masach <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
    exit;
}

// Kiểm tra xem sách đã có trong danh sách yêu thích chưa
$stmt = $ketnoi->prepare("SELECT * FROM yeuthich WHERE manguoidung = ? AND masach = ?");
$stmt->bind_param("ii", $manguoidung, $masach);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Nếu đã tồn tại thì xóa (bỏ yêu thích)
    $del = $ketnoi->prepare("DELETE FROM yeuthich WHERE manguoidung = ? AND masach = ?");
    $del->bind_param("ii", $manguoidung, $masach);
    $del->execute();
    echo json_encode(['status' => 'removed']);
} else {
    // Nếu chưa có thì thêm vào
    $ins = $ketnoi->prepare("INSERT INTO yeuthich (manguoidung, masach) VALUES (?, ?)");
    $ins->bind_param("ii", $manguoidung, $masach);
    $ins->execute();
    echo json_encode(['status' => 'added']);
}

$ketnoi->close();
?>
