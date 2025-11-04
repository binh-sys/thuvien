<?php
require_once('ketnoi.php');
if (!isset($_GET['id'])) { header('Location: index.php?page_layout=danhsachnguoidung'); exit; }
$id = intval($_GET['id']);

$result = mysqli_query($ketnoi, "SELECT * FROM nguoidung WHERE idnguoidung=$id");
$user = mysqli_fetch_assoc($result);
if (!$user) { echo "<script>alert('Không tìm thấy người dùng');</script>"; exit; }

if (isset($_POST['update_user'])) {
  $hoten = mysqli_real_escape_string($ketnoi, $_POST['hoten']);
  $email = mysqli_real_escape_string($ketnoi, $_POST['email']);
  $sdt = mysqli_real_escape_string($ketnoi, $_POST['sdt']);
  $vaitro = mysqli_real_escape_string($ketnoi, $_POST['vaitro']);
  $matkhau = trim($_POST['matkhau']);

  if ($matkhau != '') {
    $hash = password_hash($matkhau, PASSWORD_DEFAULT);
    $sql = "UPDATE nguoidung SET hoten='$hoten', email='$email', sdt='$sdt', vaitro='$vaitro', matkhau='$hash' WHERE idnguoidung=$id";
  } else {
    $sql = "UPDATE nguoidung SET hoten='$hoten', email='$email', sdt='$sdt', vaitro='$vaitro' WHERE idnguoidung=$id";
  }

  if (mysqli_query($ketnoi, $sql)) {
    echo "<script>alert('✅ Cập nhật thành công!'); window.location='index.php?page_layout=danhsachnguoidung';</script>";
  } else {
    echo "<script>alert('❌ Lỗi khi cập nhật!');</script>";
  }
}
?>

<div class="container mt-4">
  <div class="card shadow-sm">
    <div class="card-header bg-warning text-dark">
      <h4 class="mb-0"><i class="bx bx-edit"></i> Sửa thông tin người dùng</h4>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-bold">Họ tên</label>
          <input type="text" name="hoten" class="form-control" required value="<?= htmlspecialchars($user['hoten']); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Email</label>
          <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email']); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Số điện thoại</label>
          <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($user['sdt']); ?>">
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Vai trò</label>
          <select name="vaitro" class="form-select">
            <option value="hoc_sinh" <?= $user['vaitro']=='hoc_sinh'?'selected':''; ?>>Học sinh</option>
            <option value="thuthu" <?= $user['vaitro']=='thuthu'?'selected':''; ?>>Thủ thư</option>
            <option value="admin" <?= $user['vaitro']=='admin'?'selected':''; ?>>Admin</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Mật khẩu mới (nếu muốn đổi)</label>
          <input type="password" name="matkhau" class="form-control">
        </div>

        <div class="d-flex justify-content-between">
          <button type="submit" name="update_user" class="btn btn-success px-4">
            <i class="bx bx-save"></i> Cập nhật
          </button>
          <a href="index.php?page_layout=danhsachnguoidung" class="btn btn-secondary px-4">
            <i class="bx bx-arrow-back"></i> Quay lại
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
