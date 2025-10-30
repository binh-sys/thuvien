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
    <link href="css/style.css" rel="stylesheet">
    <link href="css/responsive.css" rel="stylesheet">
    <link href="css/about.css" rel="stylesheet">
    <link href="css/footer.css" rel="stylesheet">
</head>

<body>
    <?php
    $current_page = basename($_SERVER['PHP_SELF']); // Lấy tên file hiện tại (vd: menu.php)
    ?>
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
                        Thư viện Trường Ctech là nơi lưu trữ và chia sẻ tri thức, phục vụ nhu cầu học tập – nghiên cứu
                        cho học sinh, sinh viên và giáo viên.
                        Với hàng ngàn đầu sách đa dạng về văn học, khoa học, kỹ thuật, kỹ năng sống và giáo dục, thư
                        viện luôn sẵn sàng đồng hành cùng bạn trên hành trình tri thức.
                    </p>
                    <p>
                        Hệ thống quản lý trực tuyến giúp bạn dễ dàng tra cứu, đăng ký mượn, và theo dõi lịch sử mượn –
                        trả chỉ bằng vài cú click chuột.
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
    <!-- JS -->
    <script src="js/jquery-3.4.1.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/custom.js"></script>
</body>

</html>