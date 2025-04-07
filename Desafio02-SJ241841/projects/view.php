<?php
require_once '../auth/auth_check.php';
require_once '../models/Project.php';
require_once '../models/File.php';

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

// Obtener los archivos del proyecto
$file = new File();
$files = $file->getByProjectId($project_id);

// Mensaje de estado para subida/eliminación de archivos
$message = '';
if (isset($_GET['status']) && isset($_GET['msg'])) {
    if ($_GET['status'] === 'success') {
        $message = '<div class="alert alert-success">' . htmlspecialchars($_GET['msg']) . '</div>';
    } else {
        $message = '<div class="alert alert-error">' . htmlspecialchars($_GET['msg']) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($project_data['title']); ?> - Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?php echo htmlspecialchars($project_data['title']); ?></h1>
            <div class="navigation">
                <a href="../index.php">Volver al Inicio</a> | 
                <a href="../auth/logout.php">Cerrar sesión</a>
            </div>
        </header>
        
        <main>
            <?php echo $message; ?>
            
            <div class="project-details">
                <h2>Detalles del Proyecto</h2>
                <div class="description">
                    <h3>Descripción</h3>
                    <p><?php echo nl2br(htmlspecialchars($project_data['description'])); ?></p>
                </div>
                
                <div class="meta-info">
                    <p><strong>Creado:</strong> <?php echo date('d/m/Y H:i', strtotime($project_data['created_at'])); ?></p>
                    <p><strong>Última actualización:</strong> <?php echo date('d/m/Y H:i', strtotime($project_data['updated_at'])); ?></p>
                </div>
                
                <div class="actions">
                    <a href="edit.php?id=<?php echo $project_id; ?>" class="btn btn-edit">Editar Proyecto</a>
                    <a href="delete.php?id=<?php echo $project_id; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de querer eliminar este proyecto? Esta acción no se puede deshacer.')">Eliminar Proyecto</a>
                </div>
            </div>
            
            <div class="files-section">
                <h2>Archivos del Proyecto</h2>
                
                <!-- Formulario de subida de archivos -->
                <div class="upload-form">
                    <h3>Subir Nuevo Archivo</h3>
                    <form action="../files/upload.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                        <div class="form-group">
                            <label for="file">Seleccionar archivo (PDF o imagen):</label>
                            <input type="file" id="file" name="file" required accept=".pdf,.jpg,.jpeg,.png,.gif">
                        </div>
                        <button type="submit" class="btn btn-primary">Subir Archivo</button>
                    </form>
                </div>
                
                <!-- Lista de archivos -->
                <div class="files-list">
                    <h3>Archivos Existentes</h3>
                    <?php if (count($files) > 0): ?>
                        <ul>
                            <?php foreach ($files as $f): ?>
                                <li>
                                    <div class="file-info">
                                        <span class="file-icon"><?php echo $f['filetype'] === 'pdf' ? '📄' : '🖼️'; ?></span>
                                        <span class="file-name"><?php echo htmlspecialchars($f['filename']); ?></span>
                                        <span class="file-date">Subido: <?php echo date('d/m/Y H:i', strtotime($f['uploaded_at'])); ?></span>
                                        <div class="file-actions">
                                            <a href="../uploads/<?php echo $f['filepath']; ?>" target="_blank" class="btn btn-view">Ver</a>
                                            <a href="../files/delete.php?id=<?php echo $f['id']; ?>&project_id=<?php echo $project_id; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de querer eliminar este archivo?')">Eliminar</a>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-records">No hay archivos asociados a este proyecto.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión de Proyectos</p>
        </footer>
    </div>
</body>
</html>
