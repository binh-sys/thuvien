<?php
require_once('ketnoi.php');

// Thêm đơn hàng
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $idnguoidung = $_POST['idnguoidung'];
    $trangthai = $_POST['trangthai'];
    $tongtien = $_POST['tongtien'];

    // 1) Tạo đơn hàng trước
    $sql = "INSERT INTO donhang(idnguoidung, tongtien, trangthai, ngaydat)
            VALUES ('$idnguoidung', '$tongtien', '$trangthai', NOW())";
    mysqli_query($ketnoi, $sql);

    $newOrderId = mysqli_insert_id($ketnoi);

    // 2) Thêm chi tiết đơn hàng
    if (isset($_POST['idsach'])) {
        foreach ($_POST['idsach'] as $index => $idsach) {

            $soluong = $_POST['soluong'][$index];
            $dongia = $_POST['dongia'][$index];
            $thanhtien = $soluong * $dongia;

            mysqli_query($ketnoi, "
                INSERT INTO donhang_chitiet(iddonhang, idsach, soluong, dongia, thanhtien)
                VALUES ('$newOrderId', '$idsach', '$soluong', '$dongia', '$thanhtien')
            ");
        }
    }

    header("Location: index.php?page_layout=danhsachdonhang");
    exit;
}

// Lấy danh sách khách hàng
$resultUser = mysqli_query($ketnoi, "SELECT * FROM nguoidung ORDER BY hoten ASC");

// Lấy danh sách sách
$resultSach = mysqli_query($ketnoi, "SELECT * FROM sach ORDER BY tensach ASC");
?>

<style>
  .product-img { width: 60px; height: 80px; object-fit: cover; border-radius: 6px; }
  .btn-add { background-color:#009688; color:white; }
  .btn-add:hover { background-color:#00796b; }
</style>

<div class="container mt-4">
  <div class="card shadow border-0">

    <div class="card-header text-white"
         style="background: linear-gradient(135deg,#00bfa5,#009688);">
      <h4 class="mb-0"><i class='bx bx-cart-add'></i> Tạo đơn hàng mới</h4>
    </div>

    <div class="card-body">

      <form method="POST" id="orderForm">

        <!-- CHỌN KHÁCH HÀNG -->
        <label class="form-label fw-bold">Khách hàng:</label>
        <select name="idnguoidung" class="form-select mb-3" required>
          <option value="">-- Chọn khách hàng --</option>
          <?php while($u = mysqli_fetch_assoc($resultUser)) { ?>
            <option value="<?= $u['idnguoidung']; ?>"><?= $u['hoten']; ?></option>
          <?php } ?>
        </select>

        <!-- THÊM SẢN PHẨM -->
        <label class="form-label fw-bold">Thêm sản phẩm:</label>
        <div class="d-flex gap-2 mb-3">
          <select id="chonSach" class="form-select">
            <option value="">-- Chọn sách --</option>
            <?php while($s = mysqli_fetch_assoc($resultSach)) { ?>
              <option value="<?= $s['idsach']; ?>"
                data-ten="<?= $s['tensach']; ?>"
                data-anh="<?= $s['hinhanhsach']; ?>"
                data-gia="<?= $s['dongia']; ?>">
                <?= $s['tensach']; ?> (<?= number_format($s['dongia']); ?> đ)
              </option>
            <?php } ?>
          </select>

          <button type="button" class="btn btn-add" onclick="addProduct()">
            <i class='bx bx-plus'></i> Thêm
          </button>
        </div>

        <!-- DANH SÁCH SẢN PHẨM -->
        <table class="table table-bordered text-center">
          <thead class="table-light">
            <tr>
              <th>Hình</th>
              <th>Tên sách</th>
              <th>Giá</th>
              <th>Số lượng</th>
              <th>Thành tiền</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="productList"></tbody>
        </table>

        <!-- TỔNG TIỀN -->
        <h5 class="text-end mt-3">
          <strong>Tổng tiền: </strong>
          <span id="tongTienText">0</span> đ
        </h5>

        <input type="hidden" name="tongtien" id="tongTienInput" value="0">

        <!-- TRẠNG THÁI -->
        <label class="form-label fw-bold mt-3">Trạng thái:</label>
        <select name="trangthai" class="form-select mb-4">
          <option value="cho_duyet">Chờ duyệt</option>
          <option value="dang_giao">Đang giao</option>
          <option value="hoan_thanh">Hoàn thành</option>
          <option value="da_huy">Đã hủy</option>
        </select>

        <button class="btn btn-success px-4"><i class='bx bx-save'></i> Lưu đơn hàng</button>
        <a href="index.php?page_layout=danhsachdonhang" class="btn btn-secondary px-4">Hủy</a>

      </form>

    </div>
  </div>
</div>

<script>
let rowId = 0;

function addProduct() {
    let select = document.getElementById("chonSach");
    let option = select.options[select.selectedIndex];
    if (!option.value) return;

    let id = option.value;
    let ten = option.getAttribute("data-ten");
    let anh = option.getAttribute("data-anh");
    let gia = option.getAttribute("data-gia");

    rowId++;

    let html = `
    <tr id="row${rowId}">
        <td><img src="uploads/${anh}" class="product-img"></td>
        <td>${ten}</td>

        <td>${Number(gia).toLocaleString()} đ
            <input type="hidden" name="dongia[]" value="${gia}">
            <input type="hidden" name="idsach[]" value="${id}">
        </td>

        <td>
            <input type="number" name="soluong[]" class="form-control"
                   value="1" min="1" oninput="updateTotal()">
        </td>

        <td class="thanhtien">${Number(gia).toLocaleString()} đ</td>

        <td>
            <button type="button" class="btn btn-danger btn-sm"
                    onclick="removeRow(${rowId})">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    </tr>`;

    document.getElementById("productList").insertAdjacentHTML("beforeend", html);

    updateTotal();
}

function removeRow(id) {
    document.getElementById("row" + id).remove();
    updateTotal();
}

function updateTotal() {
    let rows = document.querySelectorAll("#productList tr");
    let tong = 0;

    rows.forEach(r => {
        let gia = r.querySelector("input[name='dongia[]']").value;
        let sl = r.querySelector("input[name='soluong[]']").value;
        let thanh = gia * sl;

        r.querySelector(".thanhtien").innerHTML = thanh.toLocaleString() + " đ";
        tong += thanh;
    });

    document.getElementById("tongTienText").innerHTML = tong.toLocaleString();
    document.getElementById("tongTienInput").value = tong;
}
</script>
