<?php 
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
include 'connect.php'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lịch Sử Làm Bài</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; padding: 20px;}
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #2980b9;}
        table { width: 100%; border-collapse: collapse; margin-top: 20px;}
        th, td { padding: 12px 15px; border: 1px solid #ddd; text-align: center;}
        th { background-color: #2980b9; color: white;}
        tr:nth-child(even) { background-color: #f2f2f2;}
        .btn-back { display: block; text-align:center; margin-top: 30px; text-decoration: none; color: #0056b3; font-weight: bold;}
    </style>
</head>
<body>

<div class="container">
    <?php
$ten_hocsinh = $_SESSION['username'];
// Nếu là Admin đang soi bài người khác
if ($_SESSION['username'] === 'admin' && isset($_GET['view_user'])) {
    $ten_hocsinh = $_GET['view_user'];
    echo "<div style='background:#ffc107; color:#000; padding:10px; text-align:center; font-weight:bold; margin-bottom:15px; border-radius:5px;'>⚙️ ADMIN ĐANG XEM DỮ LIỆU CỦA: $ten_hocsinh</div>";
}    
    echo "<h2>LỊCH SỬ LÀM BÀI CỦA: " . mb_strtoupper($ten_hocsinh, 'UTF-8') . "</h2>";

    $sql = "SELECT lop, ngay_thi, diem, so_cau_dung, tong_cau FROM KetQuaThi WHERE ten_hocsinh = '$ten_hocsinh' ORDER BY ngay_thi DESC";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Ngày Thi</th><th>Lớp</th><th>Kết Quả (Đúng/Tổng)</th><th>Điểm Hệ 10</th></tr>";
        while($row = $result->fetch_assoc()) {
            $ngay = date("d/m/Y H:i", strtotime($row['ngay_thi']));
            echo "<tr>";
            echo "<td>" . $ngay . "</td>";
            echo "<td>Lớp " . $row['lop'] . "</td>";
            echo "<td>" . $row['so_cau_dung'] . " / " . $row['tong_cau'] . "</td>";
            echo "<td style='color:red; font-weight:bold;'>" . $row['diem'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='text-align:center;'>Bạn chưa thực hiện bài thi nào.</p>";
    }
    ?>
    
    <a href="index.php" class="btn-back">← Về trang chủ</a>
</div>

</body>
</html>