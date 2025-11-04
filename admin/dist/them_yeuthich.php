<?php
if (!isset($ketnoi)) require_once('ketnoi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $manguoidung = $_POST['manguoidung'];
  $masach = $_POST['masach'];
  $sql = "INSERT INTO yeuthich (manguoidung, masach, ngaythem) VALUES ('$manguoidung', '$masach', NOW())";
  mysqli_query($ketnoi, $sql);
  header("Location: index.php?page_layout=danhsachyeuthich");
  exit;
}

$nguoidung = mysqli_query($ketnoi, "SELECT idnguoidung, hoten FROM nguoidung ORDER BY hoten");
$sach = mysqli_query($ketnoi, "SELECT masach, tensach FROM sach ORDER BY tensach");
?>

<div class="card" style="max-width:500px;margin:auto;">
  <h3>Thêm Yêu Thích</h3>
  <form method="POST">
    <div class="form-group">
      <label>Người dùng</label>
      <select name="manguoidung" required>
        <option value="">-- Chọn người dùng --</option>
        <?php while($n=mysqli_fetch_assoc($nguoidung)): ?>
          <option value="<?php echo $n['idnguoidung']; ?>"><?php echo htmlspecialchars($n['hoten']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-group">
      <label>Sách</label>
      <select name="masach" required>
        <option value="">-- Chọn sách --</option>
        <?php while($s=mysqli_fetch_assoc($sach)): ?>
          <option value="<?php echo $s['masach']; ?>"><?php echo htmlspecialchars($s['tensach']); ?></option>
        <?php endwhile; ?>
      </select>
    </div>

    <button type="submit" class="btn btn-edit">Lưu</button>
    <a href="index.php?page_layout=danhsachyeuthich" class="btn btn-delete">Hủy</a>
  </form>
</div>
