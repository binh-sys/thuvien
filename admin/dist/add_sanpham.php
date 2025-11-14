<?php
require_once('ketnoi.php');

$iddonhang = $_GET['iddonhang'];

// Lấy danh sách sách
$sqlSach = "SELECT idsach, tensach, dongia, hinhanhsach FROM sach ORDER BY tensach ASC";
$listSach = mysqli_query($ketnoi, $sqlSach);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $idsach = $_POST['idsach'];
  $soluong = $_POST['soluong'];

  // Lấy giá sách
  $sach = mysqli_fetch_assoc(mysqli_query($ketnoi, "SELECT dongia FROM sach WHERE idsach = $idsach"));
  $dongia = $sach['dongia'];

  $thanhtien = $dongia * $soluong;

  // Lưu vào chi tiết đơn hàng
  $sqlInsert = "INSERT INTO donhang_chitiet (iddonhang, idsach, soluong, dongia, thanhtien)
                VALUES ('$iddonhang', '$idsach', '$soluong', '$dongia', '$thanhtien')";

  mysqli_query($ketnoi, $sqlInsert);

  // Cập nhật tổng tiền
  mysqli_query($ketnoi, "
    UPDATE donhang 
    SET tongtien = (SELECT SUM(thanhtien) FROM donhang_chitiet WHERE iddonhang = $iddonhang)
    WHERE iddonhang = $iddonhang
  ");

  header("Location: index.php?page_layout=xem_donhang&iddonhang=$iddonhang");
  exit;
}
?>

<style>
  .card { border-radius: 18px; }
</style>

<div class="container mt-4">
  <div class="card shadow border-0">

    <div class="card-header text-white"
         style="background: linear-gradient(135deg,#00bfa5,#009688);">
      <h4 class="mb-0"><i class="bx bx-plus"></i> Thêm sách vào đơn hàng</h4>
    </div>

    <div class="card-body">

      <form method="POST">

        <label class="form-label fw-bold">Chọn sách:</label>
        <select name="idsach" id="idsach" class="form-select mb-3" required onchange="updatePrice()">
          <option value="">-- Chọn sách --</option>
          <?php while ($s = mysqli_fetch_assoc($listSach)) { ?>
            <option value="<?= $s['idsach']; ?>" data-dongia="<?= $s['dongia']; ?>">
              <?= $s['tensach']; ?> — <?= number_format($s['dongia']); ?> đ
            </option>
          <?php } ?>
        </select>

        <label class="form-label fw-bold">Số lượng:</label>
        <input type="number" name="soluong" id="soluong" class="form-control mb-3" 
               min="1" value="1" required oninput="updateTotal()">

        <label class="form-label fw-bold">Đơn giá:</label>
        <input type="text" id="dongia" class="form-control mb-3" disabled>

        <label class="form-label fw-bold">Thành tiền:</label>
        <input type="text" id="thanhtien" class="form-control mb-4" disabled>

        <button class="btn btn-success px-4"><i class='bx bx-save'></i> Thêm vào đơn</button>
        <a href="index.php?page_layout=xem_donhang&iddonhang=<?= $iddonhang; ?>" 
           class="btn btn-secondary px-4">Hủy</a>

      </form>

    </div>
  </div>
</div>

<script>
function updatePrice() {
  let select = document.getElementById("idsach");
  let price = select.options[select.selectedIndex].getAttribute("data-dongia");
  document.getElementById("dongia").value = Number(price).toLocaleString() + " đ";
  updateTotal();
}

function updateTotal() {
  let select = document.getElementById("idsach");
  let price = select.options[select.selectedIndex]?.getAttribute("data-dongia") || 0;
  let qty = document.getElementById("soluong").value;
  let total = price * qty;
  document.getElementById("thanhtien").value = Number(total).toLocaleString() + " đ";
}
</script>
