<?php
session_start();
include 'connect.php';

// Khóa cửa: Nếu không phải tài khoản 'admin' thì đuổi ra ngoài
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    die("<h2 style='text-align:center; color:red; margin-top:50px;'>⛔ BẠN KHÔNG CÓ QUYỀN TRUY CẬP TRANG NÀY!</h2>");
}

// Xử lý XÓA tài khoản
if (isset($_GET['delete'])) {
    $del_user = $_GET['delete'];
    if ($del_user !== 'admin') { // Không cho phép xóa chính admin
        $conn->query("DELETE FROM TaiKhoan WHERE username = '$del_user'");
        $conn->query("DELETE FROM KetQuaThi WHERE ten_hocsinh = '$del_user'"); // Xóa luôn lịch sử thi của học sinh đó
        header("Location: admin.php");
        exit;
    }
}

// Xử lý THÊM tài khoản thủ công từ Admin
if (isset($_POST['add_user'])) {
    $new_u = $_POST['new_username'];
    $new_p = $_POST['new_password'];
    $check = $conn->query("SELECT * FROM TaiKhoan WHERE username = '$new_u'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO TaiKhoan (username, password) VALUES ('$new_u', '$new_p')");
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị Admin</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background-color: #f4f7f6; padding: 20px; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; color: #dc3545; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #343a40; color: white; }
        .action-link { padding: 6px 12px; text-decoration: none; color: white; border-radius: 4px; font-size: 13px; font-weight: bold; margin: 0 3px; display: inline-block;}
        .bg-blue { background-color: #007bff; }
        .bg-orange { background-color: #fd7e14; }
        .bg-red { background-color: #dc3545; }
        .add-form { background: #e9ecef; padding: 15px; border-radius: 5px; margin-top: 30px; }
        .add-form input { padding: 8px; margin-right: 10px; border-radius: 4px; border: 1px solid #ccc;}
        .add-form button { padding: 8px 15px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight:bold;}
    </style>
</head>
<body>

<div class="container">
    <h2>⚙️ HỆ THỐNG QUẢN TRỊ ADMIN ⚙️</h2>
    
    <div style="text-align:right;">
        <a href="index.php" style="color: #007bff; font-weight: bold; text-decoration:none;">🏠 Về Trang Chủ</a>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Tên tài khoản</th>
            <!-- Đã bỏ chữ (Chỉ dành cho Admin) -->
            <th>Hành động</th>
        </tr>
        <?php
        $result = $conn->query("SELECT * FROM TaiKhoan ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td><strong>" . $row['username'] . "</strong></td>";
            
            if ($row['username'] === 'admin') {
                // Đã đổi thành "Tài khoản giáo viên"
                echo "<td><span style='color:gray; font-weight:bold;'>Tài khoản giáo viên</span></td>";
            } else {
                echo "<td>";
                // Chuyền biến ?view_user qua URL để xem lén
                echo "<a href='lichsu.php?view_user=" . $row['username'] . "' class='action-link bg-blue'>Xem Lịch Sử</a>";
                echo "<a href='thongke.php?view_user=" . $row['username'] . "' class='action-link bg-orange'>Xem Phân Tích</a>";
                echo "<a href='admin.php?delete=" . $row['username'] . "' class='action-link bg-red' onclick=\"return confirm('Bạn có chắc chắn muốn xóa học sinh này và toàn bộ điểm số của họ?');\">Xóa</a>";
                echo "</td>";
            }
            echo "</tr>";
        }
        ?>
    </table>

    <div class="add-form">
        <h3>+ Thêm Nhanh Tài Khoản Học Sinh</h3>
        <form method="POST" action="" style="text-align: center;">
            <input type="text" name="new_username" required placeholder="Tên đăng nhập mới...">
            <input type="text" name="new_password" required placeholder="Mật khẩu...">
            <button type="submit" name="add_user">+ THÊM TÀI KHOẢN</button>
        </form>
    </div>
</div>

</body>
</html>