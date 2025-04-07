<?php
require_once 'models/Project.php';
require_once 'auth/auth_check.php';

$project = new Project();
$projects = $project->getByUserId($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Sistema de Gestión de Proyectos</h1>
            <div class="user-info">
                Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?> | 
                <a href="auth/logout.php">Cerrar sesión</a>
            </div>
        </header>
        
        <main>
            <div class="actions">
                <a href="projects/create.php" class="btn btn-primary">Nuevo Proyecto</a>
            </div>
            
            <h2>Mis Proyectos</h2>
            
            <?php if (count($projects) > 0): ?>
                <div class="projects-list">
                    <?php foreach ($projects as $proj): ?>
                        <div class="project-card">
                            <h3><?php echo htmlspecialchars($proj['title']); ?></h3>
                            <p class="project-description">
                                <?php echo htmlspecialchars(substr($proj['description'], 0, 100)) . (strlen($proj['description']) > 100 ? '...' : ''); ?>
                            </p>
                            <div class="project-meta">
                                <span>Creado: <?php echo date('d/m/Y', strtotime($proj['created_at'])); ?></span>
                                <span>Actualizado: <?php echo date('d/m/Y', strtotime($proj['updated_at'])); ?></span>
                            </div>
                            <div class="project-actions">
                                <a href="projects/view.php?id=<?php echo $proj['id']; ?>" class="btn btn-view">Ver</a>
                                <a href="projects/edit.php?id=<?php echo $proj['id']; ?>" class="btn btn-edit">Editar</a>
                                <a href="projects/delete.php?id=<?php echo $proj['id']; ?>" class="btn btn-delete" onclick="return confirm('¿Estás seguro de querer eliminar este proyecto?')">Eliminar</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-records">No tienes proyectos creados. <a href="projects/create.php">Crea uno ahora</a>.</p>
            <?php endif; ?>
        </main>
        
        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistema de Gestión de Proyectos</p>
        </footer>
    </div>
</body>
</html>
