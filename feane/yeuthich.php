<?php
session_start();
require_once('ketnoi.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['manguoidung'])) {
    header('Location: dangnhap.php');
    exit;
}

$manguoidung = $_SESSION['manguoidung'];

// Lấy danh sách sách yêu thích
$stmt = $ketnoi->prepare("
    SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia
    FROM yeuthich
    JOIN sach ON yeuthich.masach = sach.masach
    LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
    LEFT JOIN tacgia ON sach.matacgia = tacgia.matacgia
    WHERE yeuthich.manguoidung = ?
");
$stmt->bind_param("i", $manguoidung);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sách yêu thích - Thư Viện Trường Học</title>
    <link rel="shortcut icon" href="images/Book.png" type="image/png">
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="css/footer.css" rel="stylesheet">
    <link href="css/yeuthich.css" rel="stylesheet">
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

    <!-- MAIN -->
    <section class="layout_padding">
        <div class="container">
            <h2 class="text-center fw-bold mb-4" style="color:#222;">
                <i class="fa fa-heart text-danger"></i> Sách yêu thích của bạn
            </h2>

            <div class="row g-4">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($result)): ?>
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="card book-card h-100">
                                <button class="favorite-btn" data-id="<?php echo $r['masach']; ?>">
                                    <i class="fa fa-heart"></i>
                                </button>
                                <img src="images/<?php echo htmlspecialchars($r['hinhanhsach']); ?>" alt="">
                                <div class="card-body d-flex flex-column justify-content-between">
                                    <div>
                                        <h5 class="text-truncate"><?php echo htmlspecialchars($r['tensach']); ?></h5>
                                        <p class="text-muted small mb-2"><?php echo htmlspecialchars($r['tentacgia']); ?></p>
                                        <p class="text-secondary small"><?php echo htmlspecialchars($r['tenloaisach']); ?></p>
                                    </div>
                                    <a href="chitietsach.php?masach=<?php echo $r['masach']; ?>" class="btn btn-warning btn-sm rounded-pill mt-2">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-muted mt-5">Bạn chưa thêm sách nào vào danh sách yêu thích.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script>
        $(document).on("click", ".favorite-btn", function() {
            const btn = $(this);
            const icon = btn.find("i");
            const masach = btn.data("id");

            $.post("xuly_yeuthich.php", {
                masach
            }, function(res) {
                if (res.status === "added") {
                    icon.addClass("text-danger"); // tô đỏ trái tim
                    btn.attr("title", "Đã thêm vào yêu thích ❤️");
                } else if (res.status === "removed") {
                    icon.removeClass("text-danger"); // bỏ đỏ trái tim
                    btn.attr("title", "Đã bỏ khỏi yêu thích 💔");

                    // Nếu bạn đang ở trang yeuthich.php, xóa sách khỏi danh sách
                    if (window.location.pathname.includes("yeuthich.php")) {
                        btn.closest(".col-sm-6, .col-md-4, .col-lg-3").fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                }

                // (Tuỳ chọn) Thông báo nhẹ
                if (res.message) {
                    console.log(res.message);
                }
            }, "json");
        });
    </script>

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
        <script>
            $(document).on("click", ".favorite-btn", function() {
                const btn = $(this);
                const icon = btn.find("i");
                const masach = btn.data("id");

                $.post("xuly_yeuthich.php", {
                    masach
                }, function(res) {
                    if (res.status === "added") {
                        icon.addClass("text-danger"); // tô đỏ trái tim
                        btn.attr("title", "Đã thêm vào yêu thích ❤️");
                    } else if (res.status === "removed") {
                        icon.removeClass("text-danger"); // bỏ đỏ trái tim
                        btn.attr("title", "Đã bỏ khỏi yêu thích 💔");

                        // Nếu bạn đang ở trang yeuthich.php, xóa sách khỏi danh sách
                        if (window.location.pathname.includes("yeuthich.php")) {
                            btn.closest(".col-sm-6, .col-md-4, .col-lg-3").fadeOut(300, function() {
                                $(this).remove();
                            });
                        }
                    }

                    // (Tuỳ chọn) Thông báo nhẹ
                    if (res.message) {
                        console.log(res.message);
                    }
                }, "json");
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>