<?php
require_once('ketnoi.php');

// Lấy danh sách tác giả
$sql = "SELECT * FROM tacgia";
$query = mysqli_query($ketnoi, $sql);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Quản lý Tác Giả</h4>

    <!-- Basic Bootstrap Table -->
    <div class="card">
      <h5 class="card-header">Danh sách tác giả
        <a href="?page_layout=themtacgia"><i class="bx bx-plus"></i></a>
      </h5>

      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>STT</th>
              <th>Tên tác giả</th>
              <th>Ghi chú</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php 
            $i = 1;
            while ($row = mysqli_fetch_assoc($query)) { ?>
              <tr>
                <td><strong><?php echo $i++; ?></strong></td>
                <td><?php echo $row['tentacgia']; ?></td>
                <td><?php echo $row['ghichu']; ?></td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="?page_layout=suatacgia&id=<?php echo $row['matacgia']; ?>">
                        <i class="bx bx-edit-alt me-1"></i> Sửa
                      </a>
                      <a class="dropdown-item" href="?page_layout=xoatacgia&id=<?php echo $row['matacgia']; ?>">
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
    <!--/ Basic Bootstrap Table -->
  </div>
  <!-- / Content -->
</div>
<!-- / Content wrapper -->
