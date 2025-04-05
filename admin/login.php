<?php
session_start();
require_once '../config/db.php';

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // 使用 password_verify() 进行密码验证
    if ($admin && password_verify($_POST['password'], $admin['password_hash'])) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: index.php");
        exit();
    } else {
        $error_message = "用户名或密码错误！";
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>管理员登录</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #ecf0f1;
        }
        .login-container {
            width: 350px;
            background: white;
            padding: 30px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .error {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>管理员登录</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="用户名" required>
            <input type="password" name="password" placeholder="密码" required>
            <button type="submit">登录</button>
        </form>
        <?php if (!empty($error_message)): ?>
            <p class="error"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
    </div>

</body>
</html>
