<?php
require_once('ketnoi.php');

// Lấy danh sách thể loại sách
$sql = "SELECT * FROM loaisach ORDER BY maloaisach DESC";
$query = mysqli_query($ketnoi, $sql);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Quản lý Thể Loại Sách</h4>

    <div class="card">
      <h5 class="card-header">Danh sách thể loại
        <a href="?page_layout=themloaisach"><i class="bx bx-plus"></i></a>
      </h5>

      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>STT</th>
              <th>Tên thể loại</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <?php 
            $i = 1;
            while ($row = mysqli_fetch_assoc($query)) { ?>
              <tr>
                <td><strong><?php echo $i++; ?></strong></td>
                <td><?php echo htmlspecialchars($row['tenloaisach']); ?></td>
                <td>
                  <div class="dropdown">
                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                      <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu">
                      <a class="dropdown-item" href="?page_layout=sualoaisach&id=<?php echo $row['maloaisach']; ?>">
                        <i class="bx bx-edit-alt me-1"></i> Sửa
                      </a>
                      <a class="dropdown-item" href="?page_layout=xoaloaisach&id=<?php echo $row['maloaisach']; ?>">
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
