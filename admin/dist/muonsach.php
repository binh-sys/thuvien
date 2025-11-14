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

<style>
  .card { border-radius: 18px; overflow: hidden; }
  .card-header {
    background: linear-gradient(135deg, #00bfa5, #009688);
    border-bottom: none;
    padding: 1rem 1.5rem;
  }
  .card-header h4 { font-weight: 600; }
  .btn-light {
    background-color: #f8f9fa; border: none; color: #009688;
    font-weight: 500; transition: 0.2s;
  }
  .btn-light:hover { background-color: #e0f2f1; transform: translateY(-1px); }
  thead th {
    background-color: #e0f7fa !important;
    color: #00695c !important;
    font-weight: 600;
  }
  tbody tr:hover {
    background-color: #f1f8e9;
    transform: scale(1.01);
  }
  .btn-warning, .btn-danger { border: none; transition: 0.2s; }
  .btn-warning:hover { background-color: #ffca28; }
  .btn-danger:hover { background-color: #ef5350; }
  .badge.bg-warning {
    background-color: #fff3cd !important;
    color: #856404 !important;
    font-weight: 500;
  }
  .form-check-input {
    width: 1.3em; height: 1.3em; cursor: pointer;
    border: 2px solid #009688;
  }
  .form-check-input:checked {
    background-color: #009688;
    border-color: #00796b;
  }
</style>

<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-header text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class='bx bx-bookmark-alt'></i> Quản lý mượn sách</h4>
      <a href="index.php?page_layout=them_muonsach" class="btn btn-light btn-sm">
        <i class="bx bx-plus"></i> Thêm mượn sách
      </a>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>STT</th>
              <th>Người mượn</th>
              <th>Tên sách</th>
              <th>Ngày mượn</th>
              <th>Hạn trả</th>
              <th>Ngày trả thực tế</th>
              <th>Xác nhận trả</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody>
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
                  <?php if (!$row['ngaytra_thucte']) { ?>
                    <form action="xacnhan_tra.php" method="POST" class="m-0">
                      <input type="hidden" name="idmuon" value="<?= $row['idmuon']; ?>">
                      <div class="form-check d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" name="xacnhan" onchange="this.form.submit()">
                      </div>
                    </form>
                  <?php } else { ?>
                    <i class='bx bx-check-circle text-success fs-4'></i>
                  <?php } ?>
                </td>
                <td>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="index.php?page_layout=sua_muonsach&idmuon=<?= $row['idmuon']; ?>" 
                       class="btn btn-warning btn-sm rounded-circle" 
                       data-bs-toggle="tooltip" title="Chỉnh sửa">
                      <i class="bx bx-edit"></i>
                    </a>
                    <a href="index.php?page_layout=xoa_muonsach&idmuon=<?= $row['idmuon']; ?>" 
                       class="btn btn-danger btn-sm rounded-circle"
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
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    const tooltipList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    tooltipList.map(el => new bootstrap.Tooltip(el))
  });
</script>
