<?php

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

    
    public static function getFondActuelJusque($dateLimite) {
        $db = getDB();
    
        // Total des dépôts jusqu'à une date donnée
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(f.montant_fonds), 0) as Depot
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = 'Depot' AND d.date_details <= ?"
        );
        $stmt->execute([$dateLimite]); 
        $depot = $stmt->fetch(PDO::FETCH_ASSOC)['Depot'];
    
        // Total des retraits jusqu'à une date donnée
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(f.montant_fonds), 0) as Retrait
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = 'Retrait' AND d.date_details <= ?"
        );
        $stmt->execute([$dateLimite]);
        $retrait = $stmt->fetch(PDO::FETCH_ASSOC)['Retrait'];
    
        return [
            'total_depot' => $depot,
            'total_retrait' => $retrait,
            'fond_actuel' => $depot - $retrait
        ];
    }

        public static function ajouterAvecDetails($montant, $date, $id_pret = null) {
            $db = getDB();
    
            // 1. Insérer dans Fonds
            $stmtFonds = $db->prepare("INSERT INTO Fonds (montant_fonds) VALUES (?)");
            $stmtFonds->execute([$montant]);
            $idFonds = $db->lastInsertId();
    
            // 2. Récupérer id_type_transactions pour "Prélèvement prêt"
            $stmtType = $db->prepare("SELECT id_type_transactions FROM Type_transactions WHERE LOWER(nom_type_transactions) = LOWER(?)");
            $stmtType->execute(['Retrait']);
            $idTypeTransaction = $stmtType->fetchColumn();
    
            if (!$idTypeTransaction) {
                Flight::halt(500, "Type de transaction 'Retrait' introuvable");
            }
    
            // 3. Insérer dans Details_fonds
            $stmtDetails = $db->prepare("
                INSERT INTO Details_fonds (id_fonds, id_type_transactions, date_details, id_prets)
                VALUES (?, ?, ?, ?)
            ");
            $stmtDetails->execute([
                $idFonds,
                $idTypeTransaction,
                $date,
                $id_pret
            ]);
    
            return $idFonds;
        }

    
}