<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
$masach = $_GET['masach'] ?? '';
if ($masach) {
    mysqli_query($ketnoi, "DELETE FROM sach WHERE masach='$masach'");
}
header("Location: index.php?page_layout=danhsachsach");
exit;
?>
