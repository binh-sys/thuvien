<?php
require_once('ketnoi.php');

// Truy vấn danh sách mượn sách (join để lấy tên sách + người dùng)
$sql = "
  SELECT 
    muonsach.mamuon,
    nguoidung.hoten AS tennguoidung,
    sach.tensach,
    muonsach.ngaymuon,
    muonsach.hantra,
    muonsach.trangthai
  FROM muonsach
  JOIN nguoidung ON muonsach.manguoidung = nguoidung.manguoidung
  JOIN sach ON muonsach.masach = sach.masach
  ORDER BY muonsach.mamuon DESC
";
$query = mysqli_query($ketnoi, $sql);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Quản lý Mượn Sách</h4>

    <div class="card">
      <h5 class="card-header">Danh sách mượn sách 
        <a href="?page_layout=them_muonsach"><i class="bx bx-plus"></i></a>
      </h5>

      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>STT</th>
              <th>Người mượn</th>
              <th>Tên sách</th>
              <th>Ngày mượn</th>
              <th>Hạn trả</th>
              <th>Trạng thái</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php 
            $i = 1;
            while ($row = mysqli_fetch_assoc($query)) { 
              // Màu trạng thái
              $badge = '';
              switch($row['trangthai']) {
                case 'dang_muon': $badge = 'bg-warning text-dark'; break;
                case 'da_tra': $badge = 'bg-success'; break;
                case 'tre_han': $badge = 'bg-danger'; break;
              }
            ?>
              <tr>
                <td><strong><?php echo $i++; ?></strong></td>
                <td><?php echo htmlspecialchars($row['tennguoidung']); ?></td>
                <td><?php echo htmlspecialchars($row['tensach']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['ngaymuon'])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['hantra'])); ?></td>
                <td><span class="badge <?php echo $badge; ?>"><?php echo $row['trangthai']; ?></span></td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="?page_layout=suamuonsach&id=<?php echo $row['mamuon']; ?>">
                        <i class="bx bx-edit-alt me-1"></i> Sửa
                      </a>
                      <a class="dropdown-item" href="?page_layout=xoamuonsach&id=<?php echo $row['mamuon']; ?>">
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
