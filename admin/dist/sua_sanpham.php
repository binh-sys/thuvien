<?php
require_once('ketnoi.php');

$id = $_GET['id']; // id chi tiết đơn hàng

// Lấy thông tin chi tiết sản phẩm
$sql = "SELECT donhang_chitiet.*, sach.tensach, sach.hinhanhsach, sach.dongia AS gia_goc
        FROM donhang_chitiet
        JOIN sach ON donhang_chitiet.idsach = sach.idsach
        WHERE donhang_chitiet.id = $id";

$ct = mysqli_fetch_assoc(mysqli_query($ketnoi, $sql));
$iddonhang = $ct['iddonhang'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $soluong = $_POST['soluong'];
  $dongia = $ct['gia_goc'];
  $thanhtien = $soluong * $dongia;

  // Cập nhật chi tiết đơn
  mysqli_query($ketnoi, "
    UPDATE donhang_chitiet 
    SET soluong='$soluong', dongia='$dongia', thanhtien='$thanhtien'
    WHERE id=$id
  ");

  // Cập nhật tổng tiền đơn hàng
  mysqli_query($ketnoi, "
    UPDATE donhang
    SET tongtien = (SELECT SUM(thanhtien) FROM donhang_chitiet WHERE iddonhang=$iddonhang)
    WHERE iddonhang=$iddonhang
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
         style="background:linear-gradient(135deg,#00bfa5,#009688);">
      <h4 class="mb-0"><i class="bx bx-edit"></i> Sửa sản phẩm trong đơn</h4>
    </div>

    <div class="card-body">

      <form method="POST">

        <label class="form-label fw-bold">Tên sách:</label>
        <input type="text" class="form-control mb-3"
               value="<?= $ct['tensach']; ?>" disabled>

        <label class="form-label fw-bold">Số lượng:</label>
        <input type="number" name="soluong" id="soluong"
               min="1" value="<?= $ct['soluong']; ?>"
               class="form-control mb-3" oninput="updateTotal()">

        <label class="form-label fw-bold">Đơn giá:</label>
        <input type="text" id="dongia"
               class="form-control mb-3" 
               value="<?= number_format($ct['gia_goc']); ?> đ" disabled>

        <label class="form-label fw-bold">Thành tiền:</label>
        <input type="text" id="thanhtien"
               class="form-control mb-4"
               value="<?= number_format($ct['thanhtien']); ?> đ" disabled>

        <button class="btn btn-success px-4"><i class='bx bx-save'></i> Cập nhật</button>
        <a href="index.php?page_layout=xem_donhang&iddonhang=<?= $iddonhang; ?>" 
           class="btn btn-secondary px-4">Hủy</a>

      </form>

    </div>
  </div>
</div>

<script>
function updateTotal() {
  let qty = document.getElementById("soluong").value;
  let price = <?= $ct['gia_goc']; ?>;
  document.getElementById("thanhtien").value = Number(price * qty).toLocaleString() + " đ";
}
</script>
