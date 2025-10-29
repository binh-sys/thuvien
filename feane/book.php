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
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.maloaisach 
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
