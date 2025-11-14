<?php
require_once('ketnoi.php');

$iddonhang = $_GET['iddonhang'];

$sql = "DELETE FROM donhang WHERE iddonhang = $iddonhang";
mysqli_query($ketnoi, $sql);

header("Location: index.php?page_layout=danhsachdonhang");
exit;
?>
