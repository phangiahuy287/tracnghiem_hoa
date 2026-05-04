<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Làm Bài Thi</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f0f8ff; padding: 20px; color: #333;}
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #28a745; margin-bottom: 30px;}
        .question-card { margin-bottom: 25px; padding: 20px; border-left: 5px solid #0056b3; background-color: #fafafa; border-radius: 5px;}
        .q-title { font-weight: bold; font-size: 17px; margin-bottom: 15px; line-height: 1.5;}
        .q-badge { font-size: 12px; font-weight: normal; background: #e9ecef; padding: 4px 8px; border-radius: 12px; color: #666; margin-left: 10px; display: inline-block;}
        label { display: block; margin-bottom: 10px; cursor: pointer; padding: 10px; border-radius: 5px; transition: 0.2s; border: 1px solid transparent;}
        label:hover { background-color: #e2e6ea; border-color: #ccc;}
        .btn-submit { background-color: #28a745; color: white; padding: 15px; width: 100%; border: none; font-size: 18px; font-weight: bold; cursor: pointer; border-radius: 5px; margin-top: 20px;}
        .btn-submit:hover { background-color: #218838; }
        .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #0056b3; font-weight: bold;}
        .result-box { text-align:center; padding: 40px 0; background-color: #e9ecef; border-radius: 10px; }
        .score-text { font-size: 35px; font-weight: bold; color: #dc3545; margin: 15px 0; }
    </style>
</head>
<body>

<div class="container">
    
    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // LUỒNG 1: XỬ LÝ CHẤM ĐIỂM (Chỉ chạy khi ấn nút Nộp bài)
        if (isset($_POST['action_type']) && $_POST['action_type'] == 'nop_bai') {
            $diem = 0;
            $tong_cau = 0;

            foreach ($_POST as $key => $dapan_chon) {
                // Lọc lấy những dữ liệu gửi lên bắt đầu bằng chữ 'cauhoi_'
                if (strpos($key, 'cauhoi_') === 0) {
                    $tong_cau++;
                    $id_cauhoi = str_replace('cauhoi_', '', $key);

                    // Truy vấn đáp án đúng từ Database
                    $sql_check = "SELECT dapan_dung FROM CauHoi WHERE id = $id_cauhoi";
                    $res_check = $conn->query($sql_check);
                    
                    if ($row_check = $res_check->fetch_assoc()) {
                        if ($dapan_chon == $row_check['dapan_dung']) {
                            $diem++;
                        }
                    }
                }
            }

            // Tính điểm hệ 10
            $diem_he_10 = ($tong_cau > 0) ? round(($diem / $tong_cau) * 10, 2) : 0;

            // In kết quả
            echo "<div class='result-box'>";
            echo "<h2 style='color:#0056b3; margin-top:0;'>KẾT QUẢ CỦA BẠN</h2>";
            echo "<p style='font-size: 20px;'>Số câu đúng: <strong>$diem / $tong_cau</strong></p>";
            echo "<p class='score-text'>$diem_he_10 Điểm</p>";
            echo "<a href='index.php' class='btn-submit' style='display:inline-block; width:auto; text-decoration:none; margin-top:20px;'>Làm đề thi mới</a>";
            echo "</div>";
        } 
        
        // LUỒNG 2: XỬ LÝ TẠO ĐỀ (Khi từ trang chủ index.php chuyển sang)
        else {
            echo "<h2>BÀI THI TRẮC NGHIỆM HÓA HỌC</h2>";
            
            // DÙNG ISSET ĐỂ TRÁNH LỖI WARNING BẠN VỪA GẶP
            $lop = isset($_POST['lop']) ? (int)$_POST['lop'] : 0;
            $hoc_ki = isset($_POST['hoc_ki']) ? (int)$_POST['hoc_ki'] : 0;
            $chuong = isset($_POST['chuong']) ? $_POST['chuong'] : 'Tất cả';
            $muc_do = isset($_POST['muc_do']) ? (int)$_POST['muc_do'] : 0;
            $so_luong = isset($_POST['so_luong']) ? (int)$_POST['so_luong'] : 10;

            // Nối chuỗi SQL thông minh
            $sql = "SELECT * FROM CauHoi WHERE lop = $lop";
            if ($hoc_ki != 0) $sql .= " AND hoc_ki = $hoc_ki";
            if ($chuong != 'Tất cả') $sql .= " AND chuong = '$chuong'";
            if ($muc_do != 0) $sql .= " AND muc_do = $muc_do";
            $sql .= " ORDER BY RAND() LIMIT $so_luong";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                echo "<form action='lambai.php' method='POST'>";
                // Biến cờ hiệu báo cho hệ thống biết form này gửi đi là để nộp bài
                echo "<input type='hidden' name='action_type' value='nop_bai'>"; 
                
                $stt = 1;
                while($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    echo "<div class='question-card'>";
                    echo "<div class='q-title'>Câu $stt: " . $row["noidung"] . "<span class='q-badge'>" . $row['chuong'] . "</span></div>";

                    echo "<label><input type='radio' name='cauhoi_$id' value='A' required> A. " . $row["dapan_a"] . "</label>";
                    echo "<label><input type='radio' name='cauhoi_$id' value='B'> B. " . $row["dapan_b"] . "</label>";
                    echo "<label><input type='radio' name='cauhoi_$id' value='C'> C. " . $row["dapan_c"] . "</label>";
                    echo "<label><input type='radio' name='cauhoi_$id' value='D'> D. " . $row["dapan_d"] . "</label>";
                    echo "</div>";
                    $stt++;
                }
                echo '<button type="submit" class="btn-submit">NỘP BÀI & CHẤM ĐIỂM</button>';
                echo "</form>";
            } else {
                echo "<div style='text-align:center; padding: 40px 0;'>";
                echo "<h3 style='color:#dc3545;'>Rất tiếc, ngân hàng đề hiện không có đủ câu hỏi thỏa mãn bộ lọc!</h3>";
                echo "<a href='index.php' class='back-link'>← Quay lại trang cấu hình</a>";
                echo "</div>";
            }
        }
    } else {
        echo "<div style='text-align:center;'><p>Vui lòng truy cập từ trang chủ.</p><a href='index.php' class='back-link'>← Về trang chủ</a></div>";
    }
    ?>
</div>

</body>
</html>