<?php
session_start();
if (!isset($_SESSION['admin_uname'])) {
    header('Location: login.php');
    exit;
}
?>