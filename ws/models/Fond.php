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
            "SELECT COALESCE(SUM(f.montant_fonds), 0) as depot
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = ? AND d.date_details <= ?"
        );
        $stmt->execute(['Depot', $dateLimite]); 
        $depot = $stmt->fetch(PDO::FETCH_ASSOC)['depot'];
    
        // Total des retraits jusqu'à une date donnée
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(f.montant_fonds), 0) as retrait
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = ? AND d.date_details <= ?"
        );
        $stmt->execute(['retrait', $dateLimite]);
        $retrait = $stmt->fetch(PDO::FETCH_ASSOC)['retrait'];
    
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
        public static function getFondsPourUnMois($mois)
        {
            $db = getDB();
        
            // Sécurité : format attendu 'YYYY-MM'
            if (!preg_match('/^\d{4}-\d{2}$/', $mois)) {
                throw new Exception("Format du mois invalide");
            }
        
            $finMois = new DateTime($mois . '-01');
            $finMoisStr = $finMois->format('Y-m-t'); // Ex: 2025-07-31
        
            // Dépôts cumulés jusqu'à fin du mois
            $stmt = $db->prepare(
                "SELECT COALESCE(SUM(f.montant_fonds), 0)
                 FROM Details_fonds d
                 JOIN Fonds f ON d.id_fonds = f.id_fonds
                 JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
                 WHERE tt.nom_type_transactions = 'Depot' AND d.date_details <= ?"
            );
            $stmt->execute([$finMoisStr]);
            $depot = (float)$stmt->fetchColumn();
        
            // Retraits cumulés jusqu'à fin du mois
            $stmt = $db->prepare(
                "SELECT COALESCE(SUM(f.montant_fonds), 0)
                 FROM Details_fonds d
                 JOIN Fonds f ON d.id_fonds = f.id_fonds
                 JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
                 WHERE tt.nom_type_transactions = 'Retrait' AND d.date_details <= ?"
            );
            $stmt->execute([$finMoisStr]);
            $retrait = (float)$stmt->fetchColumn();
        
            // Remboursements du mois
            $stmt = $db->prepare(
                "SELECT COALESCE(SUM(montant_remboursement), 0)
                 FROM Remboursements
                 WHERE DATE_FORMAT(date_remboursement, '%Y-%m') = ?"
            );
            $stmt->execute([$mois]);
            $remboursement = (float)$stmt->fetchColumn();
        
            $fondsActuels = $depot - $retrait;
        
            return [
                'mois' => $mois,
                'fonds_actuels' => $fondsActuels,
                'remboursement_clients' => $remboursement,
                'total_disponible' => $fondsActuels + $remboursement
            ];
        }
        public static function getFondsParMois($moisDebut = null, $moisFin = null)
{
    $db = getDB();

    // Si aucun filtre : on prend les dates extrêmes dans Details_fonds
    if (!$moisDebut || !$moisFin) {
        $stmt = $db->query("SELECT MIN(DATE_FORMAT(date_details, '%Y-%m')) as debut, MAX(DATE_FORMAT(date_details, '%Y-%m')) as fin FROM Details_fonds");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $moisDebut = $row['debut'] ?? date('Y-m');
        $moisFin = $row['fin'] ?? date('Y-m');
    }

    // Création des dates
    $dateDebut = new DateTime($moisDebut . '-01');
    $dateFin = new DateTime($moisFin . '-01');
    $dateFin->modify('first day of next month'); // Inclus le dernier mois

    $resultats = [];

    while ($dateDebut < $dateFin) {
        $moisStr = $dateDebut->format('Y-m');
        $finMoisStr = $dateDebut->format('Y-m-t'); // Fin du mois

        // Dépôt cumulé jusqu'à fin de ce mois
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(f.montant_fonds), 0) 
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = 'Depot' AND d.date_details <= ?"
        );
        $stmt->execute([$finMoisStr]);
        $depot = (float)$stmt->fetchColumn();

        // Retrait cumulé jusqu'à fin de ce mois
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(f.montant_fonds), 0)
             FROM Details_fonds d
             JOIN Fonds f ON d.id_fonds = f.id_fonds
             JOIN Type_transactions tt ON d.id_type_transactions = tt.id_type_transactions
             WHERE tt.nom_type_transactions = 'Retrait' AND d.date_details <= ?"
        );
        $stmt->execute([$finMoisStr]);
        $retrait = (float)$stmt->fetchColumn();

        // Remboursements du mois en question
        $stmt = $db->prepare(
            "SELECT COALESCE(SUM(montant_remboursement), 0) 
             FROM Remboursements
             WHERE DATE_FORMAT(date_remboursement, '%Y-%m') = ?"
        );
        $stmt->execute([$moisStr]);
        $remboursement = (float)$stmt->fetchColumn();

        $fondActuel = $depot - $retrait;

        $resultats[] = [
            'mois' => $moisStr,
            'depot' => $depot,
            'retrait' => $retrait,
            'fonds_actuels' => $fondActuel,
            'remboursement_clients' => $remboursement,
            'total_disponible' => $fondActuel + $remboursement
        ];

        $dateDebut->modify('+1 month');
    }

    return $resultats;
}

        

    
}