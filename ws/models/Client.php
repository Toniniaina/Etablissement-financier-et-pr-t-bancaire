<?php
require_once __DIR__ . '/../db.php';

class Client {
    // Récupérer tous les clients
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Clients");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un client par son ID
    public static function getById($id_clients) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Clients WHERE id_clients = ?");
        $stmt->execute([$id_clients]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau client
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Clients (nom_clients, prenom_clients, date_naissance, salaire) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data->nom_clients,
            $data->prenom_clients,
            $data->date_naissance,
            $data->salaire
        ]);
        return $db->lastInsertId();
    }

    // Mettre à jour un client
    public static function update($id_clients, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Clients SET nom_clients = ?, prenom_clients = ?, date_naissance = ?, salaire = ? WHERE id_clients = ?");
        $stmt->execute([
            $data->nom_clients,
            $data->prenom_clients,
            $data->date_naissance,
            $data->salaire,
            $id_clients
        ]);
    }

    // Supprimer un client
    public static function delete($id_clients) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Clients WHERE id_clients = ?");
        $stmt->execute([$id_clients]);
    }
}