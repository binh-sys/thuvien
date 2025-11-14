<?php
require_once('ketnoi.php');
session_start();

// ===== THÔNG TIN NGƯỜI DÙNG ĐĂNG NHẬP =====
$logged_name = $_SESSION['hoten'] ?? '';
$logged_email = $_SESSION['email'] ?? '';

// ===== THÔNG TIN SÁCH =====
$selected_books = [];
if (isset($_GET['idsach'])) {
  $ids = [(int)$_GET['idsach']];
} elseif (isset($_GET['ids'])) {
  $ids = array_map('intval', explode(',', $_GET['ids']));
} else {
  $ids = [];
}

if (!empty($ids)) {
  $id_str = implode(',', $ids);
  $q = mysqli_query($ketnoi, "
        SELECT sach.idsach, sach.tensach, sach.dongia, sach.soluong,
               tacgia.tentacgia, loaisach.tenloaisach 
        FROM sach 
        LEFT JOIN tacgia ON sach.idtacgia = tacgia.idtacgia 
        LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach 
        WHERE sach.idsach IN ($id_str)
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
  $soluong_mua = $_POST['soluong_mua'] ?? [];

  if (empty($hoten) || empty($email) || empty($book_ids)) {
    $message_form = '<div class="alert alert-danger">⚠️ Vui lòng nhập đầy đủ thông tin và chọn ít nhất 1 sách.</div>';
  } else {

    // ===== Kiểm tra tài khoản =====
    $stmt_user = mysqli_prepare($ketnoi, "SELECT idnguoidung FROM nguoidung WHERE email=?");
    mysqli_stmt_bind_param($stmt_user, 's', $email);
    mysqli_stmt_execute($stmt_user);
    mysqli_stmt_bind_result($stmt_user, $uid);

    if (mysqli_stmt_fetch($stmt_user)) {
      $idnguoidung = $uid;
    } else {
      $idnguoidung = null;
    }
    mysqli_stmt_close($stmt_user);

    if ($idnguoidung) {

      $inserted = 0;
      $ngaymua = date('Y-m-d');

      foreach ($book_ids as $idsach) {

        $sl = (int)$soluong_mua[$idsach];

        // Kiểm tra tồn kho
        $check_stock = mysqli_query($ketnoi, "SELECT soluong FROM sach WHERE idsach=$idsach");
        $stock = mysqli_fetch_assoc($check_stock)['soluong'];

        if ($stock < $sl || $sl <= 0) continue;

        // Lấy giá
        $q_price = mysqli_query($ketnoi, "SELECT dongia FROM sach WHERE idsach=$idsach");
        $dongia = mysqli_fetch_assoc($q_price)['dongia'];
        $tongtien = $dongia * $sl;

        // ===== Thêm vào đơn hàng =====
        $ins = mysqli_prepare($ketnoi, "
          INSERT INTO donhang (idnguoidung, idsach, soluong, dongia, tongtien, ngaymua, trangthai)
          VALUES (?, ?, ?, ?, ?, ?, 'cho_xac_nhan')
        ");
        mysqli_stmt_bind_param($ins, 'iiidds', $idnguoidung, $idsach, $sl, $dongia, $tongtien, $ngaymua);

        if (mysqli_stmt_execute($ins)) $inserted++;

        mysqli_stmt_close($ins);

        // Trừ số lượng sách
        mysqli_query($ketnoi, "
          UPDATE sach SET soluong = soluong - $sl 
          WHERE idsach = $idsach AND soluong >= $sl
        ");
      }

      if ($inserted > 0) {
        $message_form = '<div class="alert alert-success">🛒 Đặt mua thành công ' . $inserted . ' sách!</div>';
      } else {
        $message_form = '<div class="alert alert-warning">⚠️ Không thể mua sách! Kiểm tra lại tồn kho.</div>';
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
  <title>Mua sách - Thư Viện Trường Học</title>
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/responsive.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/book.css">
  <link rel="stylesheet" href="css/footer.css">
</head>

<body>

  <?php include 'header.php'; ?>

  <section class="book_section py-5">
    <div class="container">
      <div class="card p-4 shadow-lg border-0" style="border-radius: 15px;">

        <h3 class="mb-4 text-center text-warning">
          <i class="fa fa-shopping-cart me-2"></i> Xác nhận mua sách
        </h3>

        <form method="POST">

          <div class="form-group mb-3">
            <label>Họ và tên</label>
            <input type="text" name="hoten" class="form-control bg-dark text-white border-secondary"
              value="<?php echo htmlspecialchars($logged_name); ?>" required>
          </div>

          <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control bg-dark text-white border-secondary"
              value="<?php echo htmlspecialchars($logged_email); ?>" required>
          </div>

          <?php if (!empty($selected_books)): ?>
            <div class="form-group mb-3">
              <label>📚 Danh sách sách bạn sẽ mua:</label>

              <ul class="book-list list-unstyled bg-dark text-white p-3 rounded">
                <?php foreach ($selected_books as $b): ?>
                  <li class="py-2 border-bottom border-secondary">
                    <i class="fa fa-book me-2 text-warning"></i>
                    <b><?php echo $b['tensach']; ?></b>
                    — <small><?php echo $b['tentacgia']; ?> (<?php echo $b['tenloaisach']; ?>)</small>

                    <input type="hidden" name="book_ids[]" value="<?php echo $b['idsach']; ?>">

                    <div class="mt-2">
                      <label>Số lượng mua:</label>
                      <input type="number" name="soluong_mua[<?php echo $b['idsach']; ?>]"
                             class="form-control bg-dark text-white border-secondary"
                             min="1" max="<?php echo $b['soluong']; ?>" value="1" style="width:120px;">
                    </div>

                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php else: ?>
            <div class="alert alert-warning text-center">⚠️ Bạn chưa chọn sách nào để mua!</div>
          <?php endif; ?>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-warning px-5 py-2 fw-bold rounded-pill">
              🛒 Xác nhận mua
            </button>
          </div>
        </form>

        <div class="mt-4">
          <?php echo $message_form; ?>
        </div>

      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
</body>
</html>
