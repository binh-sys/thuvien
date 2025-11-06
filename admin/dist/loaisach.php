<?php
require_once('ketnoi.php');
$sql = "SELECT * FROM loaisach ORDER BY idloaisach DESC";
$result = mysqli_query($ketnoi, $sql);
?>

<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center"
         style="background: linear-gradient(90deg, #4f46e5, #06b6d4);">
      <h4 class="mb-0"><i class="bx bx-category"></i> Danh sách thể loại</h4>
      <a href="index.php?page_layout=them_loaisach" class="btn btn-light btn-sm fw-semibold">
        <i class="bx bx-plus"></i> Thêm thể loại
      </a>
    </div>

    <div class="card-body">
      <table class="table table-hover align-middle text-center">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Tên thể loại</th>
            <th>Ngày tạo</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; while ($row = mysqli_fetch_assoc($result)) { ?>
          <tr>
            <td><?= $i++; ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($row['tenloaisach']); ?></td>
            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
            <td>
              <div class="d-flex justify-content-center gap-2">
                <a href="index.php?page_layout=sua_loaisach&idloaisach=<?= $row['idloaisach']; ?>"
                   class="btn btn-warning btn-sm" data-bs-toggle="tooltip" title="Chỉnh sửa">
                   <i class="bx bx-edit"></i></a>
                <a href="index.php?page_layout=xoa_loaisach&idloaisach=<?= $row['idloaisach']; ?>"
                   onclick="return confirm('⚠️ Bạn có chắc muốn xóa thể loại này không?');"
                   class="btn btn-danger btn-sm" data-bs-toggle="tooltip" title="Xóa thể loại">
                   <i class="bx bx-trash"></i></a>
              </div>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
