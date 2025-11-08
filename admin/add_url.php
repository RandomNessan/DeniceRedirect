<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $url = $_POST['url'];
    $stmt = $pdo->prepare("INSERT INTO urls (url) VALUES (?)");
    $stmt->execute([$url]);
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>添加 URL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            height: 100vh;
        }

        /* 左侧导航栏 */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
        }
        .sidebar h1 {
            margin-bottom: 20px;
            font-size: 20px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            text-align: center;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }
        .btn:hover {
            background-color: #2980b9;
        }
        .logout {
            background-color: #e74c3c;
        }
        .logout:hover {
            background-color: #c0392b;
        }

        /* 右侧表单卡片 */
        .main-content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f4f4;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        .card h2 {
            margin-bottom: 20px;
        }
        .input-box {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #27ae60;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #219150;
        }
    </style>
</head>
<body>

    <!-- 左侧导航栏 -->
    <div class="sidebar">
        <h1>管理后台</h1>
        <a href="index.php" class="btn">返回首页</a>
        <a href="update_admin.php" class="btn">修改管理员账号</a>
        <a href="logout.php" class="btn logout">退出登录</a>
    </div>

    <!-- 右侧表单 -->
    <div class="main-content">
        <div class="card">
            <h2>添加新 URL</h2>
            <form method="post">
                <input type="text" name="url" class="input-box" placeholder="输入 URL" required>
                <button type="submit" class="submit-btn">添加 URL</button>
            </form>
        </div>
    </div>

</body>
</html>
