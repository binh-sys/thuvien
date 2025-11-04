<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
$mamuon = $_GET['mamuon'] ?? '';

$res = mysqli_query($ketnoi, "SELECT * FROM muonsach WHERE mamuon='$mamuon'");
$data = mysqli_fetch_assoc($res);

$nguoidung = mysqli_query($ketnoi, "SELECT idnguoidung, hoten FROM nguoidung ORDER BY hoten ASC");
$sach = mysqli_query($ketnoi, "SELECT masach, tensach FROM sach ORDER BY tensach ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manguoidung = mysqli_real_escape_string($ketnoi, $_POST['manguoidung']);
    $masach = mysqli_real_escape_string($ketnoi, $_POST['masach']);
    $ngaymuon = mysqli_real_escape_string($ketnoi, $_POST['ngaymuon']);
    $hantra = mysqli_real_escape_string($ketnoi, $_POST['hantra']);

    $sql = "UPDATE muonsach 
            SET manguoidung='$manguoidung', masach='$masach', ngaymuon='$ngaymuon', hantra='$hantra' 
            WHERE mamuon='$mamuon'";
    if (mysqli_query($ketnoi, $sql)) {
        echo "<script>alert('✅ Cập nhật thành công');window.location='index.php?page_layout=danhsachmuonsach';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Lỗi khi cập nhật');</script>";
    }
}
?>

<div class="card">
  <h3>✏️ Sửa thông tin mượn</h3>
  <form method="POST">
    <label>Người mượn:</label>
    <select name="manguoidung" required>
      <?php while($r = mysqli_fetch_assoc($nguoidung)): ?>
        <option value="<?= $r['idnguoidung'] ?>" <?= $data['manguoidung']==$r['idnguoidung']?'selected':'' ?>>
          <?= htmlspecialchars($r['hoten']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Sách:</label>
    <select name="masach" required>
      <?php while($r = mysqli_fetch_assoc($sach)): ?>
        <option value="<?= $r['masach'] ?>" <?= $data['masach']==$r['masach']?'selected':'' ?>>
          <?= htmlspecialchars($r['tensach']) ?>
        </option>
      <?php endwhile; ?>
    </select>

    <label>Ngày mượn:</label>
    <input type="date" name="ngaymuon" value="<?= $data['ngaymuon'] ?>" required>

    <label>Hạn trả:</label>
    <input type="date" name="hantra" value="<?= $data['hantra'] ?>" required>

    <div style="margin-top:15px;">
      <button type="submit" class="btn btn-edit">💾 Lưu thay đổi</button>
      <a href="index.php?page_layout=danhsachmuonsach" class="btn btn-cancel">🔙 Quay lại</a>
    </div>
  </form>
</div>
