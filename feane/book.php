<?php
require_once('ketnoi.php');
session_start();

// ===== THÔNG TIN NGƯỜI DÙNG ĐĂNG NHẬP =====
$logged_name = $_SESSION['hoten'] ?? '';
$logged_email = $_SESSION['email'] ?? '';

// ===== THÔNG TIN SÁCH =====
$selected_books = [];
if (isset($_GET['masach'])) {
    $ids = [(int)$_GET['masach']];
} elseif (isset($_GET['ids'])) {
    $ids = array_map('intval', explode(',', $_GET['ids']));
} else {
    $ids = [];
}

if (!empty($ids)) {
    $id_str = implode(',', $ids);
    $q = mysqli_query($ketnoi, "
        SELECT sach.masach, sach.tensach, tacgia.tentacgia, loaisach.tenloaisach 
        FROM sach 
        LEFT JOIN tacgia ON sach.matacgia = tacgia.matacgia 
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach 
        WHERE sach.masach IN ($id_str)
    ");
    while ($r = mysqli_fetch_assoc($q)) {
        $selected_books[] = $r;
    }
}

// ====== XỬ LÝ GỬI FORM ======
$message_form = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $book_ids = $_POST['book_ids'] ?? [];
    $hantra = $_POST['hantra'] ?? date('Y-m-d', strtotime('+7 days'));
    $ngaymuon = date('Y-m-d');

    if (empty($hoten) || empty($email) || empty($book_ids)) {
        $message_form = '<div class="alert alert-danger">⚠️ Vui lòng nhập đầy đủ thông tin và chọn ít nhất 1 sách.</div>';
    } else {
        $stmt_user = mysqli_prepare($ketnoi, "SELECT manguoidung FROM nguoidung WHERE email=?");
        mysqli_stmt_bind_param($stmt_user, 's', $email);
        mysqli_stmt_execute($stmt_user);
        mysqli_stmt_bind_result($stmt_user, $uid);
        if (mysqli_stmt_fetch($stmt_user)) {
            $manguoidung = $uid;
        } else {
            $manguoidung = null;
        }
        mysqli_stmt_close($stmt_user);

        if ($manguoidung) {
            $inserted = 0;
            foreach ($book_ids as $masach) {
                $check = mysqli_prepare($ketnoi, "SELECT COUNT(*) FROM muonsach WHERE manguoidung=? AND masach=? AND trangthai!='da_tra'");
                mysqli_stmt_bind_param($check, 'ii', $manguoidung, $masach);
                mysqli_stmt_execute($check);
                mysqli_stmt_bind_result($check, $cnt);
                mysqli_stmt_fetch($check);
                mysqli_stmt_close($check);

                if ($cnt == 0) {
                    $ins = mysqli_prepare($ketnoi, "INSERT INTO muonsach (manguoidung, masach, ngaymuon, hantra, trangthai) VALUES (?, ?, ?, ?, 'dang_muon')");
                    mysqli_stmt_bind_param($ins, 'iiss', $manguoidung, $masach, $ngaymuon, $hantra);
                    if (mysqli_stmt_execute($ins)) $inserted++;
                    mysqli_stmt_close($ins);

                    mysqli_query($ketnoi, "UPDATE sach SET Soluong = Soluong - 1 WHERE masach = $masach AND Soluong > 0");
                }
            }
            if ($inserted > 0) {
                $message_form = '<div class="alert alert-success">✅ Mượn thành công ' . $inserted . ' sách!</div>';
            } else {
                $message_form = '<div class="alert alert-warning">⚠️ Tất cả sách bạn chọn đã được mượn hoặc không khả dụng.</div>';
            }
        } else {
            $message_form = '<div class="alert alert-danger">❌ Không tìm thấy tài khoản người dùng.</div>';
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
  <link rel="stylesheet" href="css/book.css">
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


<!-- ===== FORM MƯỢN SÁCH ===== -->
<section class="book_section py-5">
  <div class="container">
    <div class="card p-4 shadow-lg border-0" style="border-radius: 15px;">
      <h3 class="mb-4 text-center text-warning">
        <i class="fa fa-book me-2"></i> Xác nhận mượn sách
      </h3>

      <form method="POST">
        <!-- Họ tên -->
        <div class="form-group mb-3">
          <label>Họ và tên</label>
          <input type="text" name="hoten" class="form-control bg-dark text-white border-secondary"
                 value="<?php echo htmlspecialchars($logged_name); ?>"
                 placeholder="Nhập họ và tên..." required>
        </div>

        <!-- Email -->
        <div class="form-group mb-3">
          <label>Email</label>
          <input type="email" name="email" class="form-control bg-dark text-white border-secondary"
                 value="<?php echo htmlspecialchars($logged_email); ?>"
                 placeholder="Nhập email của bạn..." required>
        </div>

        <!-- Ngày mượn -->
        <div class="form-group mb-3">
          <label>Ngày mượn</label>
          <input type="date" name="ngaymuon" class="form-control bg-dark text-white border-secondary"
                 value="<?php echo date('Y-m-d'); ?>" readonly>
        </div>

        <!-- Hạn trả -->
        <div class="form-group mb-4">
          <label>Hạn trả</label>
          <input type="date" name="hantra" class="form-control bg-dark text-white border-secondary"
                 min="<?php echo date('Y-m-d'); ?>"
                 max="<?php echo date('Y-m-d', strtotime('+14 days')); ?>"
                 value="<?php echo date('Y-m-d', strtotime('+7 days')); ?>" required>
          <small class="text-muted">⚠️ Ngày trả phải trong vòng 14 ngày kể từ hôm nay.</small>
        </div>

        <!-- Danh sách sách đã chọn -->
        <?php if (!empty($selected_books)): ?>
          <div class="form-group mb-3">
            <label>📚 Danh sách sách bạn sẽ mượn:</label>
            <ul class="book-list list-unstyled bg-dark text-white p-3 rounded">
              <?php foreach ($selected_books as $b): ?>
                <li class="py-1 border-bottom border-secondary">
                  <i class="fa fa-book me-2 text-warning"></i>
                  <b><?php echo htmlspecialchars($b['tensach']); ?></b> 
                  — <small><?php echo htmlspecialchars($b['tentacgia']); ?> (<?php echo htmlspecialchars($b['tenloaisach']); ?>)</small>
                  <input type="hidden" name="book_ids[]" value="<?php echo $b['masach']; ?>">
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php else: ?>
          <div class="alert alert-warning text-center">⚠️ Bạn chưa chọn sách nào để mượn!</div>
        <?php endif; ?>

        <!-- Nút xác nhận -->
        <div class="text-center mt-4">
          <button type="submit" class="btn btn-warning px-5 py-2 fw-bold rounded-pill">
            ✅ Xác nhận mượn
          </button>
        </div>
      </form>

      <!-- Hiển thị thông báo -->
      <div class="mt-4">
        <?php echo $message_form; ?>
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
</footer>


  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
</body>
</html>
