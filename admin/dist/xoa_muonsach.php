<?php
require_once('ketnoi.php');

if (!isset($_GET['idmuon'])) {
  echo "<script>alert('❌ Không tìm thấy phiếu mượn cần xóa!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
  exit;
}

$idmuon = $_GET['idmuon'];

$sql = "DELETE FROM muonsach WHERE idmuon = '$idmuon'";
if (mysqli_query($ketnoi, $sql)) {
  echo "<script>alert('✅ Đã xóa phiếu mượn thành công!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
} else {
  echo "<script>alert('❌ Không thể xóa phiếu mượn (có thể liên quan dữ liệu khác)!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
}
?>
