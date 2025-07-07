<?php
require_once __DIR__ . '/../db.php';

class TypeTransaction {
    // Récupérer tous les types de transactions
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Type_transactions");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un type de transaction par son ID
    public static function getById($id_type_transactions) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Type_transactions WHERE id_type_transactions = ?");
        $stmt->execute([$id_type_transactions]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Créer un nouveau type de transaction
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Type_transactions (nom_type_transactions) VALUES (?)");
        $stmt->execute([$data->nom_type_transactions]);
        return $db->lastInsertId();
    }

    // Mettre à jour un type de transaction
    public static function update($id_type_transactions, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Type_transactions SET nom_type_transactions = ? WHERE id_type_transactions = ?");
        $stmt->execute([$data->nom_type_transactions, $id_type_transactions]);
    }

    // Supprimer un type de transaction
    public static function delete($id_type_transactions) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Type_transactions WHERE id_type_transactions = ?");
        $stmt->execute([$id_type_transactions]);
    }
}