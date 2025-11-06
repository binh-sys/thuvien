<?php
require_once('ketnoi.php');

// Lấy danh sách mượn sách kèm thông tin người dùng và sách
$sql = "SELECT 
            muonsach.idmuon,
            nguoidung.hoten AS tennguoidung,
            sach.tensach,
            muonsach.ngaymuon,
            muonsach.hantra,
            muonsach.ngaytra_thucte
        FROM muonsach
        JOIN nguoidung ON muonsach.idnguoidung = nguoidung.idnguoidung
        JOIN sach ON muonsach.idsach = sach.idsach
        ORDER BY muonsach.idmuon DESC";

$result = mysqli_query($ketnoi, $sql);
?>

<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class='bx bx-bookmark-alt'></i> Quản lý mượn sách</h4>
      <a href="index.php?page_layout=them_muonsach" class="btn btn-light btn-sm">
        <i class="bx bx-plus"></i> Thêm mượn sách
      </a>
    </div>

    <div class="card-body">
      <table class="table table-hover align-middle">
        <thead class="table-primary text-center">
          <tr>
            <th>STT</th>
            <th>Người mượn</th>
            <th>Tên sách</th>
            <th>Ngày mượn</th>
            <th>Hạn trả</th>
            <th>Ngày trả thực tế</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <?php 
          $i = 1;
          while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <td><strong><?= $i++; ?></strong></td>
              <td><?= htmlspecialchars($row['tennguoidung']); ?></td>
              <td><?= htmlspecialchars($row['tensach']); ?></td>
              <td><?= date('d/m/Y', strtotime($row['ngaymuon'])); ?></td>
              <td><?= date('d/m/Y', strtotime($row['hantra'])); ?></td>
              <td>
                <?= $row['ngaytra_thucte'] 
                    ? date('d/m/Y', strtotime($row['ngaytra_thucte'])) 
                    : '<span class="badge bg-warning text-dark">Chưa trả</span>'; ?>
              </td>
              <td>
                <div class="d-flex justify-content-center gap-2">
                  <a href="index.php?page_layout=sua_muonsach&idmuon=<?= $row['idmuon']; ?>" 
                     class="btn btn-warning btn-sm" 
                     data-bs-toggle="tooltip" title="Chỉnh sửa">
                    <i class="bx bx-edit"></i>
                  </a>
                  <a href="index.php?page_layout=xoa_muonsach&idmuon=<?= $row['idmuon']; ?>" 
                     class="btn btn-danger btn-sm"
                     onclick="return confirm('⚠️ Bạn có chắc muốn xóa phiếu mượn này không?');"
                     data-bs-toggle="tooltip" title="Xóa">
                    <i class="bx bx-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // Kích hoạt tooltip Bootstrap
  document.addEventListener("DOMContentLoaded", () => {
    const tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipList.map(el => new bootstrap.Tooltip(el))
  });
</script>
