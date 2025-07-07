<?php
require_once __DIR__ . '/../db.php';
class StatusPret {
    // Récupérer tous les statuts de prêts
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Status_prets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un statut par son ID
    public static function getById($id_status_prets) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Status_prets WHERE id_status_prets = ?");
        $stmt->execute([$id_status_prets]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau statut
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Status_prets (nom_status) VALUES (?)");
        $stmt->execute([$data->nom_status]);
        return $db->lastInsertId();
    }

    // Mettre à jour un statut
    public static function update($id_status_prets, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Status_prets SET nom_status = ? WHERE id_status_prets = ?");
        $stmt->execute([$data->nom_status, $id_status_prets]);
    }

    // Supprimer un statut
    public static function delete($id_status_prets) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Status_prets WHERE id_status_prets = ?");
        $stmt->execute([$id_status_prets]);
    }
}