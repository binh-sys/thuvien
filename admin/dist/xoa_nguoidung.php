<?php
require_once('ketnoi.php');

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  $sql = "DELETE FROM nguoidung WHERE idnguoidung = $id";
  if (mysqli_query($ketnoi, $sql)) {
    echo "<script>alert('✅ Xóa người dùng thành công!'); 
          window.location='index.php?page_layout=danhsachnguoidung';</script>";
  } else {
    echo "<script>alert('❌ Lỗi khi xóa người dùng!');</script>";
  }
} else {
  header('Location: index.php?page_layout=danhsachnguoidung');
  exit;
}
?>
