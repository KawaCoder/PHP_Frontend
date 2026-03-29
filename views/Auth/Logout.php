<?php
require_once __DIR__ . '/../../config/bootstrap.php';

use App\Controllers\Auth\LogoutController;

$controller = new LogoutController();
$controller->execute();

header('Location: /views/Auth/LoginView.php');
exit();
