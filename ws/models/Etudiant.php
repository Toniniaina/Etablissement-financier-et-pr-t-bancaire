<?php
require_once __DIR__ . '/../db.php';

class Etudiant {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM etudiant");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM etudiant WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO etudiant (nom, prenom, email, age) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE etudiant SET nom = ?, prenom = ?, email = ?, age = ? WHERE id = ?");
        $stmt->execute([$data->nom, $data->prenom, $data->email, $data->age, $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM etudiant WHERE id = ?");
        $stmt->execute([$id]);
    }
}
