<?php
include('ketnoi.php');

if (isset($_GET['masach'])) {
  $masach = intval($_GET['masach']);
  $sql = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia 
          FROM sach
          LEFT JOIN loaisach ON sach.idloaisach = loaisach.idloaisach
          LEFT JOIN tacgia ON sach.matacgia = tacgia.matacgia
          WHERE sach.masach = $masach";
  $result = mysqli_query($ketnoi, $sql);
  $sach = mysqli_fetch_assoc($result);
  
  if (!$sach) {
    echo "<div class='container py-5 text-center'><h3>Không tìm thấy sách!</h3></div>";
    exit;
  }
} else {
  echo "<div class='container py-5 text-center'><h3>Thiếu mã sách!</h3></div>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($sach['tensach']); ?> - Thư viện</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap">
  <link rel="stylesheet" href="css/chitiet.css" >
  <link href="css/footer.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
  <div class="book-card row g-0">
    <div class="col-md-5">
      <img src="http://localhost/thuvien/feane/images/<?php echo htmlspecialchars($sach['hinhanhsach']); ?>" 
           alt="<?php echo htmlspecialchars($sach['tensach']); ?>" class="book-image">
    </div>
    <div class="col-md-7">
      <div class="book-info">
        <h2 class="book-title mb-3"><?php echo htmlspecialchars($sach['tensach']); ?></h2>

        <div class="book-meta mb-3">
          <p><strong>Thể loại:</strong> <?php echo htmlspecialchars($sach['tenloaisach']); ?></p>
          <p><strong>Tác giả:</strong> <?php echo htmlspecialchars($sach['tentacgia']); ?></p>
          <p><strong>Số lượng còn:</strong> <?php echo htmlspecialchars($sach['Soluong']); ?> cuốn</p>
        </div>

        <p class="book-price">Giá: <?php echo number_format($sach['dongia']); ?> VNĐ</p>
        <p style="text-align: justify;"><?php echo nl2br($sach['mota']); ?></p>

        <div class="mt-4 d-flex gap-3">
          <a href="muonsach.php?masach=<?php echo $sach['masach']; ?>" class="btn btn-main">📘 Mượn Sách</a>
          <a href="index.php" class="btn btn-back">⬅ Quay lại</a>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
