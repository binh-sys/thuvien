<?php
if (!isset($ketnoi)) require_once('ketnoi.php');

// Lấy danh sách người dùng và sách
$nguoidung = mysqli_query($ketnoi, "SELECT idnguoidung, hoten FROM nguoidung ORDER BY hoten ASC");
$sach = mysqli_query($ketnoi, "SELECT masach, tensach FROM sach ORDER BY tensach ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manguoidung = mysqli_real_escape_string($ketnoi, $_POST['manguoidung']);
    $masach = mysqli_real_escape_string($ketnoi, $_POST['masach']);
    $ngaymuon = mysqli_real_escape_string($ketnoi, $_POST['ngaymuon']);
    $hantra = mysqli_real_escape_string($ketnoi, $_POST['hantra']);

    $sql = "INSERT INTO muonsach (manguoidung, masach, ngaymuon, hantra, trangthai)
            VALUES ('$manguoidung', '$masach', '$ngaymuon', '$hantra', 'dang_muon')";
    if (mysqli_query($ketnoi, $sql)) {
        echo "<script>alert('✅ Ghi mượn sách thành công');window.location='index.php?page_layout=danhsachmuonsach';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Lỗi khi ghi mượn sách');</script>";
    }
}
?>

<div class="card">
  <h3>➕ Ghi Mượn Sách</h3>
  <form method="POST">
    <label>Người mượn:</label>
    <select name="manguoidung" required>
      <option value="">-- Chọn người mượn --</option>
      <?php while($r = mysqli_fetch_assoc($nguoidung)): ?>
        <option value="<?= $r['idnguoidung'] ?>"><?= htmlspecialchars($r['hoten']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Sách:</label>
    <select name="masach" required>
      <option value="">-- Chọn sách --</option>
      <?php while($r = mysqli_fetch_assoc($sach)): ?>
        <option value="<?= $r['masach'] ?>"><?= htmlspecialchars($r['tensach']) ?></option>
      <?php endwhile; ?>
    </select>

    <label>Ngày mượn:</label>
    <input type="date" name="ngaymuon" required>

    <label>Hạn trả:</label>
    <input type="date" name="hantra" required>

    <div style="margin-top:15px;">
      <button type="submit" class="btn btn-edit">💾 Lưu lại</button>
      <a href="index.php?page_layout=danhsachmuonsach" class="btn btn-cancel">🔙 Quay lại</a>
    </div>
  </form>
</div>
