<?php
// xoa_tacgia.php
require_once('ketnoi.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "DELETE FROM tacgia WHERE matacgia = $id";
    if (mysqli_query($ketnoi, $sql)) {
        echo "<script>
                localStorage.setItem('user_message', JSON.stringify({ text: '✅ Xóa tác giả thành công!', type: 'success' }));
                window.location='index.php?page_layout=danhsachtacgia';
              </script>";
    } else {
        echo "<script>
                localStorage.setItem('user_message', JSON.stringify({ text: '⚠️ Không thể xóa tác giả vì vẫn còn sách thuộc về họ!', type: 'error' }));
                window.location='index.php?page_layout=danhsachtacgia';
              </script>";
    }
}

?>
