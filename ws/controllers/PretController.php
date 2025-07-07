<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../models/Fond.php';
require_once __DIR__ . '/../models/MvtPret.php';
require_once __DIR__ . '/../models/DetailFond.php';

require_once __DIR__ . '/../helpers/Utils.php';


class PretController {
    public static function getAll() {
        $prets = Pret::getAll();
        Flight::json($prets);
    }
    public static function getAllTypes() {
        $typePret =TypePret::getAll();
        Flight::json($typePret);
    }
     public static function getInteretsParMois()
    {
        $query = Flight::request()->query;
        $debut = isset($query['debut']) ? $query['debut'] : null; // format 'YYYY-MM'
        $fin = isset($query['fin']) ? $query['fin'] : null;       // format 'YYYY-MM'

        if (!$debut || !$fin) {
            Flight::json(['error' => 'Paramètres debut et fin requis (format YYYY-MM)'], 400);
            return;
        }

        $resultats = Pret::getInteretsParMois($debut, $fin);
        Flight::json($resultats);
    }

    
    public static function ajouterPret() {
        $data = Flight::request()->data;

        // Vérifier les fonds disponibles
        $fondsDisponible = Fond::getFondActuelJusque($data->date_debut);
        if ($fondsDisponible < $data->montant_prets) {
            Flight::halt(400, "Fond insuffisants");
        }

        // Créer le prêt via le modèle
        $idPret = Pret::create($data);

     
        MvtPret::ajouterMouvement([
            'id_prets' => $idPret,
            'id_status_prets' => 1,
            'date_mouvement' => date('Y-m-d')
        ]);

        Fond::ajouterAvecDetails(
            $data->montant_prets,
            $data->date_debut,
            $idPret
        );

        // Mettre à jour le fonds

        Flight::json(['success' => true, 'id_pret' => $idPret]);
    }

    public static function rechercher() {
        $data = Flight::request()->data;
        $resultats = Pret::rechercheAvecFiltre($data);
        Flight::json($resultats);
    }

    public static function approuver() {
        $data = Flight::request()->data;
    
        $idPret = $data->id_prets;
        $dateMouvement = $data->date_mouvement;
    
        $db = getDB();
    
        $stmtDate = $db->prepare("SELECT MAX(date_mouvement) FROM Mouvement_prets WHERE id_prets = ?");
        $stmtDate->execute([$idPret]);
        $dernierDateMouvement = $stmtDate->fetchColumn();
    
        if ($dernierDateMouvement !== false && $dateMouvement < $dernierDateMouvement) {
            Flight::halt(400, "La date d'approbation ne peut pas être antérieure à la dernière date de mouvement existante ($dernierDateMouvement).");
        }
    
        $stmt = $db->prepare("SELECT id_status_prets FROM Status_prets WHERE LOWER(nom_status) = 'approuve'");
        $stmt->execute();
        $idStatusApprouve = $stmt->fetchColumn();
    
        if (!$idStatusApprouve) {
            Flight::halt(400, "Statut 'Approuvé' non trouvé");
        }
    
        $stmt2 = $db->prepare("INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES (?, ?, ?)");
        $stmt2->execute([$idPret, $idStatusApprouve, $dateMouvement]);
    
        Flight::json(['success' => true]);
    }
    public static function rejeter() {
        $data = Flight::request()->data;
    
        $idPret = $data->id_prets;
        $dateMouvement = $data->date_mouvement;
    
        $db = getDB();
    
        $stmtDate = $db->prepare("SELECT MAX(date_mouvement) FROM Mouvement_prets WHERE id_prets = ?");
        $stmtDate->execute([$idPret]);
        $dernierDateMouvement = $stmtDate->fetchColumn();
    
        if ($dernierDateMouvement !== false && $dateMouvement < $dernierDateMouvement) {
            Flight::halt(400, "La date d'approbation ne peut pas être antérieure à la dernière date de mouvement existante ($dernierDateMouvement).");
        }
    
        $stmt = $db->prepare("SELECT id_status_prets FROM Status_prets WHERE LOWER(nom_status) = 'rejete'");
        $stmt->execute();
        $idStatusApprouve = $stmt->fetchColumn();
    
        if (!$idStatusApprouve) {
            Flight::halt(400, "Statut 'rejete' non trouvé");
        }
    
        $stmt2 = $db->prepare("INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES (?, ?, ?)");
        $stmt2->execute([$idPret, $idStatusApprouve, $dateMouvement]);
    
        Flight::json(['success' => true]);
    }
    
    
}
