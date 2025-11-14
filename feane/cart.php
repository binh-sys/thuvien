<?php
require_once('ketnoi.php');
session_start();

// Lấy danh sách thể loại & tác giả
$loaisach = mysqli_query($ketnoi, "SELECT * FROM loaisach ORDER BY tenloaisach ASC");
$tacgia = mysqli_query($ketnoi, "SELECT * FROM tacgia ORDER BY tentacgia ASC");

// Bộ lọc
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$idloaisach = isset($_GET['idloaisach']) ? intval($_GET['idloaisach']) : 0;
$idtacgia = isset($_GET['idtacgia']) ? intval($_GET['idtacgia']) : 0;
$new = isset($_GET['new']);
$featured = isset($_GET['featured']);

// --- Truy vấn sách ---
$sql = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia 
        FROM sach
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
        LEFT JOIN tacgia ON sach.idtacgia = tacgia.idtacgia
        WHERE 1=1";

if ($keyword !== '') {
  $kw = mysqli_real_escape_string($ketnoi, $keyword);
  $sql .= " AND (sach.tensach COLLATE utf8mb4_vietnamese_ci LIKE '%$kw%' 
             OR tacgia.tentacgia COLLATE utf8mb4_vietnamese_ci LIKE '%$kw%' 
             OR loaisach.tenloaisach COLLATE utf8mb4_vietnamese_ci LIKE '%$kw%')";
}

if ($idloaisach > 0) $sql .= " AND sach.idloaisach = $idloaisach";
if ($idtacgia > 0)   $sql .= " AND sach.idtacgia = $idtacgia";

if ($new) {
  $sql .= " AND sach.ngaynhap >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
  $sql .= " ORDER BY sach.ngaynhap DESC";
} 
elseif ($featured) {
  $sql .= " AND sach.luotmuon >= 10";
  $sql .= " ORDER BY sach.luotmuon DESC";
} 
else {
  $sql .= " ORDER BY sach.tensach ASC";
}

$books = mysqli_query($ketnoi, $sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mua sách - Thư Viện Trường Học</title>

  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link href="css/header.css" rel="stylesheet">
  <link rel="stylesheet" href="css/menu.css">
  <link href="css/footer.css" rel="stylesheet">
</head>

<body class="menu-page">
  <?php include 'header.php'; ?>

  <!-- DANH SÁCH THỂ LOẠI -->
  <div class="container">
    <div class="category-scroll">
      <ul class="filters_menu">

        <li class="<?= ($idloaisach == 0 && !$new && !$featured) ? 'active' : ''; ?>">
          <a href="shop.php" class="filter-link">Tất cả</a>
        </li>

        <li class="<?= ($new) ? 'active' : ''; ?>">
          <a href="shop.php?new=1" class="filter-link">Sách mới về</a>
        </li>

        <li class="<?= ($featured) ? 'active' : ''; ?>">
          <a href="shop.php?featured=1" class="filter-link">Sách nổi bật</a>
        </li>

        <?php
        mysqli_data_seek($loaisach, 0);
        while ($row = mysqli_fetch_assoc($loaisach)) {
          $active = ($idloaisach == $row['idloaisach']) ? 'active' : '';
          echo '<li class="' . $active . '">
                  <a href="shop.php?idloaisach=' . $row['idloaisach'] . '" class="filter-link">'
                  . htmlspecialchars($row['tenloaisach']) .
                '</a></li>';
        }
        ?>
      </ul>
    </div>
  </div>

  <!-- DANH SÁCH SÁCH -->
  <section>
    <div class="container">
      <div class="row">

        <?php while ($r = mysqli_fetch_assoc($books)) {
          $img = 'images/' . $r['hinhanhsach'];
        ?>
          <div class="col-sm-6 col-lg-4 mb-4">
            <div class="box">

              <!-- Checkbox chọn nhiều sách để mua -->
              <input type="checkbox" class="select-book" value="<?= $r['idsach']; ?>"
                style="position:absolute; top:10px; left:10px; z-index:10; transform:scale(1.5); cursor:pointer;">

              <div class="img-box">
                <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($r['tensach']); ?>">
              </div>

              <div class="detail-box">
                <h5 class="fw-bold"><?= htmlspecialchars($r['tensach']); ?></h5>
                <p class="text-muted small"><?= htmlspecialchars($r['tentacgia']); ?></p>
                <p class="text-secondary small"><?= htmlspecialchars($r['tenloaisach']); ?></p>

                <div class="options d-flex justify-content-center gap-2 flex-wrap">

                  <!-- CHI TIẾT ĐƠN HÀNG -->
                  <a href="chitiet_donhang.php?idsach=<?= $r['idsach']; ?>" 
                     class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fa fa-info-circle"></i> Chi tiết đơn hàng
                  </a>

                  <!-- MUA NGAY -->
                  <a href="muasach.php?idsach=<?= $r['idsach']; ?>" 
                     class="btn btn-success rounded-pill px-3">
                    <i class="fa fa-shopping-cart"></i> Mua ngay
                  </a>

                  <!-- THÊM GIỎ HÀNG -->
                  <a href="javascript:void(0);" 
                     class="btn btn-outline-danger rounded-pill px-3 cart-btn"
                     data-id="<?= $r['idsach']; ?>">
                    <i class="fa fa-heart"></i> Thêm vào giỏ
                  </a>

                </div>
              </div>

            </div>
          </div>
        <?php } ?>

      </div>

      <!-- NÚT MUA NHIỀU SÁCH -->
      <div class="text-center mb-4">
        <button id="buy-selected" class="btn btn-success fw-bold px-4 py-2 rounded-pill">
          <i class="fa fa-shopping-basket me-2"></i> Mua sách đã chọn
        </button>
      </div>
    </div>
  </section>


  <!-- SCRIPT -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script>
    $(document).ready(function() {

      const selectedBooks = new Set();

      // Khi tick chọn sách
      $(document).on("change", ".select-book", function() {
        const id = $(this).val();
        if (this.checked) selectedBooks.add(id);
        else selectedBooks.delete(id);

        if (selectedBooks.size > 0) {
          $("#buy-selected").addClass("show");
        } else {
          $("#buy-selected").removeClass("show");
        }
      });

      // Mua sách đã chọn
      $("#buy-selected").on("click", function() {
        if (selectedBooks.size === 0) return;
        const ids = Array.from(selectedBooks).join(",");
        window.location.href = "muasach.php?ids=" + encodeURIComponent(ids);
      });

      // THÊM GIỎ HÀNG
      $(document).on("click", ".cart-btn", function() {
        const idsach = $(this).data("id");

        $.ajax({
          url: "xuly_giohang.php",
          type: "POST",
          data: { idsach: idsach },
          dataType: "json",
          success: function(res) {
            alert(res.message);
          }
        });
      });

    });
  </script>

  <?php include 'footer.php'; ?>

  <script src="js/bootstrap.js"></script>

</body>
</html>
