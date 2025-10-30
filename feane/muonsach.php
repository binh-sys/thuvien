<?php
include('ketnoi.php');

if (isset($_GET['masach'])) {
  $masach = intval($_GET['masach']);
  $sql_sach = "SELECT * FROM sach WHERE masach = $masach";
  $result_sach = mysqli_query($ketnoi, $sql_sach);
  $sach = mysqli_fetch_assoc($result_sach);

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoten = trim($_POST['hoten']);
    $email = trim($_POST['email']);
    $ngaymuon = date('Y-m-d');
    $hantra = date('Y-m-d', strtotime('+7 days'));
    $trangthai = 'dang_muon';

    // ✅ 1. Kiểm tra xem còn sách không
    if ($sach['Soluong'] <= 0) {
      echo "<script>
              alert('❌ Sách này hiện đã hết! Không thể mượn thêm.');
              window.location.href='chitietsach.php?masach=$masach';
            </script>";
      exit;
    }

    // ✅ 2. Kiểm tra người dùng
    $check = mysqli_query($ketnoi, "SELECT * FROM nguoidung WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
      $user = mysqli_fetch_assoc($check);
      $manguoidung = $user['manguoidung'];
    } else {
      mysqli_query($ketnoi, "INSERT INTO nguoidung (hoten, email, matkhau, vaitro) 
                             VALUES ('$hoten', '$email', '12345', 'hoc_sinh')");
      $manguoidung = mysqli_insert_id($ketnoi);
    }

    // ✅ 3. Thêm bản ghi mượn
    $sql_muon = "INSERT INTO muonsach (manguoidung, masach, ngaymuon, hantra, trangthai)
                 VALUES ($manguoidung, $masach, '$ngaymuon', '$hantra', '$trangthai')";
    $result_muon = mysqli_query($ketnoi, $sql_muon);

    if ($result_muon) {
      // ✅ 4. Giảm số lượng sách đi 1
      $sql_update = "UPDATE sach SET Soluong = Soluong - 1 WHERE masach = $masach";
      mysqli_query($ketnoi, $sql_update);

      echo "<script>
              alert('📚 Mượn sách thành công! Vui lòng trả trước ngày $hantra.');
              window.location.href='lichsumuon.php';
            </script>";
      exit;
    } else {
      echo "<script>alert('❌ Lỗi khi ghi dữ liệu mượn sách.');</script>";
    }
  }
} else {
  echo "<script>alert('Thiếu mã sách!'); window.location.href='index.php';</script>";
  exit;
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Mượn Sách - <?php echo htmlspecialchars($sach['tensach']); ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link href="css/footer.css" rel="stylesheet">
  <link href="css/muonsach.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
  <div class="card p-4 mx-auto" style="max-width: 700px;">
    <div class="text-center mb-4">
      <img src="http://localhost/thuvien/feane/images/<?php echo htmlspecialchars($sach['hinhanhsach']); ?>" 
     alt="<?php echo htmlspecialchars($sach['tensach']); ?>" 
     class="img-fluid book-image" style="max-height: 280px;">

    </div>

    <h3 class="text-center fw-bold mb-3"><?php echo htmlspecialchars($sach['tensach']); ?></h3>
    <p class="text-center text-muted mb-4">
      <b>Giá:</b> <?php echo number_format($sach['dongia']); ?> VNĐ &nbsp; | &nbsp;
      <b>Còn lại:</b> <?php echo $sach['Soluong']; ?> cuốn
    </p>

    <form method="POST" class="px-3">
      <div class="mb-3">
        <label class="form-label fw-semibold">Họ và tên</label>
        <input type="text" name="hoten" class="form-control form-control-lg" placeholder="Nhập họ tên của bạn" required>
      </div>
      <div class="mb-3">
        <label class="form-label fw-semibold">Email</label>
        <input type="email" name="email" class="form-control form-control-lg" placeholder="Nhập email của bạn" required>
      </div>

      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">📘 Xác nhận mượn sách</button>
        <a href="index.php" class="btn btn-outline-secondary px-5 py-2 ms-2 fw-bold">⬅ Quay lại</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>
