<?php
include('ketnoi.php');

if (isset($_GET['masach'])) {
  $masach = intval($_GET['masach']);
  $sql = "SELECT sach.*, loaisach.tenloaisach, tacgia.tentacgia 
          FROM sach
          LEFT JOIN loaisach ON sach.idloaisach = loaisach.maloaisach
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
  <style>
    body {
      background: linear-gradient(135deg, #e9f0f7, #fdfdfd);
      font-family: 'Poppins', sans-serif;
    }
    .book-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      overflow: hidden;
    }
    .book-image {
      border-radius: 20px 0 0 20px;
      height: 100%;
      width: 100%;
      object-fit: cover;
      transition: transform .3s ease;
    }
    .book-image:hover {
      transform: scale(1.05);
    }
    .book-info {
      padding: 40px;
    }
    .book-title {
      font-weight: 700;
      color: #222;
    }
    .book-meta p {
      margin-bottom: 6px;
      color: #555;
      font-size: 16px;
    }
    .book-price {
      font-size: 22px;
      color: #e63946;
      font-weight: 700;
      margin: 20px 0;
    }
    .btn-main {
      background: linear-gradient(to right, #007bff, #00c6ff);
      border: none;
      color: #fff;
      font-weight: 600;
      padding: 10px 24px;
      border-radius: 10px;
      transition: 0.3s;
    }
    .btn-main:hover {
      background: linear-gradient(to right, #0062cc, #0097e6);
      transform: translateY(-2px);
    }
    .btn-back {
      border: 2px solid #bbb;
      background: white;
      color: #444;
      font-weight: 600;
      border-radius: 10px;
      padding: 10px 24px;
    }
    .btn-back:hover {
      background: #f3f3f3;
    }
    @media (max-width: 768px) {
      .book-image {
        border-radius: 20px 20px 0 0;
      }
      .book-info {
        padding: 25px;
      }
    }
  </style>
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
