<?php
require_once '../models/User.php';

$error = '';

// Si ya hay sesión, redirigir al inicio
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validaciones básicas
    if (empty($email) || empty($password)) {
        $error = "Todos los campos son obligatorios";
    } else {
        // Intentar iniciar sesión
        $user = new User();
        $result = $user->login($email, $password);
        
        if ($result['success']) {
            // Redirigir al dashboard
            header("Location: ../index.php");
            exit();
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
    <title>Iniciar Sesión - Sistema de Proyectos</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
            </div>
        </form>
        
        <p>¿No tienes una cuenta? <a href="register.php">Registrarse</a></p>
    </div>
</body>
</html>
