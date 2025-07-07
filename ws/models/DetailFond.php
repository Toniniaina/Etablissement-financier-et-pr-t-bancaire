<?php

require_once __DIR__ . '/../db.php';

class DetailFond {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Details_fonds");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id_details_fonds) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Details_fonds WHERE id_details_fonds = ?");
        $stmt->execute([$id_details_fonds]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare(
            "INSERT INTO Details_fonds (id_fonds, id_type_transactions, date_details, id_prets)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data->id_fonds,
            $data->id_type_transactions,
            $data->date_details,
            $data->id_prets // Peut être null
        ]);
        return $db->lastInsertId();
    }

    // Mettre à jour un détail de fonds
    public static function update($id_details_fonds, $data) {
        $db = getDB();
        $stmt = $db->prepare(
            "UPDATE Details_fonds SET id_fonds = ?, id_type_transactions = ?, montant_transaction = ?, date_details = ?, id_prets = ?
             WHERE id_details_fonds = ?"
        );
        $stmt->execute([
            $data->id_fonds,
            $data->id_type_transactions,
            $data->montant_transaction,
            $data->date_details,
            $data->id_prets,
            $id_details_fonds
        ]);
    }

    // Supprimer un détail de fonds
    public static function delete($id_details_fonds) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Details_fonds WHERE id_details_fonds = ?");
        $stmt->execute([$id_details_fonds]);
    }


    public static function getFondActuelDetails() {
        $db = getDB();
    
        // Dépôt : SUM(montant_fonds) pour les transactions dont le nom est 'Depot'
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(f.montant_fonds), 0) as depot
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = ?"
        );
        $stmt->execute(['Depot']); // ou 'Dépot', selon la valeur exacte dans ta base
        $depot = $stmt->fetch(PDO::FETCH_ASSOC)['depot'];
    
        // Retrait : SUM(montant_fonds) pour les transactions dont le nom est 'Retrait'
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(f.montant_fonds), 0) as retrait
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = ?"
        );
        $stmt->execute(['Retrait']);
        $retrait = $stmt->fetch(PDO::FETCH_ASSOC)['retrait'];
    
        return [
            'total_depot' => $depot,
            'total_retrait' => $retrait,
            'fond_actuel' => $depot - $retrait
        ];
    }
}