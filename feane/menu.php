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
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.maloaisach
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
  <style>
    header.header_section {
      background: #000;
      padding: 15px 0;
    }

    header.header_section .navbar-brand span {
      color: #fff;
    }

    .navbar-nav .nav-link {
      color: #fff !important;
    }

    .navbar-nav .nav-item.active .nav-link {
      color: #ffbe33 !important;
      font-weight: 600;
    }

    .user_option i {
      color: #fff;
    }

    .filter-bar {
      background: #ff1414ff;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
      padding: 15px 20px;
      margin-top: 30px;
      margin-bottom: 25px;
    }

    .book-card {
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .book-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .book-card img {
      height: 260px;
      object-fit: cover;
    }
  </style>
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
      window.addEventListener("scroll", function () {
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
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.maloaisach
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
        $active = ($idloaisach == $row['maloaisach']) ? 'active' : '';
        ?>
        <li class="<?= $active; ?>">
          <a href="menu.php?idloaisach=<?= $row['maloaisach']; ?>" class="filter-link">
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
                style="position:absolute; top:10px; left:10px; transform:scale(1.3); cursor:pointer;">

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
    $(document).ready(function () {
      // Hiệu ứng chọn nhiều sách
      const selectedBooks = new Set();
      $(document).on("change", ".select-book", function () {
        const id = $(this).val();
        if (this.checked) selectedBooks.add(id);
        else selectedBooks.delete(id);
        $("#borrow-selected").toggle(selectedBooks.size > 0);
      });

      // Chuyển sang trang mượn nhiều
      $("#borrow-selected").on("click", function () {
        if (selectedBooks.size === 0) return;
        const ids = Array.from(selectedBooks).join(",");
        window.location.href = "book.php?ids=" + encodeURIComponent(ids);
      });
    });
  </script>

  <!-- CSS -->
  <style>
    .filters_menu {
      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 16px;
      margin: 40px auto;
      padding: 0;
      list-style: none;
    }

    /* Nút mặc định */
    .filters_menu li a {
      display: inline-block;
      background: linear-gradient(135deg, #ffa726, #ffca28);
      color: #222;
      padding: 12px 36px;
      border-radius: 50px;
      font-weight: 500;
      text-decoration: none;
      border: 2px solid #fff176;
      transition: all 0.3s ease;
      box-shadow: 0 3px 8px rgba(255, 160, 0, 0.4);
    }

    /* Hover */
    .filters_menu li a:hover {
      background: linear-gradient(135deg, #ffb300, #ffd740);
      color: #000;
      border-color: #ffeb3b;
      box-shadow: 0 6px 14px rgba(255, 193, 7, 0.6);
      transform: translateY(-3px);
    }

    /* Active */
    .filters_menu li.active a {
      background: linear-gradient(135deg, #ffb300, #ff9800);
      color: #000;
      border: 2px solid #ffeb3b;
      font-weight: 600;
      box-shadow: 0 6px 16px rgba(255, 193, 7, 0.7);
      transform: translateY(-2px);
    }

    /* Khi click xuống */
    .filters_menu li a:active {
      transform: scale(0.97);
      box-shadow: inset 0 2px 4px rgba(255, 252, 252, 1);
    }

    /* Hiệu ứng mượt mà */
    .filters_menu li a,
    .filters_menu li.active a {
      transition: all 0.25s ease-in-out;
    }

    /* Hộp sách */
    .box {
      background: rgba(255, 255, 255, 0.05);
      border-radius: 15px;
      overflow: hidden;
      position: relative;
      transition: all 0.3s ease;
      height: 100%;
      display: flex;
      flex-direction: column;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    /* Ảnh phủ full phần trên */
    .img-box {
      position: relative;
      height: 320px;
      /* tăng nhẹ để ảnh hiển thị nhiều hơn */
      overflow: hidden;
    }

    .img-box img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    /* 🔹 Phần chi tiết — làm mờ xuyên ảnh */
    .detail-box {
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      padding: 15px 10px 60px;
      /* để chừa chỗ cho nút */
      text-align: center;
      color: #fff;
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      border-top: 1px solid rgba(255, 255, 255, 0.25);
      border-radius: 0 0 15px 15px;
      text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
      transition: all 0.3s ease;
    }

    /* Hover nhẹ sáng hơn */
    .box:hover .detail-box {
      background: rgba(255, 255, 255, 0.25);
      backdrop-filter: blur(14px);
      -webkit-backdrop-filter: blur(14px);
    }

    /* Nút hành động */
    .detail-box .options a {
      padding: 10px 45px;
      min-width: 160px;
      border-radius: 50px;
      font-weight: 600;
      transition: all 0.25s ease;
    }

    .detail-box .options a.btn-outline-primary:hover {
      background-color: #0d6efd;
      color: #fff;
      box-shadow: 0 0 12px rgba(13, 110, 253, 0.6);
      transform: translateY(-3px);
    }

    .detail-box .options a.btn-warning:hover {
      background-color: #ffcd38;
      box-shadow: 0 0 15px rgba(255, 200, 50, 0.7);
      transform: translateY(-3px);
    }
  </style>



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

  <!-- Thông báo nhỏ nút yêu thích -->
  <div id="toast-container"></div>

  <style>
    #toast-container {
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 2000;
    }

    .toast {
      background: rgba(0, 0, 0, 0.9);
      color: #fff;
      padding: 10px 15px;
      border-radius: 8px;
      margin-top: 10px;
      font-size: 15px;
      opacity: 0;
      transform: translateX(100%);
      transition: all 0.4s ease;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .toast.show {
      opacity: 1;
      transform: translateX(0);
    }

    .toast i {
      color: #ffc107;
      font-size: 18px;
    }

    .favorite-btn {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 20px;
      color: #aaa;
      transition: transform 0.2s ease, color 0.2s ease;
    }

    .favorite-btn:hover {
      transform: scale(1.2);
      color: #ff4444;
    }

    .favorite-btn.liked i {
      color: #ff4444;
    }
  </style>

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

    $(document).on("click", ".favorite-btn", function () {
      const btn = $(this);
      const masach = btn.data("id");

      $.ajax({
        url: "xuly_yeuthich.php",
        type: "POST",
        data: {
          masach: masach
        },
        dataType: "json",
        success: function (res) {
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
        error: function () {
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