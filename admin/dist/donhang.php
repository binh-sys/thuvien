<?php
require_once('ketnoi.php');

// Lấy danh sách đơn hàng + thông tin người dùng
$sql = "SELECT 
            donhang.iddonhang,
            nguoidung.hoten AS tennguoidung,
            donhang.ngaydat,
            donhang.tongtien,
            donhang.trangthai,
            COUNT(donhang_chitiet.id) AS soluong_sanpham
        FROM donhang
        JOIN nguoidung ON donhang.idnguoidung = nguoidung.idnguoidung
        LEFT JOIN donhang_chitiet ON donhang.iddonhang = donhang_chitiet.iddonhang
        GROUP BY donhang.iddonhang
        ORDER BY donhang.iddonhang DESC";

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
  .badge {
    padding: 6px 10px;
    font-size: 0.8rem;
  }
</style>

<div class="container mt-4">
  <div class="card shadow border-0">
    <div class="card-header text-white d-flex justify-content-between align-items-center">
      <h4 class="mb-0"><i class='bx bx-cart'></i> Quản lý đơn hàng</h4>
      <a href="index.php?page_layout=them_donhang" class="btn btn-light btn-sm">
        <i class="bx bx-plus"></i> Tạo đơn hàng mới
      </a>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover align-middle text-center">
          <thead>
            <tr>
              <th>STT</th>
              <th>Khách hàng</th>
              <th>Ngày đặt</th>
              <th>Số SP</th>
              <th>Tổng tiền</th>
              <th>Trạng thái</th>
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
                <td><?= date('d/m/Y H:i', strtotime($row['ngaydat'])); ?></td>

                <td><strong><?= $row['soluong_sanpham']; ?></strong></td>
                <td><?= number_format($row['tongtien']); ?> đ</td>

                <td>
                  <?php 
                  $st = $row['trangthai'];
                  $color = [
                    'cho_duyet' => 'warning',
                    'dang_giao' => 'info',
                    'hoan_thanh' => 'success',
                    'da_huy' => 'danger'
                  ][$st];

                  $text = [
                    'cho_duyet' => 'Chờ duyệt',
                    'dang_giao' => 'Đang giao',
                    'hoan_thanh' => 'Hoàn thành',
                    'da_huy' => 'Đã hủy'
                  ][$st];

                  echo "<span class='badge bg-$color'>$text</span>";
                  ?>
                </td>

                <td>
                  <div class="d-flex justify-content-center gap-2">
                    <a href="index.php?page_layout=xem_donhang&iddonhang=<?= $row['iddonhang']; ?>" 
                       class="btn btn-success btn-sm rounded-circle" 
                       data-bs-toggle="tooltip" title="Xem chi tiết">
                      <i class="bx bx-show"></i>
                    </a>

                    <a href="index.php?page_layout=sua_donhang&iddonhang=<?= $row['iddonhang']; ?>" 
                       class="btn btn-warning btn-sm rounded-circle" 
                       data-bs-toggle="tooltip" title="Chỉnh sửa">
                      <i class="bx bx-edit"></i>
                    </a>

                    <a href="index.php?page_layout=xoa_donhang&iddonhang=<?= $row['iddonhang']; ?>" 
                       class="btn btn-danger btn-sm rounded-circle"
                       onclick="return confirm('⚠️ Bạn có chắc muốn xóa đơn hàng này không?');"
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
