<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Clase User - Maneja todas las operaciones relacionadas con usuarios
 */
class User {
    private $conn;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Registra un nuevo usuario
     */
    public function register($name, $email, $password) {
        // Verificar si el correo ya existe
        if ($this->emailExists($email)) {
            return ["success" => false, "message" => "El correo electrónico ya está registrado"];
        }
        
        // Hash de la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = $this->conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $result = $stmt->execute([$name, $email, $hashed_password]);
            
            if ($result) {
                return ["success" => true, "message" => "Usuario registrado correctamente"];
            } else {
                return ["success" => false, "message" => "Error al registrar el usuario"];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
    /**
     * Verifica credenciales de inicio de sesión
     */
    public function login($email, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                // Iniciar sesión
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                return ["success" => true, "message" => "Inicio de sesión exitoso"];
            } else {
                return ["success" => false, "message" => "Correo o contraseña incorrectos"];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
    /**
     * Verifica si un correo electrónico ya existe
     */
    private function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
    
    /**
     * Obtiene información de un usuario por ID
     */
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        return ["success" => true, "message" => "Sesión cerrada correctamente"];
    }
}
