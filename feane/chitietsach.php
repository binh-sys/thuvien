<?php
include('ketnoi.php');

// 🟡 Kiểm tra và lấy thông tin sách
if (isset($_GET['idsach'])) {
  $idsach = intval($_GET['idsach']);
  $sql = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia 
          FROM sach
          LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
          LEFT JOIN tacgia ON sach.idtacgia = tacgia.idtacgia
          WHERE sach.idsach = $idsach";
  $result = mysqli_query($ketnoi, $sql);
  $sach = mysqli_fetch_assoc($result);

  if (!$sach) {
    echo "<div class='container py-5 text-center text-white'><h3>Không tìm thấy sách!</h3></div>";
    exit;
  }
} else {
  echo "<div class='container py-5 text-center text-white'><h3>Thiếu mã sách!</h3></div>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($sach['tensach']); ?> - Thư viện</title>

  <!-- Liên kết CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/chitiet.css">
  <link rel="stylesheet" href="css/footer.css">
  
</head>

<body>

  <?php
  // 🟡 Gọi header và báo cho nó biết đây là trang chi tiết
  $pageType = 'detail';
  include 'header.php';
  ?>

  <!-- ===== CHI TIẾT SÁCH ===== -->
  <section class="book_section py-5">
    <div class="container py-4">
      <div class="book-card row g-0">
        <!-- ẢNH SÁCH -->
        <div class="col-md-5">
          <img src="images/<?php echo htmlspecialchars($sach['hinhanhsach']); ?>"
            alt="<?php echo htmlspecialchars($sach['tensach']); ?>" class="book-image">
        </div>

        <!-- THÔNG TIN -->
        <div class="col-md-7">
          <div class="book-info">
            <h2 class="book-title mb-3"><?php echo htmlspecialchars($sach['tensach']); ?></h2>

            <div class="book-meta mb-3">
              <p><strong>📚 Thể loại:</strong> <?php echo htmlspecialchars($sach['tenloaisach']); ?></p>
              <p><strong>✍️ Tác giả:</strong> <?php echo htmlspecialchars($sach['tentacgia']); ?></p>
              <p><strong>📦 Số lượng còn:</strong> <?php echo htmlspecialchars($sach['soluong']); ?> cuốn</p>
            </div>

            <?php if (!empty($sach['dongia'])): ?>
              <p class="book-price">💰 Giá: <?php echo number_format($sach['dongia']); ?> VNĐ</p>
            <?php endif; ?>

            <p style="text-align: justify;"><?php echo nl2br(htmlspecialchars($sach['mota'])); ?></p>

            <div class="mt-4 d-flex flex-wrap gap-3">
              <a href="muonsach.php?idsach=<?php echo $sach['idsach']; ?>" class="btn btn-main">
                📘 Mượn Sách
              </a>
              <a href="menu.php" class="btn btn-back">
                ⬅ Quay lại
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>