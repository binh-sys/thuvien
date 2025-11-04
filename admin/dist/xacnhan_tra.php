<?php
// xacnhan_tra.php
if (!isset($ketnoi)) require_once('ketnoi.php');
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Kiểm tra có mã mượn không
if (!isset($_GET['mamuon'])) {
  echo "<script>alert('Thiếu mã mượn!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
  exit;
}

$mamuon = mysqli_real_escape_string($ketnoi, $_GET['mamuon']);

// Kiểm tra bản ghi có tồn tại không
$check = mysqli_query($ketnoi, "SELECT * FROM muonsach WHERE mamuon='$mamuon'");
if (!$check || mysqli_num_rows($check) == 0) {
  echo "<script>alert('Không tìm thấy bản ghi mượn!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
  exit;
}

// Cập nhật trạng thái sang 'da_tra'
$sql = "UPDATE muonsach SET trangthai='da_tra' WHERE mamuon='$mamuon'";
if (mysqli_query($ketnoi, $sql)) {
  echo "<script>alert('✅ Đã xác nhận trả sách thành công!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
} else {
  echo "<script>alert('❌ Lỗi khi cập nhật dữ liệu!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
}
?>
