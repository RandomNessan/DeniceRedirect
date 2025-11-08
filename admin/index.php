<?php
session_start();
require_once '../config/db.php';

// 确保 session 正常存储
if (!isset($_SESSION['admin']) || empty($_SESSION['admin'])) {
    die("会话丢失，请重新登录。<a href='login.php'>点击这里登录</a>");
}

// 处理删除请求
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM urls WHERE id = ?");
    $stmt->execute([$delete_id]);
    header("Location: index.php");
    exit();
}

// 获取所有 URL，并按 ID 升序排列
$stmt = $pdo->query("SELECT * FROM urls ORDER BY id ASC");
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>管理后台</title>
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

        /* 右侧表格内容 */
        .main-content {
            flex: 1;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #34495e;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .delete-btn {
            padding: 6px 12px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>

    <!-- 左侧导航栏 -->
    <div class="sidebar">
        <h1>管理后台</h1>
        <a href="add_url.php" class="btn">添加 URL</a>
        <a href="update_admin.php" class="btn">修改管理员账号</a>
        <a href="logout.php" class="btn logout">退出登录</a>
    </div>

    <!-- 右侧表格 -->
    <div class="main-content">
        <h2>URL 列表</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>URL</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
            <?php foreach ($urls as $url): ?>
                <tr>
                    <td><?= htmlspecialchars($url['id']) ?></td>
                    <td><?= htmlspecialchars($url['url']) ?></td>
                    <td><?= htmlspecialchars($url['created_at']) ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('确定要删除这个 URL 吗？');">
                            <input type="hidden" name="delete_id" value="<?= $url['id'] ?>">
                            <button type="submit" class="delete-btn">删除</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

</body>
</html>

