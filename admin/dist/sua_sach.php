<?php
require_once('ketnoi.php');
$id = (int)$_GET['idsach'];
$sach = mysqli_fetch_assoc(mysqli_query($ketnoi, "SELECT * FROM sach WHERE idsach=$id"));
$tacgia = mysqli_query($ketnoi, "SELECT * FROM tacgia");
$loaisach = mysqli_query($ketnoi, "SELECT * FROM loaisach");

if (isset($_POST['update_sach'])) {
  $tensach = $_POST['tensach'];
  $soluong = $_POST['soluong'];
  $dongia = $_POST['dongia'];
  $mota = $_POST['mota'];
  $idtacgia = $_POST['idtacgia'];
  $idloaisach = $_POST['idloaisach'];
  $hinhanh = $sach['hinhanhsach'];

  if (!empty($_FILES['hinhanhsach']['name'])) {
    $filename = time() . '_' . $_FILES['hinhanhsach']['name'];
    move_uploaded_file($_FILES['hinhanhsach']['tmp_name'], "../../feane/images/$filename");
    $hinhanh = $filename;
  }

  $sql = "UPDATE sach SET tensach='$tensach', soluong=$soluong, dongia=$dongia, mota='$mota',
          idtacgia=$idtacgia, idloaisach=$idloaisach, hinhanhsach='$hinhanh'
          WHERE idsach=$id";

  if (mysqli_query($ketnoi, $sql)) {
    echo "<script>showToast('✅ Cập nhật sách thành công!','success');setTimeout(()=>window.location='index.php?page_layout=danhsachsach',1500);</script>";
  } else {
    echo "<script>showToast('❌ Lỗi khi cập nhật!','danger');</script>";
  }
}
?>

<div class="container mt-4">
  <div class="card shadow-sm border-0">
    <div class="card-header bg-warning text-dark">
      <h4 class="mb-0"><i class="bx bx-edit"></i> Chỉnh sửa sách</h4>
    </div>
    <div class="card-body">
      <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label fw-bold">Tên sách</label>
          <input type="text" name="tensach" class="form-control" value="<?= htmlspecialchars($sach['tensach']); ?>" required>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Số lượng</label>
            <input type="number" name="soluong" class="form-control" value="<?= $sach['soluong']; ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Đơn giá</label>
            <input type="number" name="dongia" class="form-control" value="<?= $sach['dongia']; ?>" required>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Mô tả</label>
          <textarea name="mota" class="form-control"><?= htmlspecialchars($sach['mota']); ?></textarea>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">Tác giả</label>
            <select name="idtacgia" class="form-select">
              <?php while ($tg = mysqli_fetch_assoc($tacgia)) { ?>
                <option value="<?= $tg['idtacgia']; ?>" <?= $tg['idtacgia']==$sach['idtacgia']?'selected':''; ?>><?= htmlspecialchars($tg['tentacgia']); ?></option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-bold">Thể loại</label>
            <select name="idloaisach" class="form-select">
              <?php while ($ls = mysqli_fetch_assoc($loaisach)) { ?>
                <option value="<?= $ls['idloaisach']; ?>" <?= $ls['idloaisach']==$sach['idloaisach']?'selected':''; ?>><?= htmlspecialchars($ls['tenloaisach']); ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Ảnh bìa hiện tại</label><br>
          <?php if ($sach['hinhanhsach']) { ?>
            <img src="../../feane/images/<?= $sach['hinhanhsach']; ?>" width="100" class="rounded shadow-sm mb-2">
          <?php } ?>
          <input type="file" name="hinhanhsach" class="form-control">
        </div>
        <div class="d-flex justify-content-between">
          <button type="submit" name="update_sach" class="btn btn-warning px-4"><i class="bx bx-save"></i> Lưu thay đổi</button>
          <a href="index.php?page_layout=danhsachsach" class="btn btn-secondary px-4"><i class="bx bx-arrow-back"></i> Quay lại</a>
        </div>
      </form>
    </div>
  </div>
</div>
