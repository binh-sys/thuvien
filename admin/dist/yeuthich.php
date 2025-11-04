<?php
if (!isset($ketnoi)) require_once('ketnoi.php');
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = '';
if ($search !== '') {
  $s = mysqli_real_escape_string($ketnoi, $search);
  $where = "WHERE n.hoten LIKE '%$s%' OR s.tensach LIKE '%$s%'";
}

$sql = "SELECT y.id, n.hoten AS nguoidung, s.tensach AS sach, y.ngaythem
        FROM yeuthich y
        JOIN nguoidung n ON y.manguoidung = n.idnguoidung
        JOIN sach s ON y.masach = s.masach
        $where
        ORDER BY y.ngaythem DESC";
$res = mysqli_query($ketnoi, $sql);
?>
<div class="card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
    <div style="font-weight:700;font-size:16px">Danh Sách Yêu Thích</div>
    <div style="display:flex;gap:8px;align-items:center">
      <input id="favSearch" placeholder="Tìm người dùng hoặc sách..." 
             style="padding:8px 12px;border-radius:10px;border:1px solid #e6eef6">
      <a href="index.php?page_layout=them_yeuthich" class="btn btn-edit">➕ Thêm</a>
    </div>
  </div>

  <div class="table-wrap">
    <table class="app-table">
      <thead>
        <tr>
          <th>STT</th>
          <th>Người dùng</th>
          <th>Sách</th>
          <th>Ngày thêm</th>
          <th style="text-align:right">Hành động</th>
        </tr>
      </thead>
      <tbody id="favBody">
        <?php if ($res && mysqli_num_rows($res) > 0): $i=1; while($r=mysqli_fetch_assoc($res)): ?>
          <tr data-name="<?php echo h($r['nguoidung'].' '.$r['sach']); ?>">
            <td><?php echo $i++; ?></td>
            <td><?php echo h($r['nguoidung']); ?></td>
            <td><?php echo h($r['sach']); ?></td>
            <td><?php echo h($r['ngaythem']); ?></td>
            <td style="text-align:right">
              <a href="index.php?page_layout=sua_yeuthich&id=<?php echo urlencode($r['id']); ?>" class="btn btn-edit">✏️ Sửa</a>
              <a href="index.php?page_layout=xoa_yeuthich&id=<?php echo urlencode($r['id']); ?>" class="btn btn-delete" onclick="return confirm('Xóa khỏi danh sách yêu thích?');">🗑 Xóa</a>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="5" style="text-align:center;padding:24px;color:#6b7280">Không có mục yêu thích nào.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.getElementById('favSearch').addEventListener('input', function(){
  const v = this.value.toLowerCase();
  document.querySelectorAll('#favBody tr').forEach(tr=>{
    tr.style.display = (tr.getAttribute('data-name')||'').toLowerCase().indexOf(v) === -1 ? 'none' : '';
  });
});
</script>
