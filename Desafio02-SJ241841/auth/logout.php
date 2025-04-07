<?php
require_once '../models/User.php';

// Cerrar la sesión
$user = new User();
$result = $user->logout();

// Redirigir al login
header("Location: login.php");
exit();
