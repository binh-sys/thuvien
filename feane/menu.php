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

// Câu truy vấn sách
$sql = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia 
        FROM sach
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
        LEFT JOIN tacgia ON sach.idtacgia = tacgia.idtacgia
        WHERE 1=1";

if ($keyword !== '') {
  $kw = mysqli_real_escape_string($ketnoi, $keyword);
  $sql .= " AND (sach.tensach LIKE '%$kw%' OR tacgia.tentacgia LIKE '%$kw%' OR loaisach.tenloaisach LIKE '%$kw%')";
}
if ($idloaisach > 0) {
  $sql .= " AND sach.idloaisach = $idloaisach";
}
if ($idtacgia > 0) {
  $sql .= " AND sach.idtacgia = $idtacgia";
}

$sql .= " ORDER BY sach.tensach ASC";
$books = mysqli_query($ketnoi, $sql);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kho sách - Thư Viện Trường Học</title>
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
  <?php
  // --- Lấy danh sách thể loại & tác giả ---
  $loaisach = mysqli_query($ketnoi, "SELECT * FROM loaisach ORDER BY tenloaisach ASC");
  $tacgia = mysqli_query($ketnoi, "SELECT * FROM tacgia ORDER BY tentacgia ASC");

  // --- Bộ lọc ---
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
    $sql .= " AND (sach.tensach LIKE '%$kw%' 
             OR tacgia.tentacgia LIKE '%$kw%' 
             OR loaisach.tenloaisach LIKE '%$kw%')";
  }

  if ($idloaisach > 0) {
    $sql .= " AND sach.idloaisach = $idloaisach";
  }

  if ($idtacgia > 0) {
    $sql .= " AND sach.idtacgia = $idtacgia";
  }

  // ✅ Sách mới trong 30 ngày gần nhất
  if ($new) {
    $sql .= " AND sach.ngaynhap >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
    $sql .= " ORDER BY sach.ngaynhap DESC";
  }
  // ✅ Sách nổi bật (được mượn nhiều)
  elseif ($featured) {
    $sql .= " AND sach.luotmuon >= 10";
    $sql .= " ORDER BY sach.luotmuon DESC";
  }
  // ✅ Mặc định: hiển thị toàn bộ theo tên
  else {
    $sql .= " ORDER BY sach.tensach ASC";
  }

  $books = mysqli_query($ketnoi, $sql);

  ?>
  
  <!-- Danh Sách thể loại -->
  <div class="container">
    <ul class="filters_menu">
      <li class="<?= ($idloaisach == 0 && !$new && !$featured) ? 'active' : ''; ?>">
        <a href="menu.php" class="filter-link">Tất cả</a>
      </li>
      <li class="<?= ($new) ? 'active' : ''; ?>">
        <a href="menu.php?new=1" class="filter-link">Sách mới về</a>
      </li>
      <li class="<?= ($featured) ? 'active' : ''; ?>">
        <a href="menu.php?featured=1" class="filter-link">Sách nổi bật</a>
      </li>

      <?php mysqli_data_seek($loaisach, 0); ?>
      <?php while ($row = mysqli_fetch_assoc($loaisach)) {
        $active = ($idloaisach == $row['idloaisach']) ? 'active' : '';
      ?>
        <li class="<?= $active; ?>">
          <a href="menu.php?idloaisach=<?= $row['idloaisach']; ?>" class="filter-link">
            <?= htmlspecialchars($row['tenloaisach']); ?>
          </a>
        </li>
      <?php } ?>
    </ul>
  </div>

  <!-- PHẦN DANH SÁCH SÁCH -->
  <section>
    <div class="container">
      <!-- DANH SÁCH SÁCH -->
      <div class="row">
        <?php while ($r = mysqli_fetch_assoc($books)) {
          $img = 'images/' . $r['hinhanhsach'];
        ?>
          <div class="col-sm-6 col-lg-4 mb-4">
            <div class="box">
              <!-- Checkbox chọn nhiều -->
              <input type="checkbox" class="select-book" value="<?= $r['idsach']; ?>"
                style="position:absolute; top:10px; left:10px; z-index:10; transform:scale(1.5); cursor:pointer;">
              <div class="img-box position-relative">
                <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($r['tensach']); ?>">
              </div>
              <div class="detail-box">
                <h5 class="fw-bold text-truncate"><?= htmlspecialchars($r['tensach']); ?></h5>
                <p class="text-muted small mb-2"><?= htmlspecialchars($r['tentacgia']); ?></p>
                <h6 class="text-secondary small mb-3"><?= htmlspecialchars($r['tenloaisach']); ?></h6>

                <div class="options d-flex justify-content-center gap-2 flex-wrap">
                  <a href="chitietsach.php?idsach=<?= $r['idsach']; ?>" class="btn btn-outline-primary rounded-pill px-3">
                    <i class="fa fa-info-circle me-1"></i> Chi tiết
                  </a>
                  <a href="book.php?idsach=<?= $r['idsach']; ?>" class="btn btn-warning rounded-pill px-3">
                    <i class="fa fa-book me-1"></i> Mượn
                  </a>
                  <a href="javascript:void(0);"
                    class="btn btn-outline-danger rounded-pill px-3 favorite-btn <?= in_array($r['idsach'], $_SESSION['favorites'] ?? []) ? 'liked' : ''; ?>"
                    data-id="<?= $r['idsach']; ?>">
                    <i class="fa fa-heart me-1"></i> Thích
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
      <div class="text-center mb-4">
        <button id="borrow-selected" class="btn btn-warning fw-bold px-4 py-2 rounded-pill">
          <i class="fa fa-book me-2"></i> Mượn sách đã chọn
        </button>
      </div>
    </div>
  </section>


  <!-- SCRIPT -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      const selectedBooks = new Set();

      // Khi tick chọn hoặc bỏ chọn sách
      $(document).on("change", ".select-book", function() {
        const id = $(this).val();

        if (this.checked) selectedBooks.add(id);
        else selectedBooks.delete(id);

        // Hiện hoặc ẩn nút mượn
        if (selectedBooks.size > 0) {
          $("#borrow-selected").addClass("show");
        } else {
          $("#borrow-selected").removeClass("show");
        }
      });

      // Khi nhấn nút "Mượn sách đã chọn"
      $("#borrow-selected").on("click", function() {
        if (selectedBooks.size === 0) return;

        const ids = Array.from(selectedBooks).join(",");
        window.location.href = "book.php?ids=" + encodeURIComponent(ids);
      });
    });
  </script>


  <!-- Footer -->
  <?php include 'footer.php'; ?>

  <!-- Thông báo nhỏ nút yêu thích -->
  <div id="toast-container"></div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    function showToast(message) {
      const toast = $(`
    <div class="toast">
      <i class="fa fa-info-circle"></i>
      <span>${message}</span>
    </div>
  `);
      $("#toast-container").append(toast);
      setTimeout(() => toast.addClass("show"), 100);
      setTimeout(() => {
        toast.removeClass("show");
        setTimeout(() => toast.remove(), 500);
      }, 3000);
    }

    $(document).on("click", ".favorite-btn", function() {
      const btn = $(this);
      const idsach = btn.data("id");

      $.ajax({
        url: "xuly_yeuthich.php",
        type: "POST",
        data: {
          idsach: idsach
        },
        dataType: "json",
        success: function(res) {
          if (res.status === "added") {
            btn.addClass("liked");
            showToast("✅ Đã thêm vào danh sách yêu thích");
          } else if (res.status === "removed") {
            btn.removeClass("liked");
            showToast("💔 Đã xóa khỏi danh sách yêu thích");
          } else if (res.status === "error") {
            showToast(res.message);
          }
        },
        error: function() {
          showToast("⚠️ Lỗi kết nối máy chủ");
        },
      });
    });
  </script>

  <!-- JS -->
  <script>
    const toggleBtn = document.getElementById("userToggle");
    const dropdown = document.getElementById("userDropdown");

    if (toggleBtn && dropdown) {
      toggleBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.classList.toggle("show");
      });

      // Đóng menu khi click ra ngoài
      document.addEventListener("click", (e) => {
        if (!dropdown.contains(e.target) && !toggleBtn.contains(e.target)) {
          dropdown.classList.remove("show");
        }
      });

      // Mở menu khi hover (tùy chọn)
      toggleBtn.addEventListener("mouseenter", () => dropdown.classList.add("show"));
      dropdown.addEventListener("mouseleave", () => dropdown.classList.remove("show"));
    }
  </script>


  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>


</body>

</html>