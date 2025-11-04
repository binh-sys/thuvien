<?php
if (!isset($ketnoi)) require_once('ketnoi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenloaisach = trim($_POST['tenloaisach']);
    if ($tenloaisach !== '') {
        $sql = "INSERT INTO loaisach (tenloaisach, created_at) VALUES ('$tenloaisach', NOW())";
        mysqli_query($ketnoi, $sql);
        header("Location: index.php?page_layout=danhsachloaisach");
        exit;
    }
}
?>

<h3>➕ Thêm thể loại sách</h3>
<form method="post" style="max-width:400px">
  <label>Tên thể loại</label>
  <input name="tenloaisach" class="form-control" required>
  <br>
  <button type="submit" class="btn btn-primary">Lưu</button>
  <a href="index.php?page_layout=danhsachloaisach" class="btn btn-secondary">Hủy</a>
</form>
