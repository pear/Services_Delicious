<?php
if (file_exists('config-local.php')) {
    require_once 'config-local.php';
} else {
    $username = '';
    $password = '';
}

if (empty($username)) {
    echo 'skip add your username and password';
    exit();
}
?>
