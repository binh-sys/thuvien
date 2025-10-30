<?php
require_once('ketnoi.php'); // phải cung cấp $ketnoi (mysqli connection)
session_start();

// Messages
$message_form = '';
$message_modal = ''; // for generic modal feedback (we will map per masach)
$modal_to_open = 0; // masach id of modal to re-open if needed

// Helper: get or create user by email, return manguoidung or false on error
function get_or_create_user($ketnoi, $hoten, $email)
{
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
function is_already_borrowed($ketnoi, $manguoidung, $masach)
{
    $q = mysqli_prepare($ketnoi, "SELECT COUNT(*) FROM muonsach WHERE manguoidung = ? AND masach = ? AND trangthai != 'da_tra'");
    mysqli_stmt_bind_param($q, 'ii', $manguoidung, $masach);
    mysqli_stmt_execute($q);
    mysqli_stmt_bind_result($q, $cnt);
    mysqli_stmt_fetch($q);
    mysqli_stmt_close($q);
    return ($cnt > 0);
}

// Helper: borrow book (insert muonsach)
function borrow_book($ketnoi, $manguoidung, $masach, $ngaymuon, $hantra)
{
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
    <link href="css/index.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
</head>

<body>
    <div class="hero_area">
        <div class="bg-box">
            <img src="images/baner3.png" alt="Banner Thư viện">
        </div>

        <!-- Header -->
        <?php
        $current_page = basename($_SERVER['PHP_SELF']); // Lấy tên file hiện tại (vd: menu.php)
        session_start();
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
                                            Nơi lưu trữ hàng ngàn đầu sách hay dành cho học sinh, sinh viên và giáo
                                            viên.
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
                                            Cập nhật nhanh các đầu sách mới nhất, đa dạng thể loại: văn học, khoa học,
                                            công nghệ, và kỹ năng sống.
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
                            <div class="img-box"><img src="images/Capture.png" alt=""></div>
                            <div class="detail-box">
                                <h5>Sách Nổi Bật</h5>
                                <h6><span>Top</span> Thư viện</h6>
                                <a href="menu.php">Xem ngay</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box">
                            <div class="img-box"><img src="images/1.png" alt=""></div>
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
      <!-- =========================
   DANH SÁCH SÁCH YÊU THÍCH
========================= -->
    <section class="about_section layout_padding" style="background-color: #1e1f26;">
        <div class="container">
            <div class="heading_container heading_center mb-5">
                <h2 class="fw-bold text-light">
                    📚 Danh Sách Thư Viện
                </h2>
                <p class="text-secondary">Khám phá các cuốn sách nổi bật trong thư viện của chúng tôi</p>
            </div>

            <div class="row g-4 justify-content-center">
                <?php
                // Lấy 8 sách đầu tiên
                $sql_all = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia
                  FROM sach
                  LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
                  LEFT JOIN tacgia ON sach.matacgia = tacgia.matacgia
                  ORDER BY sach.tensach ASC
                  LIMIT 8";
                $res = mysqli_query($ketnoi, $sql_all);

                if ($res && mysqli_num_rows($res) > 0) {
                    while ($r = mysqli_fetch_assoc($res)) {
                        $img = 'images/' . $r['hinhanhsach'];
                        $masach = (int)$r['masach'];
                ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card book-card shadow-sm border-0 rounded-4 overflow-hidden h-100 position-relative">

                                <!-- Nút yêu thích -->
                                <button
                                    class="favorite-btn <?php echo isset($_SESSION['manguoidung']) && mysqli_num_rows(mysqli_query($ketnoi, "SELECT * FROM yeuthich WHERE manguoidung = {$_SESSION['manguoidung']} AND masach = {$r['masach']}")) > 0 ? 'liked' : ''; ?>"
                                    data-id="<?php echo $r['masach']; ?>">
                                    <i class="fa fa-heart"></i>
                                </button>


                                <div class="overflow-hidden">
                                    <img src="<?php echo htmlspecialchars($img); ?>" class="card-img-top img-hover-scale"
                                        style="height:260px; object-fit:cover;">
                                </div>

                                <div class="card-body text-center d-flex flex-column bg-dark text-light">
                                    <h5 class="fw-bold text-truncate" title="<?php echo htmlspecialchars($r['tensach']); ?>">
                                        <?php echo htmlspecialchars($r['tensach']); ?>
                                    </h5>
                                    <p class="text-secondary small mb-3">
                                        <?php echo htmlspecialchars($r['tentacgia']); ?> •
                                        <?php echo htmlspecialchars($r['tenloaisach']); ?>
                                    </p>
                                    <div class="mt-auto d-flex justify-content-center gap-2">
                                        <a href="chitietsach.php?masach=<?php echo $masach; ?>"
                                            class="btn btn-sm btn-primary rounded-pill px-3">
                                            Chi tiết
                                        </a>
                                        <a href="book.php?masach=<?php echo $masach; ?>"
                                            class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3">
                                            Mượn
                                        </a>
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

            <!-- Nút Xem Thêm -->
            <div class="text-center mt-5">
                <a href="menu.php" class="btn btn-warning px-5 py-2 fw-bold rounded-pill shadow-sm">
                    Xem thêm
                </a>
            </div>
        </div>

        <!-- Script -->
        <script>
            document.querySelectorAll(".favorite-btn").forEach(btn => {
                btn.addEventListener("click", function() {
                    this.classList.toggle("active");
                });
            });
        </script>
    </section>




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
                        <div class="heading_container">
                            <h2>Giới thiệu thư viện</h2>
                        </div>
                        <p>
                            Thư viện trường học là không gian học tập và nghiên cứu, cung cấp hàng ngàn đầu sách đa
                            dạng: văn học, khoa học,
                            công nghệ, kỹ năng và tài liệu tham khảo cho giáo viên và học sinh. Chúng tôi hỗ trợ mượn
                            sách trực tuyến để giúp
                            việc tra cứu và học tập thuận tiện hơn.
                        </p>
                        <a href="about.php">Xem thêm</a>
                    </div>
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
    </footer>

    <!-- Thông báo nhỏ nút yêu thích -->
    <div id="toast-container"></div>
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

        $(document).on("click", ".favorite-btn", function() {
            const btn = $(this);
            const masach = btn.data("id");

            $.ajax({
                url: "xuly_yeuthich.php",
                type: "POST",
                data: {
                    masach: masach
                },
                dataType: "json",
                success: function(res) {
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
                error: function() {
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

    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/custom.js"></script>
    <scriptz src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>