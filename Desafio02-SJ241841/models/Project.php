<?php
require_once __DIR__ . '/../config/db.php';

/**
 * Clase Project - Maneja todas las operaciones relacionadas con proyectos
 */
class Project {
    private $conn;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Crea un nuevo proyecto
     */
    public function create($title, $description, $user_id) {
        try {
            $stmt = $this->conn->prepare("INSERT INTO projects (title, description, user_id) VALUES (?, ?, ?)");
            $result = $stmt->execute([$title, $description, $user_id]);
            
            if ($result) {
                return [
                    "success" => true, 
                    "message" => "Proyecto creado correctamente",
                    "project_id" => $this->conn->lastInsertId()
                ];
            } else {
                return ["success" => false, "message" => "Error al crear el proyecto"];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
    /**
     * Actualiza un proyecto existente
     */
    public function update($id, $title, $description, $user_id) {
        // Verificar si el usuario es propietario del proyecto
        if (!$this->isOwner($id, $user_id)) {
            return ["success" => false, "message" => "No tienes permiso para editar este proyecto"];
        }
        
        try {
            $stmt = $this->conn->prepare("UPDATE projects SET title = ?, description = ? WHERE id = ? AND user_id = ?");
            $result = $stmt->execute([$title, $description, $id, $user_id]);
            
            if ($result) {
                return ["success" => true, "message" => "Proyecto actualizado correctamente"];
            } else {
                return ["success" => false, "message" => "Error al actualizar el proyecto"];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
    /**
     * Elimina un proyecto
     */
    public function delete($id, $user_id) {
        // Verificar si el usuario es propietario del proyecto
        if (!$this->isOwner($id, $user_id)) {
            return ["success" => false, "message" => "No tienes permiso para eliminar este proyecto"];
        }
        
        try {
            $stmt = $this->conn->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
            $result = $stmt->execute([$id, $user_id]);
            
            if ($result) {
                return ["success" => true, "message" => "Proyecto eliminado correctamente"];
            } else {
                return ["success" => false, "message" => "Error al eliminar el proyecto"];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
    /**
     * Obtiene un proyecto por ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene todos los proyectos de un usuario
     */
    public function getByUserId($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY updated_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Verifica si un usuario es propietario de un proyecto
     */
    public function isOwner($project_id, $user_id) {
        $stmt = $this->conn->prepare("SELECT id FROM projects WHERE id = ? AND user_id = ?");
        $stmt->execute([$project_id, $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
    }
}
