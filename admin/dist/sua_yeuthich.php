<?php
if (!isset($ketnoi)) require_once('ketnoi.php');

$id = $_GET['id'] ?? 0;
$query = mysqli_query($ketnoi, "SELECT * FROM yeuthich WHERE id=$id");
$data = mysqli_fetch_assoc($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $manguoidung = $_POST['manguoidung'];
  $masach = $_POST['masach'];
  $sql = "UPDATE yeuthich SET manguoidung='$manguoidung', masach='$masach' WHERE id=$id";
  mysqli_query($ketnoi, $sql);
  header("Location: index.php?page_layout=danhsachyeuthich");
  exit;
}

$nguoidung = mysqli_query($ketnoi, "SELECT idnguoidung, hoten FROM nguoidung ORDER BY hoten");
$sach = mysqli_query($ketnoi, "SELECT masach, tensach FROM sach ORDER BY tensach");
?>

<div class="card" style="max-width:500px;margin:auto;">
  <h3>Sửa Yêu Thích</h3>
  <form method="POST">
    <div class="form-group">
      <label>Người dùng</label>
      <select name="manguoidung" required>
        <?php while($n=mysqli_fetch_assoc($nguoidung)): ?>
          <option value="<?php echo $n['idnguoidung']; ?>" <?php if($n['idnguoidung']==$data['manguoidung']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($n['hoten']); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Sách</label>
      <select name="masach" required>
        <?php while($s=mysqli_fetch_assoc($sach)): ?>
          <option value="<?php echo $s['masach']; ?>" <?php if($s['masach']==$data['masach']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($s['tensach']); ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-edit">Cập nhật</button>
    <a href="index.php?page_layout=danhsachyeuthich" class="btn btn-delete">Hủy</a>
  </form>
</div>
