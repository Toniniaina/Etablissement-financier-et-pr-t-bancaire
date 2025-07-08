<?php

require_once __DIR__ . '/../db.php';


class Remboursement {
    // Récupérer tous les fonds
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Remboursements");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}