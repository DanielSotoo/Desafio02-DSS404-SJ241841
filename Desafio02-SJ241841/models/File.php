<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/Project.php';

/**
 * Clase File - Maneja todas las operaciones relacionadas con archivos
 */
class File {
    private $conn;
    private $project;
    private $upload_dir = '../uploads/';  // Directorio de subida relativo
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
        $this->project = new Project();
        
        // Crear directorio de uploads si no existe
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }
    
    /**
     * Sube un archivo y lo asocia a un proyecto
     */
    public function upload($file, $project_id, $user_id) {
        // Verificar si el usuario es propietario del proyecto
        if (!$this->project->isOwner($project_id, $user_id)) {
            return ["success" => false, "message" => "No tienes permiso para subir archivos a este proyecto"];
        }
        
        // Verificar si hay errores en la subida
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ["success" => false, "message" => "Error al subir el archivo"];
        }
        
        // Verificar el tipo de archivo (PDF o imágenes)
        $allowed_types = ['application/pdf', 'image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            return ["success" => false, "message" => "Tipo de archivo no permitido. Solo se permiten PDF e imágenes."];
        }
        
        // Generar nombre único para el archivo
        $filename = uniqid() . '_' . basename($file['name']);
        $filepath = $this->upload_dir . $filename;
        
        // Mover el archivo al directorio de subidas
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            try {
                // Determinar el tipo de archivo
                $filetype = '';
                if ($file['type'] === 'application/pdf') {
                    $filetype = 'pdf';
                } else {
                    $filetype = 'img';
                }
                
                // Registrar en la base de datos
                $stmt = $this->conn->prepare("INSERT INTO files (filename, filetype, filepath, project_id) VALUES (?, ?, ?, ?)");
                $result = $stmt->execute([
                    basename($file['name']),
                    $filetype,
                    $filename,  // Solo guardamos el nombre, no la ruta completa
                    $project_id
                ]);
                
                if ($result) {
                    return ["success" => true, "message" => "Archivo subido correctamente"];
                } else {
                    // Si falla la inserción en la BD, eliminar el archivo
                    unlink($filepath);
                    return ["success" => false, "message" => "Error al registrar el archivo en la base de datos"];
                }
            } catch (PDOException $e) {
                // En caso de error en la BD, eliminar el archivo
                unlink($filepath);
                return ["success" => false, "message" => "Error: " . $e->getMessage()];
            }
        } else {
            return ["success" => false, "message" => "Error al mover el archivo al servidor"];
        }
    }
    
    /**
     * Elimina un archivo
     */
    public function delete($file_id, $user_id) {
        try {
            // Primero obtenemos la información del archivo
            $stmt = $this->conn->prepare("
                SELECT f.*, p.user_id 
                FROM files f
                JOIN projects p ON f.project_id = p.id
                WHERE f.id = ?
            ");
            $stmt->execute([$file_id]);
            $file = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Verificar si el archivo existe
            if (!$file) {
                return ["success" => false, "message" => "El archivo no existe"];
            }
            
            // Verificar si el usuario es propietario del proyecto al que pertenece el archivo
            if ($file['user_id'] != $user_id) {
                return ["success" => false, "message" => "No tienes permiso para eliminar este archivo"];
            }
            
            // Eliminar el archivo físico
            $filepath = $this->upload_dir . $file['filepath'];
            if (file_exists($filepath)) {
                unlink($filepath);
            }
            
            // Eliminar el registro de la base de datos
            $stmt = $this->conn->prepare("DELETE FROM files WHERE id = ?");
            $result = $stmt->execute([$file_id]);
            
            if ($result) {
                return ["success" => true, "message" => "Archivo eliminado correctamente"];
            } else {
                return ["success" => false, "message" => "Error al eliminar el archivo de la base de datos"];
            }
        } catch (PDOException $e) {
            return ["success" => false, "message" => "Error: " . $e->getMessage()];
        }
    }
    
    /**
     * Obtiene todos los archivos de un proyecto
     */
    public function getByProjectId($project_id) {
        $stmt = $this->conn->prepare("SELECT * FROM files WHERE project_id = ? ORDER BY uploaded_at DESC");
        $stmt->execute([$project_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Obtiene un archivo por su ID
     */
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM files WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
