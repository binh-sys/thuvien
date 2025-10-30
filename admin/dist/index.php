<?php require_once('ketnoi.php'); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Hệ Thống Quản Lý Thư Viện Trường Học</title>

  <!-- CSS plugins -->
  <link rel="stylesheet" href="assets/vendors/feather/feather.css">
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css">
  <link rel="stylesheet" href="assets/vendors/typicons/typicons.css">
  <link rel="stylesheet" href="assets/vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css">
  <link rel="stylesheet" href="assets/vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="assets/js/select.dataTables.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/logothuvien.png"/>
</head>

<body class="with-welcome-text">
  <div class="container-scroller">
    <!-- Navbar -->
    <nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <button class="navbar-toggler navbar-toggler align-self-center me-3" type="button" data-bs-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        <a class="navbar-brand brand-logo" href="index.php">
          <img src="assets/images/logothuvien.png" alt="Thư viện" style="height:50px; width:auto; margin-right:10px;">
          <span style="display:block; font-size:18px; color:#2c3e50;">Thư viện</span>
          <span style="display:block; font-size:16px; color:#34495e;">Trường Học</span>
        </a>
        <a class="navbar-brand brand-logo-mini" href="index.php">
          <img src="assets/images/logoB.png" alt="logo"/>
        </a>
      </div>

      <div class="navbar-menu-wrapper d-flex align-items-top">
        <ul class="navbar-nav">
          <li class="nav-item fw-semibold d-none d-lg-block ms-0">
            <h1 class="welcome-text">Chào buổi sáng, <span class="text-black fw-bold">Binh</span></h1>
            <h3 class="welcome-sub-text">Tóm tắt hiệu suất của bạn trong tuần này</h3>
          </li>
        </ul>

        <ul class="navbar-nav ms-auto">
          <li class="nav-item d-none d-lg-block">
            <div class="input-group date datepicker navbar-date-picker">
              <span class="input-group-addon input-group-prepend border-right">
                <span class="icon-calendar input-group-text calendar-icon"></span>
              </span>
              <input type="text" class="form-control" placeholder="Chọn ngày">
            </div>
          </li>

          <li class="nav-item">
            <form class="search-form" action="#">
              <i class="icon-search"></i>
              <input type="search" class="form-control" placeholder="Tìm kiếm..." title="Search">
            </form>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
              <i class="icon-bell"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
              <a class="dropdown-item py-3 border-bottom">
                <p class="mb-0 fw-medium float-start">Không có thông báo mới</p>
              </a>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#">
              <i class="mdi mdi-account-circle"></i>
            </a>
          </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>

    <!-- Sidebar -->
    <div class="container-fluid page-body-wrapper">
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link" href="index.php">
              <i class="menu-icon mdi mdi-home-outline"></i>
              <span class="menu-title">Trang chủ</span>
            </a>
          </li>
          <li class="nav-item nav-category">Chức năng thư viện</li>
          <li class="nav-item"><a class="nav-link" href="index.php?page_layout=danhsachloaisach"><i class="menu-icon mdi mdi-book-open-page-variant"></i><span class="menu-title">Thể loại sách</span></a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page_layout=danhsachsach"><i class="menu-icon mdi mdi-book"></i><span class="menu-title">Sách</span></a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page_layout=danhsachmuonsach"><i class="menu-icon mdi mdi-book-open-page-variant"></i><span class="menu-title">Mượn sách</span></a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page_layout=danhsachtacgia"><i class="menu-icon mdi mdi-account-edit"></i><span class="menu-title">Tác giả</span></a></li>
          <li class="nav-item"><a class="nav-link" href="index.php?page_layout=danhsachnguoidung"><i class="menu-icon mdi mdi-account-circle"></i><span class="menu-title">Người dùng</span></a></li>
        </ul>
      </nav>

      <!-- Nội dung chính -->
      <div class="main-panel">
        <div class="content-wrapper">
          <?php
            if (isset($_GET["page_layout"])) {
              switch($_GET["page_layout"]) {
                case "danhsachnguoidung": require_once 'nguoidung.php'; break;
                case "danhsachtacgia": require_once 'tacgia.php'; break;
                case "danhsachmuonsach": require_once 'muonsach.php'; break;
                case "danhsachsach": require_once 'sach.php'; break;
                case "danhsachloaisach": require_once 'loaisach.php'; break;
              }
            } else {
              require_once 'content.php';
            }
          ?>
        </div>

        <!-- Footer -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Thiết kế bởi <b>Binh</b></span>
            <span class="float-none float-sm-end d-block mt-1 mt-sm-0 text-center">© 2025. Trường Ctech.</span>
          </div>
        </footer>
      </div>
    </div>
  </div>

  <!-- JS Plugins -->
  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
  <script src="assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <script src="assets/vendors/chart.js/chart.umd.js"></script>
  <script src="assets/vendors/progressbar.js/progressbar.min.js"></script>
  <script src="assets/js/off-canvas.js"></script>
  <script src="assets/js/template.js"></script>
  <script src="assets/js/settings.js"></script>
  <script src="assets/js/hoverable-collapse.js"></script>
  <script src="assets/js/todolist.js"></script>
  <script src="assets/js/dashboard.js"></script>
</body>
</html>
