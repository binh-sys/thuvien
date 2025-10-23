<?php
require_once('ketnoi.php');
session_start();

// Messages hiển thị cho người dùng
$message_form = "";

// ========== Các hàm xử lý ==========
function get_or_create_user($ketnoi, $hoten, $email) {
    $stmt = mysqli_prepare($ketnoi, "SELECT manguoidung, hoten FROM nguoidung WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $uid, $db_hoten);
    if (mysqli_stmt_fetch($stmt)) {
        mysqli_stmt_close($stmt);
        if ($db_hoten !== $hoten && $hoten !== '') {
            $u = mysqli_prepare($ketnoi, "UPDATE nguoidung SET hoten = ? WHERE manguoidung = ?");
            mysqli_stmt_bind_param($u, 'si', $hoten, $uid);
            mysqli_stmt_execute($u);
            mysqli_stmt_close($u);
        }
        return (int)$uid;
    }
    mysqli_stmt_close($stmt);

    $default_pass = password_hash('12345', PASSWORD_DEFAULT);
    $vaitro = 'hoc_sinh';
    $ins = mysqli_prepare($ketnoi, "INSERT INTO nguoidung (hoten, email, matkhau, vaitro) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'ssss', $hoten, $email, $default_pass, $vaitro);
    if (mysqli_stmt_execute($ins)) {
        $newid = mysqli_insert_id($ketnoi);
        mysqli_stmt_close($ins);
        return (int)$newid;
    }
    mysqli_stmt_close($ins);
    return false;
}

function is_already_borrowed($ketnoi, $manguoidung, $masach) {
    $q = mysqli_prepare($ketnoi, "SELECT COUNT(*) FROM muonsach WHERE manguoidung = ? AND masach = ? AND trangthai != 'da_tra'");
    mysqli_stmt_bind_param($q, 'ii', $manguoidung, $masach);
    mysqli_stmt_execute($q);
    mysqli_stmt_bind_result($q, $cnt);
    mysqli_stmt_fetch($q);
    mysqli_stmt_close($q);
    return ($cnt > 0);
}

function borrow_book_insert($ketnoi, $manguoidung, $masach, $ngaymuon, $hantra) {
    $trangthai = 'dang_muon';
    $ins = mysqli_prepare($ketnoi, "INSERT INTO muonsach (manguoidung, masach, ngaymuon, hantra, trangthai) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($ins, 'iisss', $manguoidung, $masach, $ngaymuon, $hantra, $trangthai);
    $ok = mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);
    return $ok;
}

// ========== Xử lý POST ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten = trim($_POST['hoten'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $masach = intval($_POST['masach'] ?? 0);
    $ngaymuon = $_POST['ngaymuon'] ?? date('Y-m-d');
    $hantra = $_POST['hantra'] ?? date('Y-m-d', strtotime('+7 days'));

    if ($hoten === '' || $email === '' || $masach <= 0) {
        $message_form = '<div class="alert alert-danger">⚠️ Vui lòng nhập đầy đủ thông tin.</div>';
    } else {
        $s = mysqli_prepare($ketnoi, "SELECT Soluong, tensach FROM sach WHERE masach = ?");
        mysqli_stmt_bind_param($s, 'i', $masach);
        mysqli_stmt_execute($s);
        mysqli_stmt_bind_result($s, $soluong, $tensach);
        if (!mysqli_stmt_fetch($s)) {
            $message_form = '<div class="alert alert-danger">⚠️ Sách không tồn tại.</div>';
        } else {
            mysqli_stmt_close($s);
            if ((int)$soluong <= 0) {
                $message_form = '<div class="alert alert-warning">⚠️ Sách "<strong>'.htmlspecialchars($tensach).'</strong>" đã hết.</div>';
            } else {
                $manguoidung = get_or_create_user($ketnoi, $hoten, $email);
                if ($manguoidung === false) {
                    $message_form = '<div class="alert alert-danger">❌ Lỗi khi xử lý người dùng.</div>';
                } elseif (is_already_borrowed($ketnoi, $manguoidung, $masach)) {
                    $message_form = '<div class="alert alert-warning">⚠️ Bạn đang mượn cuốn này và chưa trả.</div>';
                } elseif (borrow_book_insert($ketnoi, $manguoidung, $masach, $ngaymuon, $hantra)) {
                    $u = mysqli_prepare($ketnoi, "UPDATE sach SET Soluong = Soluong - 1 WHERE masach = ?");
                    mysqli_stmt_bind_param($u, 'i', $masach);
                    mysqli_stmt_execute($u);
                    mysqli_stmt_close($u);
                    $message_form = '<div class="alert alert-success">✅ Mượn sách thành công! (' . htmlspecialchars($tensach) . ')</div>';
                } else {
                    $message_form = '<div class="alert alert-danger">❌ Lỗi khi lưu yêu cầu.</div>';
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="shortcut icon" href="images/Book.png" type="image/png">
  <title>Mượn sách - Thư Viện Trường Học</title>

  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">

  <style>
    header.header_section {
      background: #000;
      padding: 15px 0;
    }
    header.header_section .nav-link,
    header.header_section .navbar-brand span {
      color: #fff !important;
    }
    .book_section {
      padding: 60px 0;
      background: #fff;
    }
    .form_container .btn-warning {
      background:#ffbe33; border:0;
    }
    .card { border-radius: 12px; }
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



  <!-- Form mượn sách -->
  <section class="book_section">
    <div class="container">
      <div class="heading_container mb-4">
        <h2>Đăng ký mượn sách</h2>
        <p>Nhập thông tin để đăng ký mượn — tài khoản sẽ được tạo tự động nếu chưa có.</p>
      </div>

      <div class="row">
        <div class="col-md-6">
          <div class="card p-4 shadow-sm">
            <form method="POST" action="book.php" class="form_container">
              <div class="form-group mb-3">
                <label>Họ và tên</label>
                <input type="text" name="hoten" class="form-control" placeholder="Họ và tên" required>
              </div>
              <div class="form-group mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
              </div>
              <div class="form-group mb-3">
                <label>Chọn sách</label>
                <select name="masach" class="form-control" required>
                  <option value="">-- Chọn sách --</option>
                  <?php
                    $book_q = mysqli_query($ketnoi, "SELECT masach, tensach, Soluong FROM sach ORDER BY tensach ASC");
                    while ($b = mysqli_fetch_assoc($book_q)) {
                      echo '<option value="'.$b['masach'].'">'.htmlspecialchars($b['tensach']).' (Còn: '.$b['Soluong'].')</option>';
                    }
                  ?>
                </select>
              </div>

              <div class="form-row mb-3">
                <div class="col">
                  <label>Ngày mượn</label>
                  <input type="date" name="ngaymuon" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col">
                  <label>Hạn trả</label>
                  <input type="date" name="hantra" class="form-control" value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
                </div>
              </div>

              <button type="submit" class="btn btn-warning px-4">Gửi yêu cầu mượn</button>
            </form>

            <div class="mt-3"><?php echo $message_form; ?></div>
          </div>
        </div>

        <div class="col-md-6 d-flex align-items-center justify-content-center">
          <img src="images/nv1.png" alt="Nhân viên thư viện" style="max-width:100%; max-height:420px; object-fit:contain;">
        </div>
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
</body>
</html>
