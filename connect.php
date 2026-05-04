<?php
$servername = "localhost";
$username = "root"; 
$password = "";     
$dbname = "hethong_hoahoc_thcs";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
// Set font chữ tiếng Việt
$conn->set_charset("utf8mb4");
?>