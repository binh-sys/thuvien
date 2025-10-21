<?php
require_once('ketnoi.php');
session_start();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="thư viện, sách, trường học, giới thiệu" />
  <meta name="description" content="Giới thiệu Thư viện Trường Ctech" />
  <meta name="author" content="Thư viện Trường Ctech" />
  <link rel="shortcut icon" href="images/Book.png" type="image/png">

  <title>Giới thiệu - Thư viện Trường Ctech</title>

  <!-- Bootstrap -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <!-- Font Awesome -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <!-- Custom -->
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    /* Header màu đen */
    .header_section {
      background-color: #000 !important;
      padding: 15px 0;
    }

    .navbar-brand span {
      color: #fff;
    }

    .navbar-nav .nav-link {
      color: #fff !important;
      font-weight: 500;
      transition: color 0.3s;
    }

    .navbar-nav .nav-link:hover,
    .navbar-nav .active>.nav-link {
      color: #ffbe33 !important;
    }

    .user_option i {
      color: #fff;
    }

    /* Giới thiệu */
    .about-section {
      background: #fff;
      padding: 60px 0;
    }

    .team-card img {
      height: 220px;
      object-fit: cover;
      border-radius: 10px;
      transition: transform 0.3s;
    }

    .team-card:hover img {
      transform: scale(1.05);
    }

    .stats-box {
      text-align: center;
      background: #f8f9fa;
      border-radius: 15px;
      padding: 30px;
      transition: transform 0.3s;
    }

    .stats-box:hover {
      transform: translateY(-5px);
    }

    .cta-section {
      background: linear-gradient(90deg, #ffbe33, #ffa41b);
      color: #fff;
      padding: 60px 0;
      text-align: center;
    }

    .cta-section h2 {
      font-weight: bold;
      margin-bottom: 20px;
    }

    .cta-section a {
      background: #fff;
      color: #333;
      border-radius: 30px;
      padding: 10px 25px;
      font-weight: bold;
      text-decoration: none;
      transition: all 0.3s;
    }

    .cta-section a:hover {
      background: #333;
      color: #fff;
    }
  </style>
</head>

<body>

<!-- Header -->
<?php
  $current_page = basename($_SERVER['PHP_SELF']); // Lấy tên file hiện tại (vd: menu.php)
?>
<header class="header_section" style="background-color: #000; padding: 15px 0;">
  <div class="container">
    <nav class="navbar navbar-expand-lg custom_nav-container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="images/Book.png" alt="Logo Thư viện" style="height: 50px; margin-right:10px;">
        <span style="font-weight: bold; font-size: 20px; color: #fff;">
          THƯ VIỆN<br><small style="font-size:14px; color: #ffc107;">CTECH</small>
        </span>
      </a>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mx-auto">
          <li class="nav-item <?php if($current_page=='index.php') echo 'active'; ?>">
            <a class="nav-link text-white" href="index.php">Trang chủ</a>
          </li>
          <li class="nav-item <?php if($current_page=='menu.php') echo 'active'; ?>">
            <a class="nav-link text-white" href="menu.php">Kho sách</a>
          </li>
          <li class="nav-item <?php if($current_page=='about.php') echo 'active'; ?>">
            <a class="nav-link text-white" href="about.php">Giới thiệu</a>
          </li>
          <li class="nav-item <?php if($current_page=='book.php') echo 'active'; ?>">
            <a class="nav-link text-white" href="book.php">Mượn sách</a>
          </li>
        </ul>
        <div class="user_option">
          <a href="#" class="user_link text-white"><i class="fa fa-user"></i></a>
        </div>
      </div>
    </nav>
  </div>

  <style>
    .header_section .nav-link {
      transition: color 0.3s ease;
    }
    .header_section .nav-link:hover {
      color: #ffc107 !important;
    }
    .header_section .nav-item.active .nav-link {
      color: #ffc107 !important;
      font-weight: 600;
    }
  </style>
</header>



  <!-- Giới thiệu -->
  <section class="about-section">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4 mb-md-0">
          <img src="images/books.png" class="img-fluid rounded shadow" alt="Thư viện">
        </div>
        <div class="col-md-6">
          <h2 class="fw-bold mb-3">Về Thư Viện Trường Ctech</h2>
          <p>
            Thư viện Trường Ctech là nơi lưu trữ và chia sẻ tri thức, phục vụ nhu cầu học tập – nghiên cứu cho học sinh, sinh viên và giáo viên.  
            Với hàng ngàn đầu sách đa dạng về văn học, khoa học, kỹ thuật, kỹ năng sống và giáo dục, thư viện luôn sẵn sàng đồng hành cùng bạn trên hành trình tri thức.
          </p>
          <p>
            Hệ thống quản lý trực tuyến giúp bạn dễ dàng tra cứu, đăng ký mượn, và theo dõi lịch sử mượn – trả chỉ bằng vài cú click chuột.  
            Mục tiêu của chúng tôi là xây dựng một môi trường học tập mở, hiện đại và thân thiện.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Đội ngũ thư viện -->
  <section class="layout_padding bg-light">
    <div class="container">
      <div class="heading_container heading_center mb-5">
        <h2 class="fw-bold">👩‍🏫 Đội ngũ quản lý thư viện</h2>
        <p class="text-muted">Những người luôn tận tâm hỗ trợ bạn trong hành trình học tập</p>
      </div>

      <div class="row g-4">
        <div class="col-md-4 text-center">
          <div class="team-card p-3 bg-white rounded shadow-sm">
            <img src="images/nv4.png" class="w-100 mb-3" alt="Nhân viên 4">
            <h5 class="fw-bold">Nguyễn Thị Lan</h5>
            <p class="text-muted mb-1">Thủ thư trưởng</p>
            <small>📧 lan.nguyen@edu.vn</small>
          </div>
        </div>
        <div class="col-md-4 text-center">
          <div class="team-card p-3 bg-white rounded shadow-sm">
            <img src="images/nv2.png" class="w-100 mb-3" alt="Nhân viên 2">
            <h5 class="fw-bold">Trần Văn Minh</h5>
            <p class="text-muted mb-1">Quản lý hệ thống</p>
            <small>📧 minh.tran@edu.vn</small>
          </div>
        </div>
        <div class="col-md-4 text-center">
          <div class="team-card p-3 bg-white rounded shadow-sm">
            <img src="images/nv3.png" class="w-100 mb-3" alt="Nhân viên 3">
            <h5 class="fw-bold">Lê Hồng Hạnh</h5>
            <p class="text-muted mb-1">Hỗ trợ người dùng</p>
            <small>📧 hanh.le@edu.vn</small>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Thống kê -->
  <section class="layout_padding">
    <div class="container">
      <div class="heading_container heading_center mb-4">
        <h2 class="fw-bold">📊 Thống kê thư viện</h2>
      </div>
      <div class="row g-4">
        <?php
        $count_books = mysqli_fetch_row(mysqli_query($ketnoi, "SELECT COUNT(*) FROM sach"))[0];
        $count_users = mysqli_fetch_row(mysqli_query($ketnoi, "SELECT COUNT(*) FROM nguoidung"))[0];
        $count_borrows = mysqli_fetch_row(mysqli_query($ketnoi, "SELECT COUNT(*) FROM muonsach"))[0];
        ?>
        <div class="col-md-4">
          <div class="stats-box">
            <h3><?php echo $count_books; ?></h3>
            <p>Đầu sách</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stats-box">
            <h3><?php echo $count_users; ?></h3>
            <p>Người dùng</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="stats-box">
            <h3><?php echo $count_borrows; ?></h3>
            <p>Lượt mượn</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta-section">
    <div class="container">
      <h2>Bắt đầu hành trình tri thức của bạn ngay hôm nay!</h2>
      <p class="mb-4">Khám phá kho sách khổng lồ và mượn sách chỉ trong vài giây</p>
      <a href="menu.php">📚 Xem kho sách</a>
      <a href="book.php" class="ml-3">📝 Đăng ký mượn</a>
    </div>
  </section>

  <!-- Footer -->
  <footer class="footer_section">
    <div class="container">
      <div class="row">
        <div class="col-md-4 footer-col">
          <h4>Liên Hệ</h4>
          <p>📍60 QL1A, xã Thường Tín, TP. Hà Nội</p>
          <p>📞 1800 6770</p>
          <p>✉️ contact@ctech.edu.vn</p>
        </div>
        <div class="col-md-4 footer-col">
          <h4>Giới Thiệu</h4>
          <p>Trang web quản lý thư viện giúp việc mượn trả sách trở nên dễ dàng và hiệu quả hơn.</p>
        </div>
        <div class="col-md-4 footer-col">
          <h4>Giờ Mở Cửa</h4>
          <p>Thứ 2 - Thứ 6: 7h30 - 17h</p>
          <p>Thứ 7: 8h - 11h30</p>
        </div>
      </div>
      <p class="text-center mt-4">&copy; 2025 Thư Viện Trường Ctech</p>
    </div>
  </footer>

  <!-- JS -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>

</body>
</html>
