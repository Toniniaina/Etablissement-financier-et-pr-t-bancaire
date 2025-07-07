<?php


require_once __DIR__ . '/../db.php';

class Taux {
    // Récupérer tous les taux
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Taux");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un taux par son ID
    public static function getById($id_taux) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Taux WHERE id_taux = ?");
        $stmt->execute([$id_taux]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau taux
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Taux (id_types_pret, pourcentage) VALUES (?, ?)");
        $stmt->execute([
            $data->id_types_pret,
            $data->pourcentage
        ]);
        return $db->lastInsertId();
    }

    // Mettre à jour un taux
    public static function update($id_taux, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Taux SET id_types_pret = ?, pourcentage = ? WHERE id_taux = ?");
        $stmt->execute([
            $data->id_types_pret,
            $data->pourcentage,
            $id_taux
        ]);
    }

    // Supprimer un taux
    public static function delete($id_taux) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Taux WHERE id_taux = ?");
        $stmt->execute([$id_taux]);
    }

    public static function getWithType() {
        $db = getDB();
        $stmt = $db->query("
            SELECT t.id_types_pret, tp.nom_type_pret, t.pourcentage
            FROM Taux t
            JOIN Types_pret tp ON t.id_types_pret = tp.id_types_pret
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}