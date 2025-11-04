<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
$tacgia = mysqli_query($ketnoi, "SELECT * FROM tacgia");
$loai = mysqli_query($ketnoi, "SELECT * FROM loaisach");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tensach = trim($_POST['tensach']);
    $matacgia = $_POST['matacgia'];
    $idloaisach = $_POST['idloaisach'];
    $soluong = (int)$_POST['soluong'];
    $hinhanhsach = '';

    if (!empty($_FILES['hinhanhsach']['name'])) {
        $fname = basename($_FILES['hinhanhsach']['name']);
        move_uploaded_file($_FILES['hinhanhsach']['tmp_name'], "images/$fname");
        $hinhanhsach = $fname;
    }

    $sql = "INSERT INTO sach (tensach, matacgia, idloaisach, soluong, hinhanhsach)
            VALUES ('$tensach', '$matacgia', '$idloaisach', '$soluong', '$hinhanhsach')";
    mysqli_query($ketnoi, $sql);
    header("Location: index.php?page_layout=danhsachsach");
    exit;
}
?>

<h3>➕ Thêm sách mới</h3>
<form method="post" enctype="multipart/form-data" style="max-width:600px">
  <label>Tên sách</label>
  <input name="tensach" class="form-control" required>

  <label>Tác giả</label>
  <select name="matacgia" class="form-control" required>
    <option value="">-- Chọn tác giả --</option>
    <?php while($t=mysqli_fetch_assoc($tacgia)): ?>
      <option value="<?= $t['matacgia'] ?>"><?= htmlspecialchars($t['tentacgia']) ?></option>
    <?php endwhile; ?>
  </select>

  <label>Thể loại</label>
  <select name="idloaisach" class="form-control" required>
    <option value="">-- Chọn thể loại --</option>
    <?php while($l=mysqli_fetch_assoc($loai)): ?>
      <option value="<?= $l['idloaisach'] ?>"><?= htmlspecialchars($l['tenloaisach']) ?></option>
    <?php endwhile; ?>
  </select>

  <label>Số lượng</label>
  <input type="number" name="soluong" min="1" class="form-control" required>

  <label>Hình ảnh sách</label>
  <input type="file" name="hinhanhsach" accept="image/*" class="form-control">

  <br>
  <button type="submit" class="btn btn-primary">Lưu</button>
  <a href="index.php?page_layout=danhsachsach" class="btn btn-secondary">Hủy</a>
</form>
