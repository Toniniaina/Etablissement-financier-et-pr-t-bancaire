<?php
namespace Models;
use PDO;
require_once __DIR__ . '/../db.php';

class MvtPret {
    // Récupérer tous les mouvements de prêts
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Mouvement_prets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un mouvement de prêt par son ID
    public static function getById($id_mouvement_prets) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Mouvement_prets WHERE id_mouvement_prets = ?");
        $stmt->execute([$id_mouvement_prets]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau mouvement de prêt
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES (?, ?, ?)");
        $stmt->execute([
            $data->id_prets,
            $data->id_status_prets,
            $data->date_mouvement
        ]);
        return $db->lastInsertId();
    }

    // Mettre à jour un mouvement de prêt
    public static function update($id_mouvement_prets, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Mouvement_prets SET id_prets = ?, id_status_prets = ?, date_mouvement = ? WHERE id_mouvement_prets = ?");
        $stmt->execute([
            $data->id_prets,
            $data->id_status_prets,
            $data->date_mouvement,
            $id_mouvement_prets
        ]);
    }

    // Supprimer un mouvement de prêt
    public static function delete($id_mouvement_prets) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Mouvement_prets WHERE id_mouvement_prets = ?");
        $stmt->execute([$id_mouvement_prets]);
    }
}