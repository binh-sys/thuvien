<?php
require_once('ketnoi.php');

$iddonhang = $_GET['iddonhang'];

// Lấy thông tin đơn hàng + khách hàng
$sql = "SELECT 
            donhang.*,
            nguoidung.hoten,
            nguoidung.email,
            nguoidung.sdt
        FROM donhang
        JOIN nguoidung ON donhang.idnguoidung = nguoidung.idnguoidung
        WHERE donhang.iddonhang = $iddonhang";

$order = mysqli_fetch_assoc(mysqli_query($ketnoi, $sql));

// Lấy danh sách sản phẩm trong đơn
$sqlCT = "SELECT 
            donhang_chitiet.*,
            sach.tensach,
            sach.hinhanhsach
          FROM donhang_chitiet
          JOIN sach ON donhang_chitiet.idsach = sach.idsach
          WHERE donhang_chitiet.iddonhang = $iddonhang";

$resultCT = mysqli_query($ketnoi, $sqlCT);
?>

<style>
    .card {
        border-radius: 18px;
    }

    .section-title {
        font-weight: 600;
        color: #009688;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .info-box {
        background: #e0f7fa;
        padding: 12px 18px;
        border-radius: 12px;
        margin-bottom: 15px;
    }

    .table th {
        background-color: #e0f7fa;
        color: #00695c;
    }
</style>

<div class="container mt-4">
    <div class="card shadow border-0">

        <div class="card-header text-white"
            style="background: linear-gradient(135deg,#00bfa5,#009688);">
            <h4 class="mb-0">
                <i class='bx bx-show'></i> Chi tiết đơn hàng #<?= $iddonhang; ?>
            </h4>
            <a href="index.php?page_layout=add_sanpham&iddonhang=<?= $iddonhang; ?>"
                class="btn btn-primary mb-3">
                <i class='bx bx-plus'></i> Thêm sản phẩm
            </a>
        </div>

        <div class="card-body">

            <!-- THÔNG TIN KHÁCH HÀNG -->
            <div class="section-title">Thông tin khách hàng</div>

            <div class="info-box">
                <p><strong>Họ tên:</strong> <?= $order['hoten']; ?></p>
                <p><strong>Email:</strong> <?= $order['email']; ?></p>
                <p><strong>Số điện thoại:</strong> <?= $order['sdt']; ?></p>
            </div>

            <!-- THÔNG TIN ĐƠN HÀNG -->
            <div class="section-title">Thông tin đơn hàng</div>

            <div class="info-box">
                <p>
                    <strong>Ngày đặt:</strong>
                    <?= date('d/m/Y H:i', strtotime($order['ngaydat'])); ?>
                </p>
                <p>
                    <strong>Trạng thái:</strong>
                    <?php
                    $st = $order['trangthai'];
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
                </p>
            </div>

            <!-- DANH SÁCH SẢN PHẨM -->
            <div class="section-title mt-4">Sản phẩm trong đơn</div>

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Ảnh</th>
                            <th>Tên sách</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $tong = 0;
                        while ($row = mysqli_fetch_assoc($resultCT)) {
                            $tong += $row['thanhtien'];
                        ?>
                            <tr>
                                <td><?= $i++; ?></td>

                                <td>
                                    <?php if ($row['hinhanhsach']) { ?>
                                        <img src="images/<?= $row['hinhanhsach']; ?>" width="60" height="80" style="object-fit: cover;">
                                    <?php } else { ?>
                                        <img src="images/no_image.jpg" width="60" height="80">
                                    <?php } ?>
                                </td>

                                <td><?= $row['tensach']; ?></td>
                                <td><?= $row['soluong']; ?></td>

                                <td><?= number_format($row['dongia']); ?> đ</td>
                                <td><strong><?= number_format($row['thanhtien']); ?> đ</strong></td>

                                <td>
                                    <a href="index.php?page_layout=sua_sanpham&id=<?= $row['id']; ?>"
                                        class="btn btn-warning btn-sm rounded-circle" title="Sửa">
                                        <i class="bx bx-edit"></i>
                                    </a>

                                    <a href="index.php?page_layout=xoa_sanpham&id=<?= $row['id']; ?>"
                                        class="btn btn-danger btn-sm rounded-circle"
                                        onclick="return confirm('Bạn chắc chắn muốn xóa sản phẩm này?');"
                                        title="Xóa">
                                        <i class="bx bx-trash"></i>
                                    </a>
                                </td>

                            </tr>
                        <?php } ?>

                        <tr>
                            <td colspan="5" class="text-end"><strong>Tổng tiền:</strong></td>
                            <td><strong class="text-danger"><?= number_format($tong); ?> đ</strong></td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <a href="index.php?page_layout=danhsachdonhang"
                class="btn btn-secondary mt-3">
                <i class='bx bx-arrow-back'></i> Quay lại
            </a>

        </div>
    </div>
</div>