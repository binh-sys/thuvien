<?php
require_once('ketnoi.php');
session_start();

if (isset($_POST['dangnhap'])) {
    $email = trim($_POST['email']);
    $matkhau = trim($_POST['matkhau']);

    // Truy vấn người dùng
    $sql = "SELECT * FROM nguoidung WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Kiểm tra mật khẩu (hash hoặc plain)
        if (password_verify($matkhau, $user['matkhau']) || $user['matkhau'] === $matkhau) {
            $_SESSION['user_id'] = $user['manguoidung'];
            $_SESSION['user_name'] = $user['hoten'];
            $_SESSION['user_role'] = $user['vaitro'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Sai mật khẩu. Vui lòng thử lại.";
        }
    } else {
        $error = "Email không tồn tại trong hệ thống.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập - Thư Viện Trường Học</title>
  <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="assets/images/logothuvien.png"/>
  <style>
    body {
      background: linear-gradient(135deg, #6ab7ff, #c9e4ff);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Roboto', sans-serif;
    }
    .auth-form-light {
      background: #fff;
      border-radius: 15px;
      padding: 40px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .brand-logo img {
      height: 70px;
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="content-wrapper d-flex align-items-center auth">
      <div class="row flex-grow">
        <div class="col-lg-4 mx-auto">
          <div class="auth-form-light text-left p-5">
            <div class="brand-logo text-center mb-4">
              <img src="assets/images/logothuvien.png" alt="logo">
              <h4 class="mt-2">Hệ Thống Quản Lý Thư Viện</h4>
            </div>

            <?php if (!empty($error)): ?>
              <div class="alert alert-danger text-center py-2"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" class="pt-3">
              <div class="form-group">
                <input type="email" name="email" class="form-control form-control-lg" placeholder="Email đăng nhập" required>
              </div>
              <div class="form-group">
                <input type="password" name="matkhau" class="form-control form-control-lg" placeholder="Mật khẩu" required>
              </div>
              <div class="mt-3 d-grid">
                <button type="submit" name="dangnhap" class="btn btn-primary btn-lg btn-block">Đăng nhập</button>
              </div>
              <div class="text-center mt-3">
                <small>Chưa có tài khoản? <a href="dangky.php" class="text-primary">Đăng ký ngay</a></small>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="assets/vendors/js/vendor.bundle.base.js"></script>
</body>
</html>
