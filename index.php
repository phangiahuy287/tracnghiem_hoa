<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chương Trình Trắc Nghiệm Hóa Học</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; padding: 20px; color: #333;}
        .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #4267b2; margin-bottom: 25px;}
        .form-group { margin-bottom: 20px; }
        label { font-weight: bold; display: block; margin-bottom: 8px; color: #555;}
        select, input { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; font-size: 15px;}
        select:focus, input:focus { outline: none; border-color: #4267b2;}
        .btn { background-color: #4267b2; color: white; padding: 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 18px; font-weight: bold; margin-top: 10px; transition: 0.3s;}
        .btn:hover { background-color: #365899; }
    </style>
</head>
<body>

<div class="container">
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
                // Tự động lấy danh sách các chương có sẵn trong database để hiển thị
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
            <label>Mức độ câu hỏi:</label>
            <select name="muc_do">
                <option value="0">Tất cả các mức độ</option>
                <option value="1">Dễ</option>
                <option value="2">Trung bình</option>
                <option value="3">Khó</option>
            </select>
        </div>

        <div class="form-group">
            <label>Số lượng câu hỏi muốn làm:</label>
            <input type="number" name="so_luong" min="1" max="100" value="10" required>
        </div>

        <button type="submit" class="btn">TẠO ĐỀ & BẮT ĐẦU LÀM BÀI</button>
    </form>
</div>

<!-- ĐOẠN JAVASCRIPT GIÚP LỌC CHƯƠNG THEO LỚP CHUẨN XÁC -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var selectLop = document.getElementById("select_lop");
        var selectChuong = document.getElementById("select_chuong");
        
        // Hút toàn bộ thẻ <option> của menu Chương vào một mảng (bộ nhớ tạm)
        var allChapters = Array.from(selectChuong.querySelectorAll("option"));

        function filterChuong() {
            var selectedLop = selectLop.value;
            
            // Quét sạch các lựa chọn đang hiển thị trên form
            selectChuong.innerHTML = "";

            // Duyệt qua bộ nhớ tạm, chương nào đúng với khối lớp đang chọn thì nhét lại vào form
            allChapters.forEach(function(opt) {
                var optLop = opt.getAttribute("data-lop");
                if (optLop === "all" || optLop === selectedLop) {
                    selectChuong.appendChild(opt);
                }
            });
            
            // Đưa menu về mặc định là "Tất cả các chương"
            selectChuong.value = "Tất cả";
        }

        // Bắt sự kiện: Cứ hễ người dùng bấm đổi Lớp là gọi hàm lọc Chương
        selectLop.addEventListener("change", filterChuong);
        
        // Chạy hàm lọc 1 lần ngay lúc web vừa load xong để làm sạch form
        filterChuong();
    });
</script>

</body>
</html>