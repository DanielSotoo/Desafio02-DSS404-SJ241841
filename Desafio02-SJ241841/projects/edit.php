<?php
require_once '../auth/auth_check.php';
require_once '../models/Project.php';

$error = '';
$success = '';
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verificar que el ID sea válido
if ($project_id <= 0) {
    header("Location: ../index.php");
    exit();
}

// Obtener información del proyecto
$project = new Project();
$project_data = $project->getById($project_id);

// Verificar que el proyecto exista y pertenezca al usuario actual
if (!$project_data || $project_data['user_id'] != $_SESSION['user_id']) {
    header("Location: ../index.php");
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validaciones básicas
    if (empty($title)) {
        $error = "El título es obligatorio";
    } else {
        // Actualizar el proyecto
        $result = $project->update($project_id, $title, $description, $_SESSION['user_id']);
        
        if ($result['success']) {
            $success = $result['message'];
            // Actualizar los datos del proyecto en la vista
            $project_data['title'] = $title;
            $project_data['description'] = $description;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto - Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Proyecto</h1>
            <div class="navigation">
                <a href="../index.php">Volver al Inicio</a> | 
                <a href="../auth/logout.php">Cerrar sesión</a>
            </div>
        </header>
        
        <main>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="title">Título del proyecto:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($project_data['title']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Descripción:</label>
                    <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($project_data['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Actualizar Proyecto</button>
                    <a href="../index.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión de Proyectos</p>
        </footer>
    </div>
</body>
</html>
