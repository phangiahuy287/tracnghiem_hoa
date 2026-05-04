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
    <title>Đánh Giá Quá Trình Học Tập</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; padding: 20px;}
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #d35400;}
        .box-can-canh { background-color: #fff3cd; padding: 20px; border-radius: 5px; border-left: 5px solid #ffc107; margin-top:20px; line-height: 1.6;}
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
    echo "<h2>BÁO CÁO PHÂN TÍCH KIẾN THỨC CỦA: " . mb_strtoupper($ten_hocsinh, 'UTF-8') . "</h2>";
    echo "<p style='text-align:center;'>Dựa trên lịch sử các bài kiểm tra đã làm</p>";
    
    // Lấy dữ liệu thống kê của đúng user đó
    $sql = "SELECT chi_tiet_sai FROM KetQuaThi WHERE ten_hocsinh = '$ten_hocsinh'";
    $result = $conn->query($sql);
    
    $tong_hop_loi = [];
    $tong_so_bai = $result->num_rows;

    if ($tong_so_bai > 0) {
        while($row = $result->fetch_assoc()) {
            $loi_cua_bai = json_decode($row['chi_tiet_sai'], true);
            if(is_array($loi_cua_bai)){
                foreach ($loi_cua_bai as $chuong => $so_loi) {
                    if(!isset($tong_hop_loi[$chuong])) {
                        $tong_hop_loi[$chuong] = $so_loi;
                    } else {
                        $tong_hop_loi[$chuong] += $so_loi;
                    }
                }
            }
        }
        
        arsort($tong_hop_loi);
        
        if (count($tong_hop_loi) > 0) {
            echo "<div class='box-can-canh'>";
            echo "<strong>Đánh giá năng lực:</strong> Qua hệ thống phân tích $tong_so_bai bài kiểm tra gần nhất, bạn thường xuyên làm sai và bị hổng kiến thức nhiều nhất ở các phần sau:<br><br>";
            
            echo "<ul>";
            $dem = 0;
            foreach ($tong_hop_loi as $chuong => $tong_loi) {
                echo "<li><strong>$chuong</strong> (Mắc tổng cộng $tong_loi lỗi sai)</li>";
                $dem++;
                if($dem >= 3) break; 
            }
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<p style='text-align:center; color:green;'>Tuyệt vời! Hệ thống ghi nhận bạn chưa mắc lỗi sai nào ở các bài thi trước.</p>";
        }
        
    } else {
        echo "<p style='text-align:center;'>Bạn chưa có dữ liệu làm bài để thống kê.</p>";
    }
    ?>
    <a href="index.php" class="btn-back">← Về trang chủ</a>
</div>

</body>
</html>