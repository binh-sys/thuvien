<?php
require_once('ketnoi.php');

function get_count($table, $conn) {
  return mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM $table"))['total'];
}

$count_sach = get_count('sach', $ketnoi);
$count_tacgia = get_count('tacgia', $ketnoi);
$count_loaisach = get_count('loaisach', $ketnoi);
$count_nguoidung = get_count('nguoidung', $ketnoi);
$count_muonsach = get_count('muonsach', $ketnoi);
?>

<div class="main-panel">
  <div class="content-wrapper">
    <div class="row">
      <div class="col-sm-12">
        <div class="home-tab">
          <div class="d-sm-flex align-items-center justify-content-between border-bottom">
            <ul class="nav nav-tabs" role="tablist">
              <li class="nav-item"><a class="nav-link active ps-0" data-bs-toggle="tab" href="#overview" role="tab">Tổng quan</a></li>
              <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#thongke" role="tab">Thống kê</a></li>
              <li class="nav-item"><a class="nav-link border-0" data-bs-toggle="tab" href="#khac" role="tab">Khác</a></li>
            </ul>
            <div class="btn-wrapper">
              <a href="#" class="btn btn-otline-dark align-items-center"><i class="mdi mdi-share-variant"></i> Chia sẻ</a>
              <a href="#" class="btn btn-otline-dark"><i class="mdi mdi-printer"></i> In</a>
              <a href="#" class="btn btn-primary text-white me-0"><i class="mdi mdi-download"></i> Xuất báo cáo</a>
            </div>
          </div>

          <div class="tab-content tab-content-basic">
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
              <div class="row mt-3">
                <div class="col-sm-12">
                  <div class="statistics-details d-flex align-items-center justify-content-between">
                    <div><p class="statistics-title">Tổng số sách</p><h3><?php echo $count_sach; ?></h3><p class="text-success d-flex"><i class="mdi mdi-book-open-page-variant"></i><span>Kho sách</span></p></div>
                    <div><p class="statistics-title">Tác giả</p><h3><?php echo $count_tacgia; ?></h3><p class="text-info d-flex"><i class="mdi mdi-account-tie"></i><span>Người sáng tác</span></p></div>
                    <div><p class="statistics-title">Thể loại</p><h3><?php echo $count_loaisach; ?></h3><p class="text-warning d-flex"><i class="mdi mdi-shape"></i><span>Phân loại</span></p></div>
                    <div class="d-none d-md-block"><p class="statistics-title">Người dùng</p><h3><?php echo $count_nguoidung; ?></h3><p class="text-primary d-flex"><i class="mdi mdi-account-group"></i><span>Thành viên</span></p></div>
                    <div class="d-none d-md-block"><p class="statistics-title">Lượt mượn sách</p><h3><?php echo $count_muonsach; ?></h3><p class="text-danger d-flex"><i class="mdi mdi-bookmark-multiple"></i><span>Đã ghi nhận</span></p></div>
                  </div>
                </div>
              </div>

              <div class="row mt-4">
                <div class="col-lg-8 d-flex flex-column">
                  <div class="card card-rounded flex-grow">
                    <div class="card-body">
                      <div class="d-sm-flex justify-content-between align-items-start">
                        <div>
                          <h4 class="card-title card-title-dash">Biểu đồ mượn sách</h4>
                          <p class="card-subtitle card-subtitle-dash">Thống kê số lượt mượn theo tháng</p>
                        </div>
                        <div class="dropdown">
                          <button class="btn btn-light dropdown-toggle toggle-dark btn-lg mb-0 me-0" data-bs-toggle="dropdown">Tháng này</button>
                          <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">Tháng trước</a>
                            <a class="dropdown-item" href="#">6 tháng gần đây</a>
                            <a class="dropdown-item" href="#">Cả năm</a>
                          </div>
                        </div>
                      </div>
                      <div class="chartjs-bar-wrapper mt-4">
                        <canvas id="borrowChart"></canvas>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-4 d-flex flex-column">
                  <div class="card card-rounded flex-grow">
                    <div class="card-body">
                      <h4 class="card-title card-title-dash">Tỉ lệ sách theo thể loại</h4>
                      <canvas id="typeChart" class="mt-4"></canvas>
                      <div id="typeChart-legend" class="mt-3 text-center"></div>
                    </div>
                  </div>
                </div>
              </div> 
            </div> 
          </div> 
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const borrowCtx = document.getElementById('borrowChart');
new Chart(borrowCtx, {
  type: 'bar',
  data: {
    labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
    datasets: [{ label: 'Lượt mượn', data: [12,19,3,5,2,3,15,10,7,9,11,6], borderWidth: 1 }]
  },
  options: { plugins:{legend:{display:false}}, scales:{y:{beginAtZero:true}} }
});

const typeCtx = document.getElementById('typeChart');
new Chart(typeCtx, {
  type: 'doughnut',
  data: { labels: ['Văn học','Khoa học','Thiếu nhi','Kinh tế','Lịch sử'], datasets: [{ data:[10,7,5,8,4] }] }
});
</script>
