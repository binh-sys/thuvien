<?php
require_once('ketnoi.php');

$sql = "SELECT * FROM nguoidung";
$query = mysqli_query($ketnoi, $sql);
?>

<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Quản lý người dùng</h4>

    <div class="card">
      <h5 class="card-header">
        Danh sách người dùng 
        <a href="?page_layout=themnguoidung"><i class="bx bx-plus"></i></a>
      </h5>

      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>STT</th>
              <th>Họ tên</th>
              <th>Email</th>
              <th>Vai trò</th>
              <th>Ngày tạo</th>
              <th>Thao tác</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php 
            $i = 1;
            while ($row = mysqli_fetch_assoc($query)) { ?>
              <tr>
                <td><strong><?php echo $i++; ?></strong></td>
                <td><?php echo htmlspecialchars($row['hoten']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                  <?php 
                    switch($row['vaitro']) {
                      case 'admin': echo '<span class="badge bg-danger">Admin</span>'; break;
                      case 'thuthu': echo '<span class="badge bg-info text-dark">Thủ thư</span>'; break;
                      case 'giao_vien': echo '<span class="badge bg-primary">Giáo viên</span>'; break;
                      default: echo '<span class="badge bg-secondary">Học sinh</span>'; break;
                    }
                  ?>
                </td>
                <td><?php echo htmlspecialchars($row['ngaytao']); ?></td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="?page_layout=suanguoidung&id=<?php echo $row['manguoidung']; ?>">
                        <i class="bx bx-edit-alt me-1"></i> Sửa
                      </a>
                      <a class="dropdown-item" href="?page_layout=xoanguoidung&id=<?php echo $row['manguoidung']; ?>">
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
