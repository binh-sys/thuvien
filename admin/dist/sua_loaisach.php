<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
$id = $_GET['idloaisach'] ?? '';
$l = mysqli_fetch_assoc(mysqli_query($ketnoi, "SELECT * FROM loaisach WHERE idloaisach='$id'"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenloaisach = trim($_POST['tenloaisach']);
    if ($tenloaisach !== '') {
        mysqli_query($ketnoi, "UPDATE loaisach SET tenloaisach='$tenloaisach' WHERE idloaisach='$id'");
        header("Location: index.php?page_layout=danhsachloaisach");
        exit;
    }
}
?>

<h3>✏️ Sửa thể loại sách</h3>
<form method="post" style="max-width:400px">
  <label>Tên thể loại</label>
  <input name="tenloaisach" value="<?= htmlspecialchars($l['tenloaisach']) ?>" class="form-control" required>
  <br>
  <button type="submit" class="btn btn-primary">Cập nhật</button>
  <a href="index.php?page_layout=danhsachloaisach" class="btn btn-secondary">Hủy</a>
</form>
