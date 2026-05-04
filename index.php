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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chương Trình Trắc Nghiệm Hóa Học</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; padding: 20px; color: #333;}
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #4267b2; margin-bottom: 25px;}
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 8px; color: #555;}
        select, input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 15px;}
        .btn { background-color: #4267b2; color: white; padding: 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 18px; font-weight: bold; margin-top: 10px; }
        .btn:hover { background-color: #365899; }
        .nav-links { text-align: center; margin-top: 15px; }
        .nav-links a { display: inline-block; margin: 0 10px; color: #e67e22; font-weight: bold; text-decoration: none; }
        .user-bar { background: #e9ecef; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: right; font-size: 14px; }
        .user-bar a { color: #dc3545; text-decoration: none; font-weight: bold; margin-left: 10px; }
    </style>
</head>
<body>

<div class="container">
    <!-- Hiển thị người đang đăng nhập và nút Đăng xuất -->
    <div class="user-bar">
        👤 Xin chào, <strong><?php echo $_SESSION['username']; ?></strong> | 
        <a href="logout.php">Đăng xuất</a>
    </div>

    <h2>CHƯƠNG TRÌNH TRẮC NGHIỆM HÓA HỌC</h2>
    <form action="lambai.php" method="POST">
        
        <div class="form-group">
            <label>Chọn khối lớp:</label>
            <select name="lop" id="select_lop" required>
                <option value="10">Hóa học Lớp 10</option>
                <option value="11">Hóa học Lớp 11</option>
                <option value="12">Hóa học Lớp 12</option>
            </select>
        </div>

        <div class="form-group">
            <label>Chọn Học kì:</label>
            <select name="hoc_ki">
                <option value="0">Cả năm</option>
                <option value="1">Học kì 1</option>
                <option value="2">Học kì 2</option>
            </select>
        </div>

        <div class="form-group">
            <label>Chọn Chương:</label>
            <select name="chuong" id="select_chuong">
                <option value="Tất cả" data-lop="all">Tất cả các chương</option>
                <?php
                $sql_chuong = "SELECT DISTINCT chuong, lop FROM CauHoi ORDER BY lop, chuong";
                $result_chuong = $conn->query($sql_chuong);
                if ($result_chuong && $result_chuong->num_rows > 0) {
                    while($row = $result_chuong->fetch_assoc()) {
                        echo "<option value='" . $row['chuong'] . "' data-lop='" . $row['lop'] . "'>" . $row['chuong'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Số lượng câu hỏi muốn làm:</label>
            <input type="number" name="so_luong" min="1" max="100" value="10" required>
        </div>

        <button type="submit" class="btn">TẠO ĐỀ & BẮT ĐẦU LÀM BÀI</button>
    </form>
    
   <div class="nav-links">
        <a href="lichsu.php">📋 Lịch sử làm bài</a>
        <a href="thongke.php">📊 Thống kê kiến thức</a>
        <?php 
        // Chỉ hiện nút này nếu người đăng nhập là admin
        if ($_SESSION['username'] === 'admin') {
            echo '<a href="admin.php" style="color: #dc3545;">⚙️ Quản lý hệ thống</a>';
        }
        ?>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var selectLop = document.getElementById("select_lop");
        var selectChuong = document.getElementById("select_chuong");
        var allChapters = Array.from(selectChuong.querySelectorAll("option"));

        function filterChuong() {
            var selectedLop = selectLop.value;
            selectChuong.innerHTML = "";
            allChapters.forEach(function(opt) {
                var optLop = opt.getAttribute("data-lop");
                if (optLop === "all" || optLop === selectedLop) {
                    selectChuong.appendChild(opt);
                }
            });
            selectChuong.value = "Tất cả";
        }
        selectLop.addEventListener("change", filterChuong);
        filterChuong();
    });
</script>

</body>
</html>