<?php
require_once('ketnoi.php');

$id = $_GET['id']; // id chi tiết đơn hàng

// Lấy id đơn hàng để cập nhật tổng tiền
$sql = "SELECT iddonhang FROM donhang_chitiet WHERE id = $id";
$ct = mysqli_fetch_assoc(mysqli_query($ketnoi, $sql));
$iddonhang = $ct['iddonhang'];

// Xóa sản phẩm
mysqli_query($ketnoi, "DELETE FROM donhang_chitiet WHERE id = $id");

// Cập nhật lại tổng tiền
mysqli_query($ketnoi, "
  UPDATE donhang
  SET tongtien = (SELECT COALESCE(SUM(thanhtien),0) FROM donhang_chitiet WHERE iddonhang=$iddonhang)
  WHERE iddonhang=$iddonhang
");

header("Location: index.php?page_layout=xem_donhang&iddonhang=$iddonhang");
exit;
?>
