<?php
require_once('ketnoi.php');

$sql = "SELECT s.*, l.tenloaisach, t.tentacgia
        FROM sach s
        LEFT JOIN loaisach l ON s.idloaisach = l.idloaisach
        LEFT JOIN tacgia t ON s.idtacgia = t.idtacgia
        ORDER BY s.ngaynhap DESC";
$result = mysqli_query($ketnoi, $sql);
?>

<!-- ===== DANH SÁCH SÁCH ===== -->
<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center" 
         style="background: linear-gradient(90deg, #1e3a8a, #3b82f6);">
      <h4 class="mb-0"><i class="bx bx-book"></i> Quản lý Sách</h4>
      <a href="index.php?page_layout=them_sach" class="btn btn-light btn-sm shadow-sm fw-semibold">
        <i class="bx bx-plus-circle"></i> Thêm Sách
      </a>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover table-bordered align-middle">
          <thead class="table-primary text-center align-middle">
            <tr>
              <th>STT</th>
              <th>Ảnh bìa</th>
              <th>Tên sách</th>
              <th>Tác giả</th>
              <th>Thể loại</th>
              <th>Số lượng</th>
              <th>Đơn giá</th>
              <th>Ngày nhập</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody class="text-center">
            <?php 
            $i = 1;
            while ($row = mysqli_fetch_assoc($result)) { ?>
              <tr>
                <td><strong><?= $i++; ?></strong></td>
                <td>
                  <?php if (!empty($row['hinhanhsach'])): ?>
                    <img src="../../feane/images/<?= htmlspecialchars($row['hinhanhsach']); ?>" 
                         width="60" height="80"
                         class="shadow-sm rounded border"
                         style="object-fit:cover;">
                  <?php else: ?>
                    <span class="text-muted fst-italic">Chưa có ảnh</span>
                  <?php endif; ?>
                </td>
                <td class="fw-semibold text-start"><?= htmlspecialchars($row['tensach']); ?></td>
                <td><?= htmlspecialchars($row['tentacgia'] ?? 'Không rõ'); ?></td>
                <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['tenloaisach'] ?? 'Không rõ'); ?></span></td>
                <td><?= $row['soluong']; ?></td>
                <td class="text-success fw-bold"><?= number_format($row['dongia']); ?>₫</td>
                <td><?= date('d/m/Y', strtotime($row['ngaynhap'])); ?></td>
                <td>
                  <div class="d-flex justify-content-center gap-2">
                    <!-- Nút Sửa -->
                    <a href="index.php?page_layout=sua_sach&idsach=<?= $row['idsach']; ?>" 
                       class="btn btn-warning btn-sm text-dark shadow-sm"
                       data-bs-toggle="tooltip" title="Chỉnh sửa">
                      <i class="bx bx-edit-alt"></i>
                    </a>

                    <!-- Nút Xóa -->
                    <button class="btn btn-danger btn-sm shadow-sm"
                            data-id="<?= $row['idsach']; ?>"
                            data-bs-toggle="tooltip" title="Xóa sách"
                            onclick="confirmDelete(this)">
                      <i class="bx bx-trash"></i>
                    </button>
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

<!-- Toast thông báo -->
<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>

<!-- Script xử lý Tooltip + Toast + Xóa -->
<script>
document.addEventListener("DOMContentLoaded", function() {
  // Tooltip
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(el => new bootstrap.Tooltip(el));
});

// Xác nhận xóa
function confirmDelete(btn) {
  if (confirm("⚠️ Bạn có chắc muốn xóa sách này không?")) {
    const id = btn.getAttribute('data-id');
    fetch(`xoa_sach.php?idsach=${id}`)
      .then(res => res.text())
      .then(msg => showToast(msg.includes('✅') ? '✅ Xóa thành công!' : '❌ Không thể xóa!', msg.includes('✅') ? 'success' : 'danger'))
      .then(() => setTimeout(() => window.location.reload(), 1500));
  }
}

// Hàm toast thông báo đẹp
function showToast(message, type = 'info') {
  const color = type === 'success' ? 'bg-success' : (type === 'danger' ? 'bg-danger' : 'bg-primary');
  const toast = document.createElement('div');
  toast.className = `toast align-items-center text-white border-0 ${color} show`;
  toast.role = 'alert';
  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body fw-semibold">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>`;
  document.getElementById('toastContainer').appendChild(toast);
  setTimeout(() => toast.remove(), 3000);
}
</script>

<!-- CSS tùy chỉnh -->
<style>
  .card {
    border-radius: 15px;
    overflow: hidden;
  }
  .table-hover tbody tr:hover {
    background-color: #eef4ff !important;
    transition: 0.2s;
  }
  .btn-sm {
    padding: 5px 8px !important;
  }
  .badge {
    font-size: 0.85rem;
  }
</style>
