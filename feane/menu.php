<?php
require_once('ketnoi.php');
session_start();

// Lấy danh sách thể loại & tác giả
$loaisach = mysqli_query($ketnoi, "SELECT * FROM loaisach ORDER BY tenloaisach ASC");
$tacgia = mysqli_query($ketnoi, "SELECT * FROM tacgia ORDER BY tentacgia ASC");

// Bộ lọc
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$idloaisach = isset($_GET['idloaisach']) ? intval($_GET['idloaisach']) : 0;
$matacgia = isset($_GET['matacgia']) ? intval($_GET['matacgia']) : 0;

// Câu truy vấn sách
$sql = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia 
        FROM sach
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
        LEFT JOIN tacgia ON sach.matacgia = tacgia.matacgia
        WHERE 1=1";

if ($keyword !== '') {
  $kw = mysqli_real_escape_string($ketnoi, $keyword);
  $sql .= " AND (sach.tensach LIKE '%$kw%' OR tacgia.tentacgia LIKE '%$kw%' OR loaisach.tenloaisach LIKE '%$kw%')";
}
if ($idloaisach > 0) {
  $sql .= " AND sach.idloaisach = $idloaisach";
}
if ($matacgia > 0) {
  $sql .= " AND sach.matacgia = $matacgia";
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
  <link rel="shortcut icon" href="images/Book.png" type="image/png">
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/menu.css">
  <link href="css/footer.css" rel="stylesheet">
</head>

<body class="sub_page">

  <!-- Header -->
  <?php
  $current_page = basename($_SERVER['PHP_SELF']); // Lấy tên file hiện tại (vd: menu.php)
  ?>
  <header class="header_section">
    <div class="container">
      <nav class="navbar navbar-expand-lg custom_nav-container align-items-center justify-content-between">
        <!-- Logo -->
        <a class="navbar-brand d-flex align-items-center" href="index.php">
          <img src="images/Book.png" alt="Logo Thư viện" style="height: 48px; margin-right:10px;">
          <span style="font-weight: bold; font-size: 20px; color: #fff;">
            THƯ VIỆN<br><small style="font-size:14px; color: #ffc107;">CTECH</small>
          </span>
        </a>
        <!-- Nút mở menu khi mobile -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
          style="border: none; outline: none;">
          <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Menu chính -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
          <ul class="navbar-nav text-uppercase fw-bold">
            <li class="nav-item <?php if ($current_page == 'index.php')
                                  echo 'active'; ?>">
              <a class="nav-link text-white px-3" href="index.php">Trang chủ</a>
            </li>
            <li class="nav-item <?php if ($current_page == 'menu.php')
                                  echo 'active'; ?>">
              <a class="nav-link text-white px-3" href="menu.php">Kho sách</a>
            </li>
            <li class="nav-item <?php if ($current_page == 'about.php')
                                  echo 'active'; ?>">
              <a class="nav-link text-white px-3" href="about.php">Giới thiệu</a>
            </li>
            <li class="nav-item <?php if ($current_page == 'book.php')
                                  echo 'active'; ?>">
              <a class="nav-link text-white px-3" href="book.php">Mượn sách</a>
            </li>
          </ul>
        </div>
        <!-- Góc phải: user -->
        <div class="user_option d-flex align-items-center" style="gap: 12px;">
          <?php if (isset($_SESSION['hoten'])): ?>
            <div class="user-dropdown">
              <div class="user-dropdown-trigger">
                <i class="fa fa-user-circle text-warning" style="font-size:18px;"></i>
                Xin chào, <b><?php echo htmlspecialchars($_SESSION['hoten']); ?></b>
              </div>
              <div class="user-dropdown-menu">
                <a href="yeuthich.php" class="dropdown-item">
                  Yêu thích
                </a>
                <a href="lichsu.php" class="dropdown-item">
                  Lịch sử mượn sách
                </a>
                <hr>
                <a href="dangxuat.php" class="dropdown-item text-danger">
                  Đăng xuất
                </a>
              </div>
            </div>
          <?php else: ?>
            <a href="dangnhap.php" class="btn btn-outline-warning fw-bold" style="border-radius:25px; padding:6px 20px;">
              <i class="fa fa-user mr-2"></i> Đăng nhập
            </a>
          <?php endif; ?>
        </div>
      </nav>
    </div>
    <!-- CSS -->
    <style>
      /* ===== Header cố định khi cuộn ===== */
      /* Hiển thị menu khi hover */
    </style>
    <!-- Script hiệu ứng khi cuộn -->
    <script>
      window.addEventListener("scroll", function() {
        const header = document.querySelector(".header_section");
        if (window.scrollY > 10) {
          header.classList.add("scrolled");
        } else {
          header.classList.remove("scrolled");
        }
      });
    </script>
  </header>
  <?php
  // --- Lấy danh sách thể loại & tác giả ---
  $loaisach = mysqli_query($ketnoi, "SELECT * FROM loaisach ORDER BY tenloaisach ASC");
  $tacgia = mysqli_query($ketnoi, "SELECT * FROM tacgia ORDER BY tentacgia ASC");

  // --- Bộ lọc ---
  $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
  $idloaisach = isset($_GET['idloaisach']) ? intval($_GET['idloaisach']) : 0;
  $matacgia = isset($_GET['matacgia']) ? intval($_GET['matacgia']) : 0;
  $new = isset($_GET['new']);
  $featured = isset($_GET['featured']);

  // --- Truy vấn sách ---
  $sql = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia 
        FROM sach
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
        LEFT JOIN tacgia ON sach.matacgia = tacgia.matacgia
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

  if ($matacgia > 0) {
    $sql .= " AND sach.matacgia = $matacgia";
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
      <!-- NÚT MƯỢN NHIỀU -->
      <div class="text-center mb-4">
        <button id="borrow-selected" class="btn btn-warning fw-bold px-4 py-2 rounded-pill" style="display:none;">
          <i class="fa fa-book me-2"></i> Mượn sách đã chọn
        </button>
      </div>

      <!-- DANH SÁCH SÁCH -->
      <div class="row">
        <?php while ($r = mysqli_fetch_assoc($books)) {
          $img = 'images/' . $r['hinhanhsach'];
        ?>
          <div class="col-sm-6 col-lg-4 mb-4">
            <div class="box">
              <!-- Checkbox chọn nhiều -->
              <input type="checkbox" class="select-book" value="<?= $r['masach']; ?>"
                style="position:absolute; top:10px; left:10px; z-index:10; transform:scale(1.5); cursor:pointer;">
              <div class="img-box position-relative">
                <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($r['tensach']); ?>">

                <!-- ❤️ Nút yêu thích -->
                <button class="favorite-btn <?= in_array($r['masach'], $_SESSION['favorites'] ?? []) ? 'liked' : ''; ?>"
                  data-id="<?= $r['masach']; ?>" style="position:absolute; top:10px; right:10px;">
                  <i class="fa fa-heart"></i>
                </button>
              </div>

              <div class="detail-box">
                <h5 class="fw-bold text-truncate"><?= htmlspecialchars($r['tensach']); ?></h5>
                <p class="text-muted small mb-2"><?= htmlspecialchars($r['tentacgia']); ?></p>
                <h6 class="text-secondary small mb-3"><?= htmlspecialchars($r['tenloaisach']); ?></h6>

                <div class="options d-flex justify-content-center gap-3">
                  <a href="chitietsach.php?masach=<?= $r['masach']; ?>" class="btn btn-outline-primary rounded-pill px-4">
                    <i class="fa fa-info-circle me-1"></i> Chi tiết
                  </a>
                  <a href="book.php?masach=<?= $r['masach']; ?>" class="btn btn-warning rounded-pill px-4">
                    <i class="fa fa-book me-1"></i> Mượn
                  </a>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </section>


  <!-- SCRIPT -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function() {
      // Hiệu ứng chọn nhiều sách
      const selectedBooks = new Set();
      $(document).on("change", ".select-book", function() {
        const id = $(this).val();
        if (this.checked) selectedBooks.add(id);
        else selectedBooks.delete(id);
        $("#borrow-selected").toggle(selectedBooks.size > 0);
      });

      // Chuyển sang trang mượn nhiều
      $("#borrow-selected").on("click", function() {
        if (selectedBooks.size === 0) return;
        const ids = Array.from(selectedBooks).join(",");
        window.location.href = "book.php?ids=" + encodeURIComponent(ids);
      });
    });
  </script>

  <!-- Footer -->
  <footer class="footer_section mt-auto">
    <div class="container">
      <div class="row gy-4 justify-content-between align-items-start">
        <!-- Cột 1: Liên hệ -->
        <div class="col-md-4 col-sm-12 text-center text-md-start">
          <h4 class="footer_title">Liên Hệ</h4>
          <ul class="list-unstyled footer_list">
            <li>📍 60 QL1A, xã Thường Tín, TP. Hà Nội</li>
            <li>📞 1800 6770</li>
            <li>✉️ contact@ctech.edu.vn</li>
          </ul>
        </div>

        <!-- Cột 2: Giới thiệu -->
        <div class="col-md-4 col-sm-12 text-center">
          <h4 class="footer_title">Giới Thiệu</h4>
          <p class="footer_text">
            Trang web quản lý thư viện giúp việc mượn – trả sách dễ dàng, tiết kiệm thời gian và hiệu quả
            hơn.
          </p>
        </div>

        <!-- Cột 3: Giờ mở cửa -->
        <div class="col-md-4 col-sm-12 text-center text-md-end">
          <h4 class="footer_title">Giờ Mở Cửa</h4>
          <ul class="list-unstyled footer_list">
            <li>🕒 Thứ 2 - Thứ 6: 7h30 - 17h00</li>
            <li>🕒 Thứ 7: 8h00 - 11h30</li>
          </ul>
        </div>
      </div>

      <hr class="footer_line">
      <p class="text-center mt-3 footer_copy">
        &copy; <?php echo date("Y"); ?> <b>Thư Viện Trường Học</b> | Thiết kế bởi <span
          class="text-warning">CTECH</span>
      </p>
    </div>
  </footer>

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
      const masach = btn.data("id");

      $.ajax({
        url: "xuly_yeuthich.php",
        type: "POST",
        data: {
          masach: masach
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