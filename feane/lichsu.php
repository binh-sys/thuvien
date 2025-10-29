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
  <style>
    body { background-color: #f9f9f9; }
    .book-card img { height: 240px; object-fit: cover; }
    .filter-bar {
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.08);
      padding: 15px 20px;
      margin-top: 30px;
      margin-bottom: 25px;
    }
    .book-card {
      transition: 0.3s;
    }
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
  </style>
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

            <!-- CSS -->
            <style>
                /* ===== Header cố định khi cuộn ===== */

                /* Hiển thị menu khi hover */
                .user-dropdown {
                    position: relative;
                    display: inline-block;
                }

                .user-dropdown-trigger {
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    color: #fff;
                    font-size: 15px;
                    transition: color 0.3s ease;
                }

                .user-dropdown-trigger:hover {
                    color: #ffc107;
                }

                /* --- MENU --- */
                .user-dropdown-menu {
                    position: absolute;
                    top: 115%;
                    right: 0;
                    background: #fff;
                    border-radius: 12px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    min-width: 180px;
                    padding: 6px;
                    opacity: 0;
                    transform: translateY(-10px);
                    transition: all 0.25s ease;
                    visibility: hidden;
                    z-index: 999;
                }

                .user-dropdown:hover .user-dropdown-menu {
                    opacity: 1;
                    transform: translateY(0);
                    visibility: visible;
                }

                /* --- ITEM --- */
                .user-dropdown-menu .dropdown-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 12px;
                    font-size: 15px;
                    color: #222;
                    border-radius: 8px;
                    /* 🔥 Bo góc từng dòng */
                    margin: 2px 0;
                    /* 🔥 Có khoảng cách với viền */
                    transition: background-color 0.2s ease, color 0.2s ease;
                }

                .user-dropdown-menu .dropdown-item:hover {
                    background-color: #fff6d0;
                    /* vàng nhạt nhẹ nhàng */
                    color: #000;
                }

                .user-dropdown-menu hr {
                    margin: 6px 0;
                    border-top: 1px solid #eee;
                }

                .header_section {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    background-color: rgba(0, 0, 0, 0.95);
                    z-index: 1000;
                    padding: 15px 0;
                    transition: all 0.3s ease;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                }
                /* Khi cuộn xuống */
                .header_section.scrolled {
                    background-color: rgba(0, 0, 0, 1);
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
                    padding: 10px 0;
                }
                /* ===== Menu ===== */
                .header_section .nav-link {
                    transition: color 0.3s ease;
                    font-weight: 600;
                    letter-spacing: 0.5px;
                }
                .header_section .nav-link:hover {
                    color: #ffc107 !important;
                }
                /* ===== Active ===== */
                .header_section .nav-item.active .nav-link {
                    color: #ffc107 !important;
                    font-weight: 700;
                    position: relative;
                }
                .header_section .nav-item.active .nav-link::after {
                    content: "";
                    position: absolute;
                    bottom: -6px;
                    left: 50%;
                    transform: translateX(-50%);
                    width: 35%;
                    height: 2px;
                    background-color: #ffc107;
                    border-radius: 1px;
                }
                /* Ngăn xuống dòng và căn chỉnh user góc phải */
                .user_option {
                    flex-shrink: 0;
                }
                body {
                    padding-top: 90px;
                    /* tránh nội dung bị che bởi header */
                }

                /* Responsive */
                @media (max-width: 992px) {
                    .user_option {
                        margin-top: 10px;
                        justify-content: center;
                    }

                    body {
                        padding-top: 120px;
                    }
                }
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

        <style>
            /* ===== FOOTER SECTION ===== */
            .footer_section {
                background-color: #000;
                color: #ddd;
                padding: 50px 0 30px 0;
                width: 100%;
                position: relative;
                bottom: 0;
                left: 0;
                flex-shrink: 0;
                font-family: "Poppins", sans-serif;
            }

            /* Tiêu đề cột */
            .footer_title {
                color: #ffc107;
                font-weight: 700;
                font-size: 18px;
                margin-bottom: 15px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            /* Danh sách và đoạn text */
            .footer_list li,
            .footer_text {
                font-size: 15px;
                line-height: 1.7;
                color: #ccc;
                margin-bottom: 6px;
                transition: color 0.3s ease;
            }

            .footer_list li:hover {
                color: #ffc107;
                cursor: pointer;
            }

            .footer_text {
                max-width: 320px;
                margin: 0 auto;
            }

            /* Dòng ngăn cách */
            .footer_line {
                border-color: rgba(255, 255, 255, 0.1);
                margin-top: 30px;
            }

            /* Bản quyền */
            .footer_copy {
                font-size: 14px;
                color: #aaa;
                margin-bottom: 0;
            }

            /* Luôn dính cuối trang nếu nội dung ngắn */
            html,
            body {
                height: 100%;
                margin: 0;
            }

            body {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }

            main {
                flex: 1;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .footer_text {
                    max-width: 100%;
                }

                .footer_section {
                    text-align: center;
                }

                .footer_title {
                    margin-top: 20px;
                }
            }
        </style>
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
