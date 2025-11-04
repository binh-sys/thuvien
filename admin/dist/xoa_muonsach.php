<?php
if (!isset($ketnoi)) require_once('ketnoi.php');

$mamuon = intval($_GET['mamuon']);
mysqli_query($ketnoi, "DELETE FROM muonsach WHERE mamuon = $mamuon");
header('Location: index.php?page_layout=danhsachmuonsach');
exit;
?>
