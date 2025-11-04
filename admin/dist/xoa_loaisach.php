<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
$id = $_GET['idloaisach'] ?? '';
if ($id) {
    mysqli_query($ketnoi, "DELETE FROM loaisach WHERE idloaisach='$id'");
}
header("Location: index.php?page_layout=danhsachloaisach");
exit;
?>
