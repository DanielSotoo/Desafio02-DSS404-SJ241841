<?php
require_once '../auth/auth_check.php';
require_once '../models/File.php';

// Verificar que sea una solicitud POST con un proyecto válido
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['project_id']) || intval($_POST['project_id']) <= 0) {
    header("Location: ../index.php");
    exit();
}

$project_id = intval($_POST['project_id']);

// Verificar que se haya enviado un archivo
if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
    header("Location: ../projects/view.php?id=$project_id&status=error&msg=" . urlencode("No se seleccionó ningún archivo"));
    exit();
}

// Procesar la subida del archivo
$file = new File();
$result = $file->upload($_FILES['file'], $project_id, $_SESSION['user_id']);

// Redirigir con mensaje de estado
if ($result['success']) {
    header("Location: ../projects/view.php?id=$project_id&status=success&msg=" . urlencode($result['message']));
} else {
    header("Location: ../projects/view.php?id=$project_id&status=error&msg=" . urlencode($result['message']));
}
exit();
