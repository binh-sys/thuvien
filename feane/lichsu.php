<?php
session_start();
require_once('ketnoi.php');

if (!isset($_SESSION['manguoidung'])) {
  header('Location: dangnhap.php');
  exit;
}

$manguoidung = $_SESSION['manguoidung'];

// Lấy bộ lọc
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$trangthai = isset($_GET['trangthai']) ? trim($_GET['trangthai']) : '';
    
// Truy vấn lịch sử mượn
$sql = "SELECT muonsach.*, sach.tensach, sach.hinhanhsach 
        FROM muonsach
        JOIN sach ON muonsach.masach = sach.masach
        WHERE muonsach.manguoidung = $manguoidung";

if ($keyword !== '') {
  $kw = mysqli_real_escape_string($ketnoi, $keyword);
  $sql .= " AND sach.tensach LIKE '%$kw%'";
}

if ($trangthai !== '' && $trangthai != 'tatca') {
  $sql .= " AND muonsach.trangthai = '$trangthai'";
}

$sql .= " ORDER BY muonsach.ngaymuon DESC";
$result = mysqli_query($ketnoi, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Lịch sử mượn sách</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/lichsu.css">
  <link rel="stylesheet" href="css/footer.css">
</head>

<body>
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
                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation" style="border: none; outline: none;">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- Menu chính -->
                    <div class="collapse navbar-collapse justify-content-center" id="navbarSupportedContent">
                        <ul class="navbar-nav text-uppercase fw-bold">
                            <li class="nav-item <?php if ($current_page == 'index.php') echo 'active'; ?>">
                                <a class="nav-link text-white px-3" href="index.php">Trang chủ</a>
                            </li>
                            <li class="nav-item <?php if ($current_page == 'menu.php') echo 'active'; ?>">
                                <a class="nav-link text-white px-3" href="menu.php">Kho sách</a>
                            </li>
                            <li class="nav-item <?php if ($current_page == 'about.php') echo 'active'; ?>">
                                <a class="nav-link text-white px-3" href="about.php">Giới thiệu</a>
                            </li>
                            <li class="nav-item <?php if ($current_page == 'book.php') echo 'active'; ?>">
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
                            <a href="dangnhap.php" class="btn btn-outline-warning fw-bold"
                                style="border-radius:25px; padding:6px 20px;">
                                <i class="fa fa-user mr-2"></i> Đăng nhập
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>
            </div>

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
        <!-- end header section -->

<!-- ========== BỘ LỌC ========== -->
<section class="layout_padding">
  <div class="container">
    <div class="filter-bar">
      <form method="GET" action="lichsu.php" class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label">Tìm kiếm</label>
          <input type="text" name="keyword" class="form-control" placeholder="Nhập tên sách..." value="<?php echo htmlspecialchars($keyword); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Trạng thái</label>
          <select name="trangthai" class="form-control">
            <option value="tatca" <?php if($trangthai=='tatca'||$trangthai=='') echo 'selected'; ?>>Tất cả</option>
            <option value="dang_muon" <?php if($trangthai=='dang_muon') echo 'selected'; ?>>Đang mượn</option>
            <option value="da_tra" <?php if($trangthai=='da_tra') echo 'selected'; ?>>Đã trả</option>
            <option value="tre_han" <?php if($trangthai=='tre_han') echo 'selected'; ?>>Trễ hạn</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-warning w-100">Lọc</button>
        </div>
      </form>
    </div>

    <h3 class="text-center mb-4 text-dark fw-bold">📖 Lịch sử mượn sách</h3>

    <div class="row g-4">
      <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($r = mysqli_fetch_assoc($result)): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card book-card border-0 rounded-4 overflow-hidden shadow-sm h-100">
            <img src="images/<?php echo htmlspecialchars($r['hinhanhsach']); ?>" class="card-img-top">
            <div class="card-body text-center d-flex flex-column">
              <h5 class="fw-bold text-truncate"><?php echo htmlspecialchars($r['tensach']); ?></h5>
              <p class="text-muted small mb-2">Ngày mượn: <?php echo date("d/m/Y", strtotime($r['ngaymuon'])); ?></p>
              <p class="text-muted small mb-3">Hạn trả: <?php echo date("d/m/Y", strtotime($r['hantra'])); ?></p>
              <?php
                $color = ($r['trangthai'] == 'da_tra') ? 'success' : (($r['trangthai'] == 'dang_muon') ? 'warning' : 'danger');
                $text = ($r['trangthai'] == 'da_tra') ? 'Đã trả' : (($r['trangthai'] == 'dang_muon') ? 'Đang mượn' : 'Trễ hạn');
              ?>
              <span class="badge bg-<?php echo $color; ?> px-3 py-2 mb-3"><?php echo $text; ?></span>

              <!-- Nút xem chi tiết -->
              <a href="chitietsach.php?masach=<?php echo $r['masach']; ?>" 
                 class="btn btn-sm btn-warning rounded-pill px-3 mt-auto">Xem chi tiết</a>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted">Bạn chưa có lịch sử mượn sách.</div>
      <?php endif; ?>
    </div>
  </div>
</section>
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

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/custom.js"></script>
    <scriptz src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
