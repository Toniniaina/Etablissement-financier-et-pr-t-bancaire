<?php
namespace Models;
use PDO;
require_once __DIR__ . '/../db.php';


class Fond {
    // Récupérer tous les fonds
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Fonds");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un fonds par son ID
    public static function getById($id_fonds) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Fonds WHERE id_fonds = ?");
        $stmt->execute([$id_fonds]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau fonds
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Fonds (montant_fonds) VALUES (?)");
        $stmt->execute([$data->montant_fonds]);
        return $db->lastInsertId();
    }

    // Mettre à jour un fonds
    public static function update($id_fonds, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Fonds SET montant_fonds = ? WHERE id_fonds = ?");
        $stmt->execute([$data->montant_fonds, $id_fonds]);
    }

    // Supprimer un fonds
    public static function delete($id_fonds) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Fonds WHERE id_fonds = ?");
        $stmt->execute([$id_fonds]);
    }
}