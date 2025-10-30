<?php
require_once('ketnoi.php');
session_start();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $matkhau = trim($_POST['matkhau']);

  if ($email === '' || $matkhau === '') {
    $message = '<div class="alert alert-danger text-center">Vui lòng nhập đầy đủ Email và Mật khẩu.</div>';
  } else {
    $stmt = mysqli_prepare($ketnoi, "SELECT manguoidung, hoten, matkhau, vaitro FROM nguoidung WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
      mysqli_stmt_bind_result($stmt, $manguoidung, $hoten, $hash, $vaitro);
      mysqli_stmt_fetch($stmt);

      if (password_verify($matkhau, $hash)) {
        // Lưu session
        $_SESSION['manguoidung'] = $manguoidung;
        $_SESSION['hoten'] = $hoten;
        $_SESSION['email'] = $email;
        $_SESSION['vaitro'] = $vaitro;

        // Chuyển hướng về trang chủ
        header("Location: index.php");
        exit;
      } else {
        $message = '<div class="alert alert-danger text-center">Sai mật khẩu. Vui lòng thử lại.</div>';
      }
    } else {
      $message = '<div class="alert alert-danger text-center">Email không tồn tại trong hệ thống.</div>';
    }
    mysqli_stmt_close($stmt);
  }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng nhập - Thư viện CTECH</title>
  <link rel="shortcut icon" href="images/Book.png" type="image/png">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
  <link href="css/style.css" rel="stylesheet" />
  <link href="css/responsive.css" rel="stylesheet" />
  <style>
    body {
      background: linear-gradient(135deg, #000, #1a1a1a);
      font-family: 'Poppins', sans-serif;
      color: #fff;
      min-height: 100vh;
    }
    .login-container {
      max-width: 450px;
      margin: 100px auto;
      background: #111;
      padding: 40px 35px;
      border-radius: 15px;
      box-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
    }
    .login-container h3 {
      text-align: center;
      color: #ffc107;
      margin-bottom: 25px;
      font-weight: bold;
    }
    .form-control {
      background-color: #222;
      border: 1px solid #444;
      color: #fff;
    }
    .form-control:focus {
      border-color: #ffc107;
      box-shadow: 0 0 5px #ffc107;
    }
    .btn-login {
      background-color: #ffc107;
      color: #000;
      font-weight: 600;
      border-radius: 30px;
      width: 100%;
      padding: 10px;
      transition: 0.3s;
    }
    .btn-login:hover {
      background-color: #e0a800;
    }
    .login-footer {
      text-align: center;
      margin-top: 20px;
    }
    .login-footer a {
      color: #ffc107;
      text-decoration: none;
      font-weight: 500;
    }
    .login-footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-container">
    <h3>Đăng Nhập Hệ Thống</h3>
    <?php echo $message; ?>
    <form method="POST">
      <div class="form-group mb-3">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Nhập email..." required>
      </div>
      <div class="form-group mb-3">
        <label for="matkhau">Mật khẩu</label>
        <input type="password" name="matkhau" class="form-control" placeholder="Nhập mật khẩu..." required>
      </div>
      <button type="submit" class="btn btn-login">Đăng nhập</button>
    </form>

    <div class="login-footer mt-3">
      <p>Chưa có tài khoản? <a href="dangky.php">Đăng ký</a></p>
      <p><a href="index.php"><i class="fa fa-arrow-left"></i> Quay lại trang chủ</a></p>
    </div>
  </div>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
</body>
</html>
