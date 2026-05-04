<?php
session_start();
include 'connect.php';

// Nếu đã đăng nhập rồi thì đẩy thẳng vào trang chủ
if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Kiểm tra tài khoản trong database
    $sql = "SELECT * FROM TaiKhoan WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        // Đăng nhập thành công -> Lưu tên vào Session và chuyển hướng
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Tài khoản hoặc mật khẩu không chính xác!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; padding: 50px; }
        .login-box { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #4267b2; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        button { background-color: #4267b2; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; width: 100%; font-size: 16px; font-weight: bold; margin-top: 10px; transition: 0.3s;}
        button:hover { background-color: #365899; }
        .error { color: #dc3545; text-align: center; font-weight: bold; margin-bottom: 10px; }
        .pwd-container { position: relative; }
        .toggle-pwd { position: absolute; right: 15px; top: 22px; cursor: pointer; user-select: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>ĐĂNG NHẬP</h2>
        <?php if($error != '') echo "<p class='error'>$error</p>"; ?>
        <form method="POST" action="">
            <label>Tên đăng nhập:</label>
            <input type="text" name="username" required placeholder="Nhập tài khoản...">
            
            <label>Mật khẩu:</label>
            <div class="pwd-container">
                <input type="password" name="password" id="pwd" required placeholder="Nhập mật khẩu...">
                <span class="toggle-pwd" onclick="togglePassword()">👁️</span>
            </div>
            
            <button type="submit">ĐĂNG NHẬP</button>
        </form>
        <p style="text-align: center; margin-top: 20px; font-size: 15px;">
            Chưa có tài khoản? <a href="register.php" style="color: #4267b2; font-weight: bold; text-decoration: none;">Tạo ngay</a>
        </p>
    </div>

    <script>
        function togglePassword() {
            var pwdField = document.getElementById("pwd");
            if (pwdField.type === "password") {
                pwdField.type = "text";
            } else {
                pwdField.type = "password";
            }
        }
    </script>
</body>
</html>