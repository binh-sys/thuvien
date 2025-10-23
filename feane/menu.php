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
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 3px 8px rgba(0,0,0,0.08);
      padding: 15px 20px;
      margin-top: 30px;
      margin-bottom: 25px;
    }
    .book-card {
      transition: transform 0.3s, box-shadow 0.3s;
    }
    .book-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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
<?php
$current_page = basename($_SERVER['PHP_SELF']);
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
          <li class="nav-item <?php if($current_page=='index.php') echo 'active'; ?>">
            <a class="nav-link text-white px-3" href="index.php">Trang chủ</a>
          </li>
          <li class="nav-item <?php if($current_page=='menu.php') echo 'active'; ?>">
            <a class="nav-link text-white px-3" href="menu.php">Kho sách</a>
          </li>
          <li class="nav-item <?php if($current_page=='about.php') echo 'active'; ?>">
            <a class="nav-link text-white px-3" href="about.php">Giới thiệu</a>
          </li>
          <li class="nav-item <?php if($current_page=='book.php') echo 'active'; ?>">
            <a class="nav-link text-white px-3" href="book.php">Mượn sách</a>
          </li>
        </ul>
      </div>

      <!-- Góc phải: user -->
      <div class="user_option d-flex align-items-center" style="white-space: nowrap; gap: 12px;">
        <?php if(isset($_SESSION['hoten'])): ?>
          <span class="text-white d-flex align-items-center mb-0" style="font-size: 15px;">
            <i class="fa fa-user-circle text-warning mr-2" style="font-size:18px;"></i>
            Xin chào, <b class="ml-1"><?php echo htmlspecialchars($_SESSION['hoten']); ?></b>
          </span>
          <a href="dangxuat.php" class="btn fw-bold"
            style="background-color:#ffc107; color:#000; border-radius:25px; padding:6px 20px;">
            Đăng xuất
          </a>
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
    .header_section {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.95);
      z-index: 1000;
      padding: 15px 0;
      transition: all 0.3s ease;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    }

    /* Khi cuộn xuống */
    .header_section.scrolled {
      background-color: rgba(0, 0, 0, 1);
      box-shadow: 0 4px 10px rgba(0,0,0,0.5);
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
      padding-top: 90px; /* tránh nội dung bị che bởi header */
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




  <!-- Bộ lọc -->
  <section class="layout_padding">
    <div class="container">
      <div class="filter-bar">
        <form method="GET" action="menu.php" class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label">Từ khóa</label>
            <input type="text" name="keyword" class="form-control" placeholder="Nhập tên sách, tác giả..." value="<?php echo htmlspecialchars($keyword); ?>">
          </div>
          <div class="col-md-3">
            <label class="form-label">Thể loại</label>
            <select name="idloaisach" class="form-control">
              <option value="0">Tất cả</option>
              <?php while ($row = mysqli_fetch_assoc($loaisach)) { ?>
                <option value="<?php echo $row['maloaisach']; ?>" <?php if ($idloaisach == $row['maloaisach']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($row['tenloaisach']); ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Tác giả</label>
            <select name="matacgia" class="form-control">
              <option value="0">Tất cả</option>
              <?php while ($tg = mysqli_fetch_assoc($tacgia)) { ?>
                <option value="<?php echo $tg['matacgia']; ?>" <?php if ($matacgia == $tg['matacgia']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($tg['tentacgia']); ?>
                </option>
              <?php } ?>
            </select>
          </div>
          <div class="col-md-2 text-end">
            <button type="submit" class="btn btn-warning w-100">Lọc</button>
          </div>
        </form>
      </div>

      <!-- Danh sách sách -->
      <div class="row g-4">
        <?php
        if ($books && mysqli_num_rows($books) > 0) {
          while ($r = mysqli_fetch_assoc($books)) {
            $img = 'images/' . $r['hinhanhsach'];
        ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="card book-card shadow-sm border-0 rounded-4 overflow-hidden h-100">
            <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($r['tensach']); ?>">
            <div class="card-body text-center d-flex flex-column">
              <h5 class="fw-bold text-truncate" title="<?php echo htmlspecialchars($r['tensach']); ?>">
                <?php echo htmlspecialchars($r['tensach']); ?>
              </h5>
              <p class="text-muted mb-1 small"><?php echo htmlspecialchars($r['tentacgia']); ?></p>
              <span class="badge bg-danger mb-3 fw-semibold">Giá: <?php echo number_format($r['dongia']); ?> VNĐ</span>
              <div class="mt-auto">
                <a href="chitietsach.php?masach=<?php echo $r['masach']; ?>" class="btn btn-sm btn-primary">Chi tiết</a>
              </div>
            </div>
          </div>
        </div>
        <?php
          }
        } else {
          echo '<div class="col-12 text-center text-muted">Không tìm thấy sách phù hợp.</div>';
        }
        ?>
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
          Trang web quản lý thư viện giúp việc mượn – trả sách dễ dàng, tiết kiệm thời gian và hiệu quả hơn.
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
      &copy; <?php echo date("Y"); ?> <b>Thư Viện Trường Học</b> | Thiết kế bởi <span class="text-warning">CTECH</span>
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
    html, body {
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


  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>
</body>
</html>
