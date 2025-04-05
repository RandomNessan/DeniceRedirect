<?php
// 载入 .env 文件
$envPath = dirname(__DIR__) . '/.env';
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
} else {
    die("缺少 .env 文件！");
}

// 读取数据库配置
$host = $env['DB_HOST'] ?? 'localhost';
$dbname = $env['DB_NAME'] ?? 'pool_route';
$username = $env['DB_USER'] ?? 'root';
$password = $env['DB_PASS'] ?? '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败：" . $e->getMessage());
}
?>
