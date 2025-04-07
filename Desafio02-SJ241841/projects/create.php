<?php
require_once '../auth/auth_check.php';
require_once '../models/Project.php';

$error = '';
$success = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    // Validaciones básicas
    if (empty($title)) {
        $error = "El título es obligatorio";
    } else {
        // Crear el proyecto
        $project = new Project();
        $result = $project->create($title, $description, $_SESSION['user_id']);
        
        if ($result['success']) {
            $success = $result['message'];
            // Redirigir al listado de proyectos después de 2 segundos
            header("refresh:2;url=../index.php");
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
    <title>Crear Proyecto - Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Crear Nuevo Proyecto</h1>
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
                    <input type="text" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Descripción:</label>
                    <textarea id="description" name="description" rows="5"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Crear Proyecto</button>
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