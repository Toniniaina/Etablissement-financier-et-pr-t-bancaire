<?php

require_once __DIR__ . '/../db.php';

class Pret {
    // Récupérer tous les prêts
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Prets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un prêt par son ID
    public static function getById($id_prets) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Prets WHERE id_prets = ?");
        $stmt->execute([$id_prets]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau prêt
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Prets (id_types_pret, id_clients, montant_prets, date_debut, duree_en_mois) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->id_types_pret,
            $data->id_clients,
            $data->montant_prets,
            $data->date_debut,
            $data->duree_en_mois
        ]);
        return $db->lastInsertId();
    }

    // Mettre à jour un prêt
    public static function update($id_prets, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Prets SET id_types_pret = ?, id_clients = ?, montant_prets = ?, date_debut = ?, duree_en_mois = ? WHERE id_prets = ?");
        $stmt->execute([
            $data->id_types_pret,
            $data->id_clients,
            $data->montant_prets,
            $data->date_debut,
            $data->duree_en_mois,
            $id_prets
        ]);
    }

    // Supprimer un prêt
    public static function delete($id_prets) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Prets WHERE id_prets = ?");
        $stmt->execute([$id_prets]);
    }
}