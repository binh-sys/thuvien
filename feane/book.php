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
        SELECT sach.idsach, sach.tensach, tacgia.tentacgia, loaisach.tenloaisach 
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
  $hantra = $_POST['hantra'] ?? date('Y-m-d', strtotime('+7 days'));
  $ngaymuon = date('Y-m-d');

  if (empty($hoten) || empty($email) || empty($book_ids)) {
    $message_form = '<div class="alert alert-danger">⚠️ Vui lòng nhập đầy đủ thông tin và chọn ít nhất 1 sách.</div>';
  } else {
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
      foreach ($book_ids as $idsach) {
        $check = mysqli_prepare($ketnoi, "SELECT COUNT(*) FROM muonsach WHERE idnguoidung=? AND idsach=? AND trangthai!='da_tra'");
        mysqli_stmt_bind_param($check, 'ii', $idnguoidung, $idsach);
        mysqli_stmt_execute($check);
        mysqli_stmt_bind_result($check, $cnt);
        mysqli_stmt_fetch($check);
        mysqli_stmt_close($check);

        if ($cnt == 0) {
          $ins = mysqli_prepare($ketnoi, "INSERT INTO muonsach (idnguoidung, idsach, ngaymuon, hantra, trangthai) VALUES (?, ?, ?, ?, 'dang_muon')");
          mysqli_stmt_bind_param($ins, 'iiss', $idnguoidung, $idsach, $ngaymuon, $hantra);
          if (mysqli_stmt_execute($ins)) $inserted++;
          mysqli_stmt_close($ins);

          mysqli_query($ketnoi, "UPDATE sach SET Soluong = Soluong - 1 WHERE idsach = $idsach AND Soluong > 0");
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
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/book.css">
  <link rel="stylesheet" href="css/footer.css">
</head>

<body>
  <?php include 'header.php'; ?>
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
                    <input type="hidden" name="book_ids[]" value="<?php echo $b['idsach']; ?>">
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
  <?php include 'footer.php'; ?>

  <script src="js/jquery-3.4.1.min.js"></script>
  <script src="js/bootstrap.js"></script>
</body>

</html>