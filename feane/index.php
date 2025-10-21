<?php
require_once('ketnoi.php'); // phải cung cấp $ketnoi (mysqli connection)
session_start();

// Messages
$message_form = '';
$message_modal = ''; // for generic modal feedback (we will map per masach)
$modal_to_open = 0; // masach id of modal to re-open if needed

// Helper: get or create user by email, return manguoidung or false on error
function get_or_create_user($ketnoi, $hoten, $email) {
    $hoten = trim($hoten);
    $email = trim($email);

    // check existing
    $stmt = mysqli_prepare($ketnoi, "SELECT manguoidung FROM nguoidung WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $uid);
    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        return (int)$uid;
    }
    mysqli_stmt_close($stmt);

    // insert new user with default password '12345' and vaitro 'hoc_sinh'
    $default_pass = password_hash('12345', PASSWORD_DEFAULT); // hashed for safety
    $vaitro = 'hoc_sinh';
    $insert = mysqli_prepare($ketnoi, "INSERT INTO nguoidung (hoten, email, matkhau, vaitro) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($insert, 'ssss', $hoten, $email, $default_pass, $vaitro);
    if (mysqli_stmt_execute($insert)) {
        $newid = mysqli_insert_id($ketnoi);
        mysqli_stmt_close($insert);
        return (int)$newid;
    } else {
        mysqli_stmt_close($insert);
        return false;
    }
}

// Helper: check if user already borrowed the book and not returned
function is_already_borrowed($ketnoi, $manguoidung, $masach) {
    $q = mysqli_prepare($ketnoi, "SELECT COUNT(*) FROM muonsach WHERE manguoidung = ? AND masach = ? AND trangthai != 'da_tra'");
    mysqli_stmt_bind_param($q, 'ii', $manguoidung, $masach);
    mysqli_stmt_execute($q);
    mysqli_stmt_bind_result($q, $cnt);
    mysqli_stmt_fetch($q);
    mysqli_stmt_close($q);
    return ($cnt > 0);
}

// Helper: borrow book (insert muonsach)
function borrow_book($ketnoi, $manguoidung, $masach, $ngaymuon, $hantra) {
    $trangthai = 'dang_muon';
    $ins = mysqli_prepare($ketnoi, "INSERT INTO muonsach (manguoidung, masach, ngaymuon, hantra, trangthai) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'iisss', $manguoidung, $masach, $ngaymuon, $hantra, $trangthai);
    $ok = mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    return $ok;
}

// Process POST (both modal and bottom form)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic sanitation
    $form_type = isset($_POST['form_type']) ? $_POST['form_type'] : 'form';
    $hoten = isset($_POST['hoten']) ? trim($_POST['hoten']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $masach = isset($_POST['masach']) ? intval($_POST['masach']) : 0;
    $ngaymuon = isset($_POST['ngaymuon']) ? $_POST['ngaymuon'] : date('Y-m-d');
    $hantra = isset($_POST['hantra']) ? $_POST['hantra'] : date('Y-m-d', strtotime('+7 days'));

    // Validate minimal
    if ($hoten === '' || $email === '' || $masach <= 0) {
        if ($form_type === 'modal') {
            $message_modal = '<div class="alert alert-danger">Vui lòng nhập đủ Họ tên, Email và chọn sách.</div>';
            $modal_to_open = $masach;
        } else {
            $message_form = '<div class="alert alert-danger">Vui lòng nhập đủ Họ tên, Email và chọn sách.</div>';
        }
    } else {
        // Get or create user
        $manguoidung = get_or_create_user($ketnoi, $hoten, $email);
        if ($manguoidung === false) {
            if ($form_type === 'modal') {
                $message_modal = '<div class="alert alert-danger">Lỗi hệ thống khi tạo người dùng. Vui lòng thử lại.</div>';
                $modal_to_open = $masach;
            } else {
                $message_form = '<div class="alert alert-danger">Lỗi hệ thống khi tạo người dùng. Vui lòng thử lại.</div>';
            }
        } else {
            // Check duplicate borrow
            if (is_already_borrowed($ketnoi, $manguoidung, $masach)) {
                if ($form_type === 'modal') {
                    $message_modal = '<div class="alert alert-warning">Bạn đang mượn cuốn sách này và chưa trả.</div>';
                    $modal_to_open = $masach;
                } else {
                    $message_form = '<div class="alert alert-warning">Bạn đang mượn cuốn sách này và chưa trả.</div>';
                }
            } else {
                // Insert borrow
                $ok = borrow_book($ketnoi, $manguoidung, $masach, $ngaymuon, $hantra);
                if ($ok) {
                    if ($form_type === 'modal') {
                        $message_modal = '<div class="alert alert-success">✅ Mượn sách thành công! Nhân viên thư viện sẽ xác nhận.</div>';
                        $modal_to_open = $masach;
                    } else {
                        $message_form = '<div class="alert alert-success">✅ Mượn sách thành công! Nhân viên thư viện sẽ xác nhận.</div>';
                    }
                    // Optionally, you might want to decrement Soluong in sach table here.
                } else {
                    if ($form_type === 'modal') {
                        $message_modal = '<div class="alert alert-danger">Có lỗi khi ghi dữ liệu. Vui lòng thử lại.</div>';
                        $modal_to_open = $masach;
                    } else {
                        $message_form = '<div class="alert alert-danger">Có lỗi khi ghi dữ liệu. Vui lòng thử lại.</div>';
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="keywords" content="thư viện, sách, mượn sách, đọc sách, học tập" />
  <meta name="description" content="Hệ thống quản lý thư viện trường học" />
  <meta name="author" content="Thư viện Trường Học" />
  <link rel="shortcut icon" href="images/Book.png" type="image/png">

  <title>Thư Viện Trường Học</title>

  <!-- bootstrap core css -->
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <!-- owl slider stylesheet -->
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <!-- nice select -->
  <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery-nice-select/1.1.0/css/nice-select.min.css" />
  <!-- font awesome -->
  <link href="css/font-awesome.min.css" rel="stylesheet" />
  <!-- custom styles -->
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />

  <style>
    .book-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      transition: all 0.3s;
    }
    .modal .form-group { margin-bottom: 0.8rem; }
  </style>
</head>

<body>

  <div class="hero_area">
    <div class="bg-box">
      <img src="images/baner3.png" alt="Banner Thư viện">
    </div>

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


    <!-- end header section -->

    <!-- slider section -->
    <section class="slider_section ">
      <div id="customCarousel1" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">

          <div class="carousel-item active">
            <div class="container ">
              <div class="row">
                <div class="col-md-7 col-lg-6 ">
                  <div class="detail-box">
                    <h1>Kho Sách Khổng Lồ</h1>
                    <p>
                      Nơi lưu trữ hàng ngàn đầu sách hay dành cho học sinh, sinh viên và giáo viên.  
                      Bạn có thể dễ dàng tìm kiếm và mượn sách chỉ với vài cú click chuột.
                    </p>
                    <div class="btn-box">
                      <a href="menu.php" class="btn1">Khám phá ngay</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="carousel-item ">
            <div class="container ">
              <div class="row">
                <div class="col-md-7 col-lg-6 ">
                  <div class="detail-box">
                    <h1>Sách Mới Về</h1>
                    <p>
                      Cập nhật nhanh các đầu sách mới nhất, đa dạng thể loại: văn học, khoa học, công nghệ, và kỹ năng sống.
                    </p>
                    <div class="btn-box">
                      <a href="menu.php" class="btn1">Xem ngay</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="carousel-item">
            <div class="container ">
              <div class="row">
                <div class="col-md-7 col-lg-6 ">
                  <div class="detail-box">
                    <h1>Đăng Ký Mượn Sách</h1>
                    <p>
                      Hãy chọn sách yêu thích của bạn và đăng ký mượn ngay hôm nay.  
                      Hệ thống giúp bạn quản lý lịch sử mượn dễ dàng, nhanh chóng.
                    </p>
                    <div class="btn-box">
                      <a href="book.php" class="btn1">Mượn Ngay</a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <div class="container">
          <ol class="carousel-indicators">
            <li data-target="#customCarousel1" data-slide-to="0" class="active"></li>
            <li data-target="#customCarousel1" data-slide-to="1"></li>
            <li data-target="#customCarousel1" data-slide-to="2"></li>
          </ol>
        </div>
      </div>
    </section>
    <!-- end slider -->
  </div>

  <!-- =========================
       SÁCH NỔI BẬT
       ========================= -->
  <!-- Offer Section (Sách nổi bật) -->
  <section class="offer_section layout_padding-bottom">
    <div class="offer_container">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <div class="box">
              <div class="img-box"><img src="images/dacnhantam.png" alt=""></div>
              <div class="detail-box">
                <h5>Sách Nổi Bật</h5>
                <h6><span>Top</span> Thư viện</h6>
                <a href="menu.php">Xem ngay</a>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="box">
              <div class="img-box"><img src="images/dad.png" alt=""></div>
              <div class="detail-box">
                <h5>Sách Được Yêu Thích</h5>
                <h6><span>100+</span> Lượt mượn</h6>
                <a href="menu.php">Khám phá</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>


  <!-- =========================
       DANH SÁCH SÁCH
       ========================= -->
   <section class="about_section layout_padding">
    <div class="container">
        <div class="heading_container heading_center mb-5">
            <h2 class="fw-bold">📚 Danh Sách Thư Viện</h2>
            <p class="text-muted">Khám phá các cuốn sách nổi bật trong thư viện của chúng tôi</p>
        </div>

        <div class="row g-4">
            <?php
            $sql_all = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia
                        FROM sach
                        LEFT JOIN loaisach ON sach.idloaisach = loaisach.maloaisach
                        LEFT JOIN tacgia ON sach.matacgia = tacgia.matacgia
                        ORDER BY sach.tensach ASC";
            $res = mysqli_query($ketnoi, $sql_all);
            if ($res && mysqli_num_rows($res) > 0) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $img = 'images/' . $r['hinhanhsach'];
                    $masach = (int)$r['masach'];
            ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="card book-card shadow-sm border-0 rounded-4 overflow-hidden h-100 position-relative">
                    <div class="overflow-hidden">
                        <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top img-hover-scale" style="height:260px; object-fit:cover;">
                    </div>
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="fw-bold text-truncate" title="<?php echo htmlspecialchars($r['tensach']); ?>">
                            <?php echo htmlspecialchars($r['tensach']); ?>
                        </h5>
                        <p class="text-muted mb-1 small"><?php echo htmlspecialchars($r['tentacgia']); ?> • <?php echo htmlspecialchars($r['tenloaisach']); ?></p>
                        <span class="badge bg-danger mb-3 fw-semibold">Giá: <?php echo number_format($r['dongia']); ?> VNĐ</span>
                        <div class="mt-auto d-flex justify-content-center gap-2">
                            <a href="chitietsach.php?masach=<?php echo $masach; ?>" class="btn btn-sm btn-primary">Chi tiết</a>
                            <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#muonModal<?php echo $masach; ?>">Mượn</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                echo '<p class="text-center text-muted">Hiện chưa có sách trong thư viện.</p>';
            }
            ?>
        </div>
    </div>
</section>

<!-- CSS nâng cấp -->
<style>
.book-card {
    transition: transform 0.3s, box-shadow 0.3s;
}
.book-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
}
.img-hover-scale {
    transition: transform 0.3s;
}
.img-hover-scale:hover {
    transform: scale(1.05);
}
.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>



  <!-- =========================
       MƯỢN SÁCH GẦN ĐÂY
       ========================= -->
  <section class="layout_padding" style="background:#f9f9f9;">
    <div class="container">
      <h3 class="text-center mb-4">📚 Mượn Sách Gần Đây</h3>
      <ul class="list-group">
        <?php
        $sql = "SELECT m.mamuon, n.hoten, s.tensach, m.trangthai 
                FROM muonsach m
                JOIN nguoidung n ON m.manguoidung = n.manguoidung
                JOIN sach s ON m.masach = s.masach
                ORDER BY m.mamuon DESC LIMIT 5";
        $query = mysqli_query($ketnoi, $sql);
        if (mysqli_num_rows($query) > 0) {
          while ($row = mysqli_fetch_assoc($query)) {
            $badge = $row['trangthai'] == 'da_tra'
              ? '<span class="badge badge-success">Đã trả</span>'
              : '<span class="badge badge-warning text-dark">Đang mượn</span>';
            echo '<li class="list-group-item d-flex justify-content-between align-items-center">
                    <span><b>' . htmlspecialchars($row['hoten']) . '</b> - ' . htmlspecialchars($row['tensach']) . '</span>
                    ' . $badge . '
                  </li>';
          }
        } else {
          echo '<li class="list-group-item text-center">Chưa có lượt mượn nào.</li>';
        }
        ?>
      </ul>
    </div>
  </section>

  <!-- =========================
       GIỚI THIỆU THƯ VIỆN
       ========================= -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="row">
        <div class="col-md-6 ">
          <div class="img-box">
            <img src="images/books.png" alt="" class="img-fluid"> 
          </div>
        </div>
        <div class="col-md-6">
          <div class="detail-box">
            <div class="heading_container"><h2>Giới thiệu thư viện</h2></div>
            <p>
              Thư viện trường học là không gian học tập và nghiên cứu, cung cấp hàng ngàn đầu sách đa dạng: văn học, khoa học,
              công nghệ, kỹ năng và tài liệu tham khảo cho giáo viên và học sinh. Chúng tôi hỗ trợ mượn sách trực tuyến để giúp
              việc tra cứu và học tập thuận tiện hơn.
            </p>
            <a href="about.php">Xem thêm</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- =========================
       FORM ĐĂNG KÝ MƯỢN SÁCH (CUỐI TRANG)
       ========================= -->
  <section id="form-muonsach" class="book_section layout_padding">
    <div class="container">
      <div class="heading_container"><h2>Đăng ký mượn sách</h2></div>
      <div class="row">
        <div class="col-md-6">
          <div class="form_container">
            <form method="POST" action="index.php">
              <input type="hidden" name="form_type" value="form">
              <div class="form-group">
                <label>Họ và tên</label>
                <input type="text" name="hoten" class="form-control" placeholder="Họ và tên" required>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
              </div>
              <div class="form-group">
                <label>Chọn sách</label>
                <select class="form-control" name="masach" required>
                  <option value="">-- Chọn sách --</option>
                  <?php
                  // load books for select
                  $book_sql = "SELECT masach, tensach, Soluong FROM sach ORDER BY tensach ASC";
                  $book_q = mysqli_query($ketnoi, $book_sql);
                  if ($book_q && mysqli_num_rows($book_q) > 0) {
                    while ($b = mysqli_fetch_assoc($book_q)) {
                      echo '<option value="'.intval($b['masach']).'">'.htmlspecialchars($b['tensach']).' (Còn: '.intval($b['Soluong']).')</option>';
                    }
                  } else {
                    echo '<option value="">Không có sách khả dụng</option>';
                  }
                  ?>
                </select>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label>Ngày mượn</label>
                  <input type="date" name="ngaymuon" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="form-group col-md-6">
                  <label>Hạn trả</label>
                  <input type="date" name="hantra" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                </div>
              </div>
              <div class="btn_box">
                <button type="submit" class="btn btn-warning">Gửi yêu cầu mượn</button>
              </div>
            </form>

            <!-- message area for bottom form -->
            <div class="mt-3" id="formMessage">
              <?php
              if ($message_form !== '') {
                echo $message_form;
              }
              ?>
            </div>
          </div>
        </div>

       <div class="col-md-6 d-flex justify-content-center align-items-center">
  <div class="map_container w-100 h-100 text-center">
    <img src="images/nv1.png" 
         alt="Thư viện" 
         class="rounded shadow"
         style="width: 100%; height: 100%; max-height: 550px; object-fit: contain; transform: scale(1.08);">
  </div>
</div>

  </div>
</div>
      </div>
    </div>
  </section>
<style>
.book_section .row {
  align-items: center; /* canh giữa form và ảnh theo chiều dọc */
}

.book_section .map_container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  min-height: 450px; /* đảm bảo ảnh không bị thấp hơn form */
}

.book_section .map_container img {
  width: 100%;
  height: auto;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.book_section .map_container img:hover {
  transform: scale(1.1); /* zoom nhẹ khi hover cho đẹp */
}

</style>

  <!-- =========================
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

  <!-- JS -->
  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
  <script src="js/custom.js"></script>
</body>
</html>
