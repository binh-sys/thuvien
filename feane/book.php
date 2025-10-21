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
      <p class="text-center mt-4">&copy; <?php echo date("Y"); ?> Thư Viện Trường Học</p>
    </div>
  </footer>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
</body>
</html>
