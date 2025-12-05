<?php
class Motor {
    private $conn;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getAllMotors() {
        $query = "SELECT * FROM motors ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMotorById($id) {
        $query = "SELECT * FROM motors WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getMotorsByCategory($category) {
        $query = "SELECT * FROM motors WHERE category = :category";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>