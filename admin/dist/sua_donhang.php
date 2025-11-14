<?php
require_once('ketnoi.php');

$iddonhang = $_GET['iddonhang'];

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM donhang WHERE iddonhang = $iddonhang";
$result = mysqli_query($ketnoi, $sql);
$row = mysqli_fetch_assoc($result);

// Lấy danh sách khách hàng
$resultUser = mysqli_query($ketnoi, "SELECT * FROM nguoidung ORDER BY hoten ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idnguoidung = $_POST['idnguoidung'];
    $tongtien = $_POST['tongtien'];
    $trangthai = $_POST['trangthai'];

    $sqlUpdate = "UPDATE donhang SET 
                  idnguoidung='$idnguoidung',
                  tongtien='$tongtien',
                  trangthai='$trangthai'
                  WHERE iddonhang=$iddonhang";

    mysqli_query($ketnoi, $sqlUpdate);

    header("Location: index.php?page_layout=danhsachdonhang");
    exit;
}
?>

<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-header text-white" style="background: linear-gradient(135deg,#00bfa5,#009688);">
      <h4 class="mb-0"><i class='bx bx-edit'></i> Chỉnh sửa đơn hàng</h4>
    </div>

    <div class="card-body">
      <form method="POST">

        <label class="form-label fw-bold">Khách hàng:</label>
        <select name="idnguoidung" class="form-select mb-3" required>
          <?php while($u = mysqli_fetch_assoc($resultUser)) { ?>
            <option value="<?= $u['idnguoidung']; ?>" 
              <?= ($u['idnguoidung'] == $row['idnguoidung']) ? 'selected' : '' ?>>
              <?= $u['hoten']; ?>
            </option>
          <?php } ?>
        </select>

        <label class="form-label fw-bold">Tổng tiền:</label>
        <input type="number" name="tongtien" value="<?= $row['tongtien']; ?>" 
               class="form-control mb-3" required>

        <label class="form-label fw-bold">Trạng thái:</label>
        <select name="trangthai" class="form-select mb-3">
          <option value="cho_duyet" <?= $row['trangthai']=='cho_duyet'?'selected':'' ?>>Chờ duyệt</option>
          <option value="dang_giao" <?= $row['trangthai']=='dang_giao'?'selected':'' ?>>Đang giao</option>
          <option value="hoan_thanh" <?= $row['trangthai']=='hoan_thanh'?'selected':'' ?>>Hoàn thành</option>
          <option value="da_huy" <?= $row['trangthai']=='da_huy'?'selected':'' ?>>Đã hủy</option>
        </select>

        <button class="btn btn-success px-4"><i class='bx bx-save'></i> Cập nhật</button>
        <a href="index.php?page_layout=danhsachdonhang" class="btn btn-secondary px-4">Hủy</a>

      </form>
    </div>
  </div>
</div>
