<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  mysqli_query($ketnoi, "DELETE FROM yeuthich WHERE id = $id");
}
header("Location: index.php?page_layout=danhsachyeuthich");
exit;
?>
