<?php
require_once('ketnoi.php');

// Lấy id mượn cần sửa
if (!isset($_GET['idmuon'])) {
  echo "<script>alert('❌ Không xác định được phiếu mượn!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
  exit;
}
$idmuon = $_GET['idmuon'];

// Lấy dữ liệu hiện tại
$query = "SELECT * FROM muonsach WHERE idmuon = '$idmuon'";
$result = mysqli_query($ketnoi, $query);
$muon = mysqli_fetch_assoc($result);

if (!$muon) {
  echo "<script>alert('❌ Không tìm thấy phiếu mượn!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
  exit;
}

// Cập nhật khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $idnguoidung = $_POST['idnguoidung'];
  $idsach = $_POST['idsach'];
  $ngaymuon = $_POST['ngaymuon'];
  $hantra = $_POST['hantra'];
  $ngaytra_thucte = !empty($_POST['ngaytra_thucte']) ? $_POST['ngaytra_thucte'] : NULL;

  $sql = "UPDATE muonsach 
          SET idnguoidung='$idnguoidung', idsach='$idsach', ngaymuon='$ngaymuon', hantra='$hantra', ngaytra_thucte=" . 
          ($ngaytra_thucte ? "'$ngaytra_thucte'" : "NULL") . " 
          WHERE idmuon='$idmuon'";

  if (mysqli_query($ketnoi, $sql)) {
    echo "<script>alert('✅ Cập nhật phiếu mượn thành công!'); window.location='index.php?page_layout=danhsachmuonsach';</script>";
  } else {
    echo "<script>alert('❌ Cập nhật thất bại!');</script>";
  }
}

// Lấy danh sách người dùng và sách
$nguoidung = mysqli_query($ketnoi, "SELECT * FROM nguoidung");
$sach = mysqli_query($ketnoi, "SELECT * FROM sach");
?>

<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-header bg-warning text-dark">
      <h5 class="mb-0"><i class="bx bx-edit"></i> Sửa phiếu mượn</h5>
    </div>

    <div class="card-body">
      <form method="POST">
        <div class="mb-3">
          <label class="form-label">Người mượn</label>
          <select name="idnguoidung" class="form-select" required>
            <option value="">-- Chọn người mượn --</option>
            <?php while ($row = mysqli_fetch_assoc($nguoidung)) { ?>
              <option value="<?= $row['idnguoidung'] ?>" <?= $row['idnguoidung'] == $muon['idnguoidung'] ? 'selected' : '' ?>>
                <?= $row['hoten'] ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Sách</label>
          <select name="idsach" class="form-select" required>
            <option value="">-- Chọn sách --</option>
            <?php while ($row = mysqli_fetch_assoc($sach)) { ?>
              <option value="<?= $row['idsach'] ?>" <?= $row['idsach'] == $muon['idsach'] ? 'selected' : '' ?>>
                <?= $row['tensach'] ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="row">
          <div class="col-md-4 mb-3">
            <label class="form-label">Ngày mượn</label>
            <input type="date" name="ngaymuon" class="form-control" value="<?= $muon['ngaymuon'] ?>" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Hạn trả</label>
            <input type="date" name="hantra" class="form-control" value="<?= $muon['hantra'] ?>" required>
          </div>
          <div class="col-md-4 mb-3">
            <label class="form-label">Ngày trả thực tế</label>
            <input type="date" name="ngaytra_thucte" class="form-control" value="<?= $muon['ngaytra_thucte'] ?>">
          </div>
        </div>

        <div class="d-flex justify-content-between">
          <a href="index.php?page_layout=danhsachmuonsach" class="btn btn-secondary"><i class="bx bx-arrow-back"></i> Trở lại</a>
          <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Lưu thay đổi</button>
        </div>
      </form>
    </div>
  </div>
</div>
