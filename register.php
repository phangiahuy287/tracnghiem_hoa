<?php
session_start();
include 'connect.php';

$message = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra xem 2 mật khẩu có giống nhau không
    if ($password !== $confirm_password) {
        $message = "<p style='color: #dc3545; text-align: center; font-weight: bold;'>Mật khẩu nhập lại không khớp!</p>";
    } else {
        // Kiểm tra xem tên đăng nhập đã ai xài chưa
        $sql_check = "SELECT * FROM TaiKhoan WHERE username = '$username'";
        $result = $conn->query($sql_check);

        if ($result && $result->num_rows > 0) {
            $message = "<p style='color: #dc3545; text-align: center; font-weight: bold;'>Tài khoản này đã tồn tại! Vui lòng chọn tên khác.</p>";
        } else {
            $sql_insert = "INSERT INTO TaiKhoan (username, password) VALUES ('$username', '$password')";
            if ($conn->query($sql_insert)) {
                $message = "<p style='color: #28a745; text-align: center; font-weight: bold;'>Tạo tài khoản thành công! <a href='login.php' style='color:#0056b3; text-decoration:none;'>Đăng nhập ngay</a></p>";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo Tài Khoản</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; padding: 50px; }
        .login-box { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #28a745; margin-bottom: 20px;}
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; margin-top: 15px; transition: 0.3s;}
        button:hover { background-color: #218838; }
        .pwd-container { position: relative; }
        .toggle-pwd { position: absolute; right: 15px; top: 22px; cursor: pointer; user-select: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>TẠO TÀI KHOẢN MỚI</h2>
        <?php echo $message; ?>
        <form method="POST" action="">
            <label>Tên đăng nhập:</label>
            <input type="text" name="username" required placeholder="Nhập tên đăng nhập...">
            
            <label>Mật khẩu:</label>
            <div class="pwd-container">
                <input type="password" name="password" id="pwd1" required placeholder="Nhập mật khẩu...">
                <!-- Truyền ID của ô nhập vào hàm JavaScript để bật/tắt đúng ô -->
                <span class="toggle-pwd" onclick="togglePassword('pwd1')">👁️</span>
            </div>

            <label>Nhập lại mật khẩu:</label>
            <div class="pwd-container">
                <input type="password" name="confirm_password" id="pwd2" required placeholder="Xác nhận lại mật khẩu...">
                <span class="toggle-pwd" onclick="togglePassword('pwd2')">👁️</span>
            </div>
            
            <button type="submit">ĐĂNG KÝ TÀI KHOẢN</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            <a href="login.php" style="color: #666; text-decoration: none; font-weight: bold;">← Quay lại đăng nhập</a>
        </p>
    </div>

    <script>
        // Hàm này nhận ID của ô input làm tham số để biết cần ẩn/hiện ô nào
        function togglePassword(inputId) {
            var pwdField = document.getElementById(inputId);
            if (pwdField.type === "password") {
                pwdField.type = "text";
            } else {
                pwdField.type = "password";
            }
        }
    </script>
</body>
</html>