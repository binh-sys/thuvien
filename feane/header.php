<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>

<header class="header_section <?php echo ($pageType === 'detail') ? 'header_detail' : ''; ?>">
    <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container align-items-center justify-content-between">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/Book.png" alt="Logo Thư viện" style="height: 48px; margin-right:10px;">
                <span style="font-weight: bold; font-size: 20px; color: #fff;">
                    THƯ VIỆN<br><small style="font-size:14px; color: #ffc107;">CTECH</small>
                </span>
            </a>

            <!-- Menu toggle cho mobile -->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"
                style="border: none; outline: none;">
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
                    <li class="nav-item <?php if ($current_page == 'cart.php') echo 'active'; ?>">
                        <a class="nav-link text-white px-3" href="cart.php">Mua sách</a>
                    </li>
                    </li>
                </ul>
            </div>

            <!-- Góc phải user -->
            <div class="user_option d-flex align-items-center" style="gap: 12px;">
                <?php if (isset($_SESSION['hoten'])): ?>
                    <div class="user-dropdown">
                        <div class="user-dropdown-trigger">
                            <i class="fa fa-user-circle text-warning" style="font-size:18px;"></i>
                            Xin chào, <b><?php echo htmlspecialchars($_SESSION['hoten']); ?></b>
                        </div>
                        <div class="user-dropdown-menu">
                            <a href="yeuthich.php" class="dropdown-item">Yêu thích</a>
                            <a href="lichsu.php" class="dropdown-item">Lịch sử mượn sách</a>
                            <hr>
                            <a href="dangxuat.php" class="dropdown-item text-danger">Đăng xuất</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="dangnhap.php" class="btn btn-outline-warning fw-bold" style="border-radius:25px; padding:6px 20px;">
                        <i class="fa fa-user mr-2"></i> Đăng nhập
                    </a>
                <?php endif; ?>
            </div>
            <!-- SEARCH -->
            <div class="search-box">
                <i class="fa fa-eye-slash search-icon"></i>
                <input type="text" id="header-search" class="search-input" placeholder="Tìm kiếm sách...">
                <ul class="search-suggestions" id="search-suggestions"></ul>
            </div>

        </nav>
    </div>
</header>

<script>
    window.addEventListener("scroll", function() {
        const header = document.querySelector(".header_section");
        if (window.scrollY > 10) header.classList.add("scrolled");
        else header.classList.remove("scrolled");
    });
</script>

<script>
    // =========================
    //  SEARCH HEADER FULL JS (EYE + BLINK ANIMATION)
    // =========================

    const searchBox = document.querySelector(".search-box");
    const searchInput = document.querySelector("#header-search");
    const searchSuggestions = document.querySelector("#search-suggestions");
    const searchIcon = document.querySelector(".search-icon");


    // Hàm tạo hiệu ứng blink
    function blinkIcon() {
        searchIcon.classList.add("eye-blink");
        setTimeout(() => {
            searchIcon.classList.remove("eye-blink");
        }, 200);
    }


    // ✅ 1. Click icon → mở search + đổi icon mắt mở + blink
    searchIcon.addEventListener("mouseenter", (e) => {
        e.stopPropagation();
        blinkIcon();

        searchBox.classList.add("active");
        searchInput.focus();

        searchIcon.classList.remove("fa-eye-slash");
        searchIcon.classList.add("fa-eye");
    });


    // ✅ 2. Gợi ý realtime
    searchInput.addEventListener("input", () => {
        const keyword = searchInput.value.trim();

        if (keyword.length === 0) {
            searchSuggestions.style.display = "none";
            return;
        }

        fetch("search_suggest.php?keyword=" + encodeURIComponent(keyword))
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    searchSuggestions.style.display = "none";
                    return;
                }

                searchSuggestions.innerHTML = data
                    .map(item => `
                    <li data-id="${item.idsach}">
                        <b>${item.tensach}</b><br>
                        <small>${item.tentacgia}</small>
                    </li>
                `)
                    .join("");

                searchSuggestions.style.display = "block";
            });
    });


    // ✅ 3. Click gợi ý
    searchSuggestions.addEventListener("click", (e) => {
        const li = e.target.closest("li");
        if (!li) return;

        const title = li.querySelector("b").textContent;

        searchInput.value = title;
        searchSuggestions.style.display = "none";

        window.location.href = "menu.php?keyword=" + encodeURIComponent(title);
    });

    searchSuggestions.addEventListener("mousedown", (e) => {
        e.preventDefault();
    });


    // ✅ 4. Enter → tìm
    searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();

            const keyword = searchInput.value.trim();
            if (keyword !== "") {
                window.location.href = "menu.php?keyword=" + encodeURIComponent(keyword);
            }
        }
    });


    // ✅ 5. Click ra ngoài → thu search-box + mắt chớp + đổi icon mắt nhắm
    document.addEventListener("click", (e) => {
        if (!searchBox.contains(e.target)) {

            blinkIcon(); // CHỚP

            searchBox.classList.remove("active");
            searchSuggestions.style.display = "none";

            searchIcon.classList.remove("fa-eye");
            searchIcon.classList.add("fa-eye-slash");
        }
    });
</script>