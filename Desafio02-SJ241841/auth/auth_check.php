<?php
/**
 * Verifica si el usuario está autenticado
 * Este archivo se incluye en todas las páginas que requieren autenticación
 */
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    // Redirigir al login
    header("Location: ../auth/login.php");
    exit();
}
