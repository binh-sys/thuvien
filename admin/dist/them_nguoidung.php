<?php
require_once('ketnoi.php');

$message = '';
$type = '';

if (isset($_POST['add_user'])) {
  $hoten   = trim(mysqli_real_escape_string($ketnoi, $_POST['hoten']));
  $email   = trim(mysqli_real_escape_string($ketnoi, $_POST['email']));
  $sdt     = trim(mysqli_real_escape_string($ketnoi, $_POST['sdt']));
  $vaitro  = mysqli_real_escape_string($ketnoi, $_POST['vaitro']);
  $matkhau = mysqli_real_escape_string($ketnoi, $_POST['matkhau']);
  $xacnhan = mysqli_real_escape_string($ketnoi, $_POST['xacnhan']);

  if ($matkhau !== $xacnhan) {
    $message = '❌ Mật khẩu xác nhận không khớp!';
    $type = 'error';
  } else {
    $check = mysqli_query($ketnoi, "SELECT idnguoidung FROM nguoidung WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
      $message = '⚠️ Email đã tồn tại!';
      $type = 'warning';
    } else {
      $hash = password_hash($matkhau, PASSWORD_DEFAULT);
      $sql = "INSERT INTO nguoidung (hoten, email, sdt, matkhau, vaitro, ngaytao)
              VALUES ('$hoten', '$email', '$sdt', '$hash', '$vaitro', NOW())";
      if (mysqli_query($ketnoi, $sql)) {
        echo "<script>
          localStorage.setItem('user_message', JSON.stringify({
            text: '✅ Thêm người dùng thành công!',
            type: 'success'
          }));
          window.location.href = 'index.php?page_layout=danhsachnguoidung';
        </script>";
        exit;
      } else {
        $message = '❌ Lỗi khi thêm người dùng!';
        $type = 'error';
      }
    }
  }
}
?>

<!-- ========= GIAO DIỆN FORM ========= -->
<div class="container mt-5" style="max-width: 750px;">
  <div class="card border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
    <div class="card-header text-white" style="
        background: linear-gradient(90deg, #1e3a8a, #2563eb);
        padding: 18px 24px;
      ">
      <h4 class="mb-0 fw-bold"><i class="bx bx-user-plus"></i> Thêm người dùng mới</h4>
    </div>

    <div class="card-body p-4">
      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label class="form-label fw-semibold text-dark">Họ tên</label>
          <input type="text" name="hoten" class="form-control form-control-lg" placeholder="Nhập họ và tên..." required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark">Email</label>
          <input type="email" name="email" class="form-control form-control-lg" placeholder="Nhập địa chỉ email..." required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-semibold text-dark">Số điện thoại</label>
          <input type="text" name="sdt" class="form-control form-control-lg" placeholder="Nhập số điện thoại...">
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark">Mật khẩu</label>
            <input type="password" name="matkhau" class="form-control form-control-lg" placeholder="Nhập mật khẩu..." required>
          </div>
          <div class="col-md-6 mb-3">
            <label class="form-label fw-semibold text-dark">Xác nhận mật khẩu</label>
            <input type="password" name="xacnhan" class="form-control form-control-lg" placeholder="Nhập lại mật khẩu..." required>
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label fw-semibold text-dark">Vai trò</label>
          <select name="vaitro" class="form-select form-select-lg" required>
            <option value="">-- Chọn vai trò --</option>
            <option value="hoc_sinh">🎓 Học sinh</option>
            <option value="thuthu">📚 Thủ thư</option>
            <option value="admin">🛠️ Quản trị viên</option>
          </select>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
          <button type="submit" name="add_user" class="btn btn-success px-5 py-2 fw-semibold shadow-sm" id="saveBtn">
            <i class="bx bx-save"></i> Lưu
          </button>
          <a href="index.php?page_layout=danhsachnguoidung" class="btn btn-outline-secondary px-5 py-2 fw-semibold">
            <i class="bx bx-arrow-back"></i> Quay lại
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========= TOAST THÔNG BÁO ========= -->
<div id="toastContainer" style="
  position: fixed;
  top: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 12px;
"></div>

<script>
// Hiển thị toast khi có lỗi trong cùng trang
<?php if (!empty($message)) : ?>
  showToast("<?php echo $message; ?>", "<?php echo $type; ?>");
<?php endif; ?>

// Hiển thị toast khi redirect từ trang khác
document.addEventListener("DOMContentLoaded", () => {
  const msg = localStorage.getItem("user_message");
  if (msg) {
    const { text, type } = JSON.parse(msg);
    showToast(text, type);
    localStorage.removeItem("user_message");
  }
});

function showToast(message, type = 'success') {
  const container = document.getElementById("toastContainer");
  const toast = document.createElement("div");

  const color =
    type === "success" ? "#16a34a" :
    type === "error"   ? "#dc2626" :
    type === "warning" ? "#f59e0b" : "#2563eb";

  const icon =
    type === "success" ? "✅" :
    type === "error"   ? "❌" :
    type === "warning" ? "⚠️" : "ℹ️";

  toast.innerHTML = `<span style='margin-right:8px'>${icon}</span>${message}`;
  toast.style.cssText = `
    background: ${color};
    color: #fff;
    font-weight: 500;
    border-radius: 12px;
    padding: 14px 18px;
    min-width: 280px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    opacity: 0;
    transform: translateX(120px);
    transition: all 0.5s ease;
  `;

  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = "1";
    toast.style.transform = "translateX(0)";
  }, 100);

  setTimeout(() => {
    toast.style.opacity = "0";
    toast.style.transform = "translateX(120px)";
    setTimeout(() => toast.remove(), 500);
  }, 3500);
}
</script>
