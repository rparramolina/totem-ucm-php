<?php
/**
 * Logout - Destroy session and redirect to login
 */

session_start();
session_destroy();
header('Location: /admin/login.php');
exit;
