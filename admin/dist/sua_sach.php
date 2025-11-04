<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
$masach = $_GET['masach'] ?? '';
$s = mysqli_fetch_assoc(mysqli_query($ketnoi, "SELECT * FROM sach WHERE masach='$masach'"));
$tacgia = mysqli_query($ketnoi, "SELECT * FROM tacgia");
$loai = mysqli_query($ketnoi, "SELECT * FROM loaisach");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tensach = trim($_POST['tensach']);
    $matacgia = $_POST['matacgia'];
    $idloaisach = $_POST['idloaisach'];
    $soluong = (int)$_POST['soluong'];
    $hinhanhsach = $s['hinhanhsach'];

    if (!empty($_FILES['hinhanhsach']['name'])) {
        $fname = basename($_FILES['hinhanhsach']['name']);
        move_uploaded_file($_FILES['hinhanhsach']['tmp_name'], "images/$fname");
        $hinhanhsach = $fname;
    }

    $sql = "UPDATE sach SET 
              tensach='$tensach', 
              matacgia='$matacgia',
              idloaisach='$idloaisach',
              soluong='$soluong',
              hinhanhsach='$hinhanhsach'
            WHERE masach='$masach'";
    mysqli_query($ketnoi, $sql);
    header("Location: index.php?page_layout=danhsachsach");
    exit;
}
?>

<h3>✏️ Sửa thông tin sách</h3>
<form method="post" enctype="multipart/form-data" style="max-width:600px">
  <label>Tên sách</label>
  <input name="tensach" value="<?= htmlspecialchars($s['tensach']) ?>" class="form-control" required>

  <label>Tác giả</label>
  <select name="matacgia" class="form-control" required>
    <?php while($t=mysqli_fetch_assoc($tacgia)): ?>
      <option value="<?= $t['matacgia'] ?>" <?= $t['matacgia']==$s['matacgia']?'selected':'' ?>>
        <?= htmlspecialchars($t['tentacgia']) ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label>Thể loại</label>
  <select name="idloaisach" class="form-control" required>
    <?php while($l=mysqli_fetch_assoc($loai)): ?>
      <option value="<?= $l['idloaisach'] ?>" <?= $l['idloaisach']==$s['idloaisach']?'selected':'' ?>>
        <?= htmlspecialchars($l['tenloaisach']) ?>
      </option>
    <?php endwhile; ?>
  </select>

  <label>Số lượng</label>
  <input type="number" name="soluong" value="<?= (int)$s['soluong'] ?>" class="form-control" required>

  <label>Hình ảnh hiện tại</label><br>
  <img src="images/<?= htmlspecialchars($s['hinhanhsach']) ?>" width="60"><br><br>
  <label>Chọn ảnh mới (nếu muốn đổi)</label>
  <input type="file" name="hinhanhsach" accept="image/*" class="form-control">

  <br>
  <button type="submit" class="btn btn-primary">Cập nhật</button>
  <a href="index.php?page_layout=danhsachsach" class="btn btn-secondary">Hủy</a>
</form>
