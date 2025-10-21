<?php
include('ketnoi.php');

if (isset($_GET['mamuon'])) {
    $mamuon = intval($_GET['mamuon']);

    // Lấy thông tin mượn để biết sách nào
    $sql_muon = "SELECT * FROM muonsach WHERE mamuon = $mamuon";
    $result_muon = mysqli_query($ketnoi, $sql_muon);
    $muon = mysqli_fetch_assoc($result_muon);

    if (!$muon) {
        echo "<script>alert('❌ Không tìm thấy thông tin mượn sách!'); window.location.href='admin_muonsach.php';</script>";
        exit;
    }

    $masach = $muon['masach'];

    // Kiểm tra trạng thái, nếu chưa trả thì mới cho trả
    if ($muon['trangthai'] == 'da_tra') {
        echo "<script>alert('⚠️ Sách này đã được trả trước đó!'); window.location.href='admin_muonsach.php';</script>";
        exit;
    }

    // 1️⃣ Cập nhật trạng thái sang "da_tra"
    $sql_update_status = "UPDATE muonsach SET trangthai = 'da_tra', hantra = CURDATE() WHERE mamuon = $mamuon";
    $ok1 = mysqli_query($ketnoi, $sql_update_status);

    // 2️⃣ Tăng số lượng sách lên 1
    $sql_update_soluong = "UPDATE sach SET Soluong = Soluong + 1 WHERE masach = $masach";
    $ok2 = mysqli_query($ketnoi, $sql_update_soluong);

    if ($ok1 && $ok2) {
        echo "<script>alert('✅ Trả sách thành công!'); window.location.href='admin_muonsach.php';</script>";
    } else {
        echo "<script>alert('❌ Có lỗi xảy ra khi cập nhật dữ liệu!'); window.location.href='admin_muonsach.php';</script>";
    }
} else {
    echo "<script>alert('Thiếu mã mượn!'); window.location.href='admin_muonsach.php';</script>";
}
?>
