<?php
/**
 * Clase Database - Maneja la conexión a la base de datos
 * Implementa el patrón Singleton para garantizar una única instancia de conexión
 */
class Database {
    private static $instance = null;
    private $conn;
    
    // Credenciales de la base de datos
    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $dbname = 'project_management';
    
    // Constructor privado - Solo puede ser instanciado dentro de la clase
    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname}",
                $this->user,
                $this->pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    // Método para obtener la instancia de la clase
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    // Método para obtener la conexión
    public function getConnection() {
        return $this->conn;
    }
    
    // Prevenir la clonación de la instancia
    private function __clone() {}
    
    // Prevenir la deserialización de la instancia
    private function __wakeup() {}
}
