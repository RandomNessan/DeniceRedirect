<?php
session_start();
require_once '../config/db.php';

// 只有管理员能访问
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    die("会话丢失，请重新登录。<a href='login.php'>点击这里登录</a>");
}

// 处理表单提交
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST['username']);
    $new_password = trim($_POST['password']);
    
    if (empty($new_username) || empty($new_password)) {
        $message = "用户名和密码不能为空！";
    } else {
        // 使用更安全的密码哈希
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE admins SET username = ?, password_hash = ? WHERE id = 1");
        $stmt->execute([$new_username, $password_hash]);

        // 更新 session，防止被踢出
        $_SESSION['admin'] = $new_username;

        $message = "管理员账号修改成功！";
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>修改管理员账号</title>
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

        /* 右侧内容 */
        .main-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            width: 400px;
            background: white;
            padding: 20px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            text-align: center;
            color: green;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <!-- 左侧导航栏 -->
    <div class="sidebar">
        <h1>管理后台</h1>
        <a href="index.php" class="btn">返回首页</a>
        <a href="add_url.php" class="btn">添加 URL</a>
        <a href="logout.php" class="btn logout">退出登录</a>
    </div>

    <!-- 右侧表单 -->
    <div class="main-content">
        <div class="form-container">
            <h2>修改管理员账号</h2>
            <form method="POST">
                <label>新用户名：</label>
                <input type="text" name="username" required>
                
                <label>新密码：</label>
                <input type="password" name="password" required>
                
                <button type="submit">提交</button>
            </form>
            <?php if (!empty($message)): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>

