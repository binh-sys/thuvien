<?php
require_once('ketnoi.php');

// Lấy danh sách sách + tên thể loại + tên tác giả
$sql = "
  SELECT 
    sach.masach,
    sach.tensach,
    sach.soluong,
    sach.hinhanhsach,
    sach.dongia,
    sach.mota,
    loaisach.tenloaisach,
    tacgia.tentacgia
  FROM sach
  JOIN loaisach ON sach.idloaisach = loaisach.maloaisach
  JOIN tacgia ON sach.matacgia = tacgia.matacgia
  ORDER BY sach.masach DESC
";
$query = mysqli_query($ketnoi, $sql);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Quản lý Sách</h4>

    <!-- Bảng hiển thị -->
    <div class="card">
      <h5 class="card-header">Danh sách Sách
        <a href="?page_layout=themsach"><i class="bx bx-plus"></i></a>
      </h5>

      <div class="table-responsive text-nowrap">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>STT</th>
              <th>Tên sách</th>
              <th>Ảnh</th>
              <th>Thể loại</th>
              <th>Tác giả</th>
              <th>Số lượng</th>
              <th>Đơn giá</th>
              <th>Mô tả</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php 
            $i = 1;
            while ($row = mysqli_fetch_assoc($query)) { ?>
              <tr>
                <td><strong><?php echo $i++; ?></strong></td>
                <td><?php echo htmlspecialchars($row['tensach']); ?></td>
                <td>
                  <?php if (!empty($row['hinhanhsach'])): ?>
                    <img src="images/<?php echo $row['hinhanhsach']; ?>" 
                         alt="<?php echo $row['tensach']; ?>" 
                         class="rounded" width="60">
                  <?php else: ?>
                    <span class="text-muted">Chưa có ảnh</span>
                  <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['tenloaisach']); ?></td>
                <td><?php echo htmlspecialchars($row['tentacgia']); ?></td>
                <td><?php echo $row['soluong']; ?></td>
                <td><?php echo number_format($row['dongia']); ?> ₫</td>
                <td style="max-width: 250px;"><?php echo htmlspecialchars($row['mota']); ?></td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="?page_layout=suasach&id=<?php echo $row['masach']; ?>">
                        <i class="bx bx-edit-alt me-1"></i> Sửa
                      </a>
                      <a class="dropdown-item" href="?page_layout=xoasach&id=<?php echo $row['masach']; ?>">
                        <i class="bx bx-trash me-1"></i> Xóa
                      </a>
                    </div>
                  </div>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- /Content wrapper -->
