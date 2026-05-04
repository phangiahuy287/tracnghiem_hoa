<?php 
session_start();
// Khóa cửa: Chưa đăng nhập thì đuổi ra login.php
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
        
        .btn-submit { background-color: #28a745; color: white; padding: 15px; width: 100%; border: none; font-size: 18px; font-weight: bold; cursor: pointer; border-radius: 5px; margin-top: 20px; transition: 0.3s;}
        .btn-submit:hover { background-color: #218838; }
        .btn-print { background-color: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; float: right; margin-bottom: 20px; font-weight: bold;}
        
        .back-link { display: inline-block; margin-top: 20px; text-decoration: none; color: #0056b3; font-weight: bold;}
        .result-box { padding: 30px; background-color: #fff; border: 2px solid #28a745; border-radius: 10px; }
        .score-text { font-size: 35px; font-weight: bold; color: #dc3545; text-align: center; margin: 15px 0; }
        .danh-gia-box { background-color: #fff3cd; padding: 15px; border-radius: 5px; margin-top: 20px; border-left: 4px solid #ffc107;}
        .cau-sai-box { margin-top: 20px;}
        .cau-sai-item { background: #f8d7da; padding: 15px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid #dc3545;}
        
        /* CSS IN ẤN ĐỂ ẨN NGÀY THÁNG, LINK VÀ CĂN LỀ */
        @media print {
            @page { margin: 0; }
            body { background-color: white; margin: 1.5cm; padding: 0;}
            .container { box-shadow: none; max-width: 100%; padding: 0;}
            .btn-submit, .btn-print, .back-link { display: none !important; }
            label:hover { background-color: transparent; border-color: transparent;}
            label { padding: 4px; margin-bottom: 4px;} 
            .question-card { border-left: 2px solid #000; padding: 10px; margin-bottom: 15px;}
        }
    </style>
</head>
<body>

<div class="container">
    <?php
    // Lấy tên học sinh từ Session đăng nhập
    $ten_hocsinh = $_SESSION['username'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        // LUỒNG 1: XỬ LÝ CHẤM ĐIỂM
        if (isset($_POST['action_type']) && $_POST['action_type'] == 'nop_bai') {
            $diem = 0;
            $tong_cau = 0;
            $mang_cau_sai = []; 
            $thong_ke_chuong = []; 

            $lop_thi = isset($_POST['lop_thi']) ? (int)$_POST['lop_thi'] : 0;

            foreach ($_POST as $key => $dapan_chon) {
                if (strpos($key, 'cauhoi_') === 0) {
                    $tong_cau++;
                    $id_cauhoi = str_replace('cauhoi_', '', $key);

                    $sql_check = "SELECT noidung, dapan_a, dapan_b, dapan_c, dapan_d, dapan_dung, chuong FROM CauHoi WHERE id = $id_cauhoi";
                    $res_check = $conn->query($sql_check);
                    
                    if ($row_check = $res_check->fetch_assoc()) {
                        if ($dapan_chon == $row_check['dapan_dung']) {
                            $diem++;
                        } else {
                            $chuong_sai = $row_check['chuong'];
                            
                            $noi_dung_chon = "";
                            if($dapan_chon == 'A') $noi_dung_chon = $row_check['dapan_a'];
                            if($dapan_chon == 'B') $noi_dung_chon = $row_check['dapan_b'];
                            if($dapan_chon == 'C') $noi_dung_chon = $row_check['dapan_c'];
                            if($dapan_chon == 'D') $noi_dung_chon = $row_check['dapan_d'];

                            $noi_dung_dung = "";
                            if($row_check['dapan_dung'] == 'A') $noi_dung_dung = $row_check['dapan_a'];
                            if($row_check['dapan_dung'] == 'B') $noi_dung_dung = $row_check['dapan_b'];
                            if($row_check['dapan_dung'] == 'C') $noi_dung_dung = $row_check['dapan_c'];
                            if($row_check['dapan_dung'] == 'D') $noi_dung_dung = $row_check['dapan_d'];
                            
                            $mang_cau_sai[] = [
                                'noidung' => $row_check['noidung'],
                                'chon_chu_cai' => $dapan_chon,
                                'chon_noi_dung' => $noi_dung_chon,
                                'dung_chu_cai' => $row_check['dapan_dung'],
                                'dung_noi_dung' => $noi_dung_dung,
                                'chuong' => $chuong_sai
                            ];

                            if (!isset($thong_ke_chuong[$chuong_sai])) {
                                $thong_ke_chuong[$chuong_sai] = 1;
                            } else {
                                $thong_ke_chuong[$chuong_sai]++;
                            }
                        }
                    }
                }
            }

            $diem_he_10 = ($tong_cau > 0) ? round(($diem / $tong_cau) * 10, 2) : 0;

            // LƯU KẾT QUẢ VÀO DATABASE
            $chi_tiet_sai_json = json_encode($thong_ke_chuong, JSON_UNESCAPED_UNICODE);
            $sql_luu_ket_qua = "INSERT INTO KetQuaThi (ten_hocsinh, lop, tong_cau, so_cau_dung, diem, chi_tiet_sai) 
                                VALUES ('$ten_hocsinh', $lop_thi, $tong_cau, $diem, $diem_he_10, '$chi_tiet_sai_json')";
            $conn->query($sql_luu_ket_qua);

            // HIỂN THỊ KẾT QUẢ
            echo "<div class='result-box'>";
            echo "<h2 style='color:#0056b3; margin-top:0;'>KẾT QUẢ CỦA: ". mb_strtoupper($ten_hocsinh, 'UTF-8') ."</h2>";
            echo "<p style='text-align:center; font-size: 20px;'>Số câu đúng: <strong>$diem / $tong_cau</strong></p>";
            echo "<div class='score-text'>$diem_he_10 Điểm</div>";
            
            if ($diem == $tong_cau) {
                echo "<div class='danh-gia-box' style='background:#d4edda; border-color:#28a745;'>";
                echo "Xuất sắc! Bạn nắm rất vững kiến thức phần này.";
                echo "</div>";
            } else {
                echo "<div class='danh-gia-box'>";
                echo "Bạn cần ôn tập lại các kiến thức trọng tâm sau đây:<br>";
                echo "<ul>";
                foreach ($thong_ke_chuong as $ten_chuong => $so_loi) {
                    echo "<li>Sai $so_loi câu thuộc phạm vi: <strong>$ten_chuong</strong></li>";
                }
                echo "</ul>";
                echo "</div>";
                
                // Hiển thị chi tiết câu sai CÓ NỘI DUNG ĐÁP ÁN
                echo "<div class='cau-sai-box'>";
                echo "<h3>Xem lại các câu trả lời sai:</h3>";
                $stt_sai = 1;
                foreach ($mang_cau_sai as $cau_sai) {
                    echo "<div class='cau-sai-item'>";
                    echo "<strong>Câu $stt_sai:</strong> " . $cau_sai['noidung'] . "<br><br>";
                    echo "<span style='color: #dc3545;'>✖ Bạn chọn đáp án: <strong>" . $cau_sai['chon_chu_cai'] . ". " . $cau_sai['chon_noi_dung'] . "</strong></span><br>";
                    echo "<span style='color: #28a745;'>✔ Đáp án chuẩn là: <strong>" . $cau_sai['dung_chu_cai'] . ". " . $cau_sai['dung_noi_dung'] . "</strong></span><br>";
                    echo "<span style='font-size:12px; color:#666; font-style:italic;'>(Thuộc " . $cau_sai['chuong'] . ")</span>";
                    echo "</div>";
                    $stt_sai++;
                }
                echo "</div>";
            }

            echo "<div style='text-align:center; margin-top:30px;'>";
            echo "<a href='lichsu.php' style='margin-right:15px; text-decoration:none; font-weight:bold; color:#0056b3;'>[ Xem Lịch Sử ]</a>";
            echo "<a href='thongke.php' style='margin-right:15px; text-decoration:none; font-weight:bold; color:#d35400;'>[ Thống Kê ]</a>";
            echo "<a href='index.php' class='btn-submit' style='display:inline-block; width:auto; text-decoration:none;'>Làm đề thi mới</a>";
            echo "</div>";
            echo "</div>";
            
            echo "<style>form { display: none; } h2:first-of-type { display: none; } .btn-print {display: none;}</style>";
        } 
        
        // LUỒNG 2: XỬ LÝ TẠO ĐỀ & IN ĐỀ 
        else {
            echo "<button class='btn-print' onclick='window.print()'>🖨 In Đề Thi</button>";
            echo "<div style='clear:both;'></div>"; 
            
            echo "<h2>BÀI THI CỦA: ". mb_strtoupper($ten_hocsinh, 'UTF-8') ."</h2>";
            
            $lop = isset($_POST['lop']) ? (int)$_POST['lop'] : 0;
            $hoc_ki = isset($_POST['hoc_ki']) ? (int)$_POST['hoc_ki'] : 0;
            $chuong = isset($_POST['chuong']) ? $_POST['chuong'] : 'Tất cả';
            $so_luong = isset($_POST['so_luong']) ? (int)$_POST['so_luong'] : 10;

            $sql = "SELECT * FROM CauHoi WHERE lop = $lop";
            if ($hoc_ki != 0) $sql .= " AND hoc_ki = $hoc_ki";
            if ($chuong != 'Tất cả') $sql .= " AND chuong = '$chuong'";
            $sql .= " ORDER BY RAND() LIMIT $so_luong";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                echo "<form action='lambai.php' method='POST'>";
                echo "<input type='hidden' name='action_type' value='nop_bai'>"; 
                echo "<input type='hidden' name='lop_thi' value='$lop'>"; 
                
                $stt = 1;
                while($row = $result->fetch_assoc()) {
                    $id = $row['id'];
                    echo "<div class='question-card'>";
                    echo "<div class='q-title'>Câu $stt: " . $row["noidung"] . "</div>";

                    if (!empty($row['hinh_anh'])) {
                        echo "<div style='margin-bottom: 15px; text-align: center;'><img src='" . $row['hinh_anh'] . "' style='max-width: 100%; border-radius: 5px;'></div>";
                    }

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
                echo "<h3 style='color:#dc3545;'>Rất tiếc, ngân hàng đề hiện không có đủ câu hỏi thỏa mãn!</h3>";
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