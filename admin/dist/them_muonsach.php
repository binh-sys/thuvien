<?php
require_once('ketnoi.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $idnguoidung = $_POST['idnguoidung'];
  $idsach = $_POST['idsach'];
  $ngaymuon = $_POST['ngaymuon'];
  $hantra = $_POST['hantra'];

  $sql = "INSERT INTO muonsach (idnguoidung, idsach, ngaymuon, hantra) 
          VALUES ('$idnguoidung', '$idsach', '$ngaymuon', '$hantra')";
  if (mysqli_query($ketnoi, $sql)) {
    echo "<script>alert('✅ Thêm phiếu mượn thành công!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
  } else {
    echo "<script>alert('❌ Lỗi khi thêm!');</script>";
  }
}

$nguoidung = mysqli_query($ketnoi, "SELECT * FROM nguoidung");
$sach = mysqli_query($ketnoi, "SELECT * FROM sach");
?>

<div class="container mt-4">
  <div class="card shadow">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0"><i class='bx bx-plus'></i> Thêm phiếu mượn</h5>
    </div>
    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Người mượn</label>
          <select name="idnguoidung" class="form-select" required>
            <option value="">-- Chọn người mượn --</option>
            <?php while ($row = mysqli_fetch_assoc($nguoidung)) { ?>
              <option value="<?= $row['idnguoidung'] ?>"><?= $row['hoten'] ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Sách mượn</label>
          <select name="idsach" class="form-select" required>
            <option value="">-- Chọn sách --</option>
            <?php while ($row = mysqli_fetch_assoc($sach)) { ?>
              <option value="<?= $row['idsach'] ?>"><?= $row['tensach'] ?></option>
            <?php } ?>
          </select>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label">Ngày mượn</label>
            <input type="date" name="ngaymuon" class="form-control" required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label">Hạn trả</label>
            <input type="date" name="hantra" class="form-control" required>
          </div>
        </div>

        <button type="submit" class="btn btn-success"><i class="bx bx-save"></i> Lưu</button>
        <a href="index.php?page_layout=danhsachmuonsach" class="btn btn-secondary">Hủy</a>
      </form>
    </div>
  </div>
</div>
