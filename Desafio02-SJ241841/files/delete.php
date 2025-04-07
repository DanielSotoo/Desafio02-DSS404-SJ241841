<?php
require_once '../auth/auth_check.php';
require_once '../models/File.php';

// Verificar que se proporcionen los parámetros necesarios
if (!isset($_GET['id']) || !isset($_GET['project_id'])) {
    header("Location: ../index.php");
    exit();
}

$file_id = intval($_GET['id']);
$project_id = intval($_GET['project_id']);

// Eliminar el archivo
$file = new File();
$result = $file->delete($file_id, $_SESSION['user_id']);

// Redirigir con mensaje de estado
if ($result['success']) {
    header("Location: ../projects/view.php?id=$project_id&status=success&msg=" . urlencode($result['message']));
} else {
    header("Location: ../projects/view.php?id=$project_id&status=error&msg=" . urlencode($result['message']));
}
exit();
