<?php
$inputPassword = 'admin'; // 你输入的密码
$storedHash = '$2y$10$FRnS692dKoOfiD/84k4tDeuzNdaj3KDwYqwY7UzbmJ8iG9aH4Roaa'; // 从数据库里取出的哈希

if (password_verify($inputPassword, $storedHash)) {
    echo "密码正确！";
} else {
    echo "密码错误！";
}
?>
