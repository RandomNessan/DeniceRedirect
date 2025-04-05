<?php
$newPassword = 'admin';
$newHash = password_hash($newPassword, PASSWORD_BCRYPT);
echo $newHash;
?>