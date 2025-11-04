<?php
require_once('ketnoi.php');

if (isset($_POST['add_author'])) {
  $tentacgia = mysqli_real_escape_string($ketnoi, $_POST['tentacgia']);
  $ghichu    = mysqli_real_escape_string($ketnoi, $_POST['ghichu']);

  $sql = "INSERT INTO tacgia (tentacgia, ghichu) VALUES ('$tentacgia', '$ghichu')";
  if (mysqli_query($ketnoi, $sql)) {
    echo "<script>
      localStorage.setItem('author_message', JSON.stringify({text:'✅ Thêm tác giả thành công!', type:'success'}));
      window.location='index.php?page_layout=danhsachtacgia';
    </script>";
  } else {
    echo "<script>showToast('❌ Lỗi khi thêm tác giả!','error');</script>";
  }
}
?>

<div class="container mt-5" style="max-width:700px;">
  <div class="card shadow-lg border-0" style="border-radius:16px;">
    <div class="card-header text-white" style="background:linear-gradient(90deg,#1e3a8a,#2563eb);">
      <h4 class="mb-0"><i class="bx bx-user-plus"></i> Thêm tác giả mới</h4>
    </div>

    <div class="card-body p-4">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label fw-semibold">Tên tác giả</label>
          <input type="text" name="tentacgia" class="form-control form-control-lg" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Ghi chú</label>
          <textarea name="ghichu" rows="3" class="form-control form-control-lg"></textarea>
        </div>

        <div class="d-flex justify-content-between">
          <button type="submit" name="add_author" class="btn btn-success px-5"><i class="bx bx-save"></i> Lưu</button>
          <a href="index.php?page_layout=danhsachtacgia" class="btn btn-outline-secondary px-5"><i class="bx bx-arrow-back"></i> Quay lại</a>
        </div>
      </form>
    </div>
  </div>
</div>
