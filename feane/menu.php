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
  <footer class="footer_section">
    <div class="container">
      <div class="row">
        <div class="col-md-4 footer-col">
          <h4>Liên Hệ</h4>
          <p>📍 60 QL1A, xã Thường Tín, TP. Hà Nội</p>
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
      <p class="text-center mt-4">&copy; 2025 Thư Viện Trường Học</p>
    </div>
  </footer>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>
</body>
</html>
