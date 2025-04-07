<?php
require_once '../auth/auth_check.php';
require_once '../models/Project.php';

$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar que el ID sea válido
if ($project_id <= 0) {
    header("Location: ../index.php");
    exit();
}

// Verificar que el proyecto exista y pertenezca al usuario
$project = new Project();
if (!$project->isOwner($project_id, $_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Si se confirma la eliminación
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $result = $project->delete($project_id, $_SESSION['user_id']);
    
    if ($result['success']) {
        header("Location: ../index.php?status=success&msg=" . urlencode("Proyecto eliminado correctamente"));
    } else {
        header("Location: ../index.php?status=error&msg=" . urlencode($result['message']));
    }
    exit();
}

// Obtener datos del proyecto para mostrar confirmación
$project_data = $project->getById($project_id);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Proyecto - Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Eliminar Proyecto</h1>
            <div class="navigation">
                <a href="../index.php">Volver al Inicio</a> | 
                <a href="../auth/logout.php">Cerrar sesión</a>
            </div>
        </header>
        
        <main>
            <div class="confirm-delete">
                <h2>¿Estás seguro de que deseas eliminar este proyecto?</h2>
                <p><strong>Título:</strong> <?php echo htmlspecialchars($project_data['title']); ?></p>
                <p><strong>Descripción:</strong> <?php echo nl2br(htmlspecialchars($project_data['description'])); ?></p>
                <p class="warning">Esta acción eliminará el proyecto y todos sus archivos asociados. No se puede deshacer.</p>
                
                <div class="actions">
                    <a href="delete.php?id=<?php echo $project_id; ?>&confirm=yes" class="btn btn-delete">Sí, eliminar</a>
                    <a href="../index.php" class="btn btn-secondary">No, cancelar</a>
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión de Proyectos</p>
        </footer>
    </div>
</body>
</html>
