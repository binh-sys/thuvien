<?php
require_once('ketnoi.php');
$id = $_GET['id'] ?? 0;
$res = mysqli_query($ketnoi, "SELECT * FROM tacgia WHERE idtacgia=$id");
if (!$res || mysqli_num_rows($res) == 0) {
  echo "<script>showToast('⚠️ Không tìm thấy tác giả!','error'); window.location='index.php?page_layout=danhsachtacgia';</script>";
  exit;
}
$row = mysqli_fetch_assoc($res);

if (isset($_POST['update_author'])) {
  $tentacgia = mysqli_real_escape_string($ketnoi, $_POST['tentacgia']);
  $ghichu    = mysqli_real_escape_string($ketnoi, $_POST['ghichu']);

  $sql = "UPDATE tacgia SET tentacgia='$tentacgia', ghichu='$ghichu' WHERE idtacgia=$id";
  if (mysqli_query($ketnoi, $sql)) {
    echo "<script>
      localStorage.setItem('author_message', JSON.stringify({text:'✅ Cập nhật thành công!', type:'success'}));
      window.location='index.php?page_layout=danhsachtacgia';
    </script>";
  } else {
    echo "<script>showToast('❌ Lỗi khi cập nhật!','error');</script>";
  }
}
?>

<div class="container mt-5" style="max-width:700px;">
  <div class="card shadow-lg border-0" style="border-radius:16px;">
    <div class="card-header text-white" style="background:linear-gradient(90deg,#1e3a8a,#2563eb);">
      <h4 class="mb-0"><i class="bx bx-edit"></i> Sửa thông tin tác giả</h4>
    </div>

    <div class="card-body p-4">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-semibold">Tên tác giả</label>
          <input type="text" name="tentacgia" class="form-control form-control-lg" value="<?php echo htmlspecialchars($row['tentacgia']); ?>" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Ghi chú</label>
          <textarea name="ghichu" rows="3" class="form-control form-control-lg"><?php echo htmlspecialchars($row['ghichu']); ?></textarea>
        </div>

        <div class="d-flex justify-content-between">
          <button type="submit" name="update_author" class="btn btn-warning text-dark px-5 fw-semibold">
            <i class="bx bx-save"></i> Cập nhật
          </button>
          <a href="index.php?page_layout=danhsachtacgia" class="btn btn-outline-secondary px-5">
            <i class="bx bx-arrow-back"></i> Quay lại
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
