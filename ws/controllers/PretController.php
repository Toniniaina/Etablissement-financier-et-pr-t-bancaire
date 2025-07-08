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

        $fonds = Fond::getFondActuelJusque($data->date_debut);
        $fondsDisponible = $fonds['fond_actuel'];
        if ($fondsDisponible < $data->montant_prets) {
            Flight::halt(400, "Fond insuffisants");
        }

        $idPret = Pret::create($data);

     
        MvtPret::ajouterMouvement([
            'id_prets' => $idPret,
            'id_status_prets' => 1,
            'date_mouvement' => $data->date_debut
        ]);

        Fond::ajouterAvecDetails(
            $data->montant_prets,
            $data->date_debut,
            $idPret
        );


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
    
 
    
        // --- Vérification du plafond d'emprunt basé sur le salaire (commentée pour l'instant) ---
        /*
        $db = getDB();
        $stmtSalaire = $db->prepare("SELECT salaire FROM Clients WHERE id_clients = ?");
        $stmtSalaire->execute([$data->id_clients]);
        $salaire = $stmtSalaire->fetchColumn();
    
        if ($salaire !== false && $salaire !== null) {
            $plafond = $salaire * 10;
            if ($data->montant_prets > $plafond) {
                Flight::halt(400, "Le montant demandé dépasse 10 fois le salaire mensuel autorisé.");
            }
        } else {
            Flight::halt(400, "Salaire du client introuvable.");
        }
        */
        // -------------------------------------------------------------------------------------------
    
    public static function getEcheancierByPret()
    {
        $id = Flight::request()->query['id'];
        if (!$id) {
            Flight::json(['error' => 'ID requis'], 400);
            return;
        }
        $result = Pret::getEcheancier($id);
        Flight::json($result);
    }
    public static function importerDepuisCSV() {
        if (!isset($_FILES['fichier'])) {
            Flight::halt(400, "Aucun fichier fourni");
        }

        $fichierTmp = $_FILES['fichier']['tmp_name'];
        if (!file_exists($fichierTmp)) {
            Flight::halt(400, "Fichier introuvable");
        }

        $handle = fopen($fichierTmp, 'r');
        if (!$handle) {
            Flight::halt(500, "Erreur lors de la lecture du fichier");
        }

        $db = getDB();
        $db->beginTransaction();

        $ligne = 0;
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $ligne++;

            if ($ligne == 1) continue; // Ignorer l'en-tête

            list($id_clients, $id_types_pret, $montant, $date_debut, $duree, $assurance, $delai_grace) = $data;

            // Vérification du fond disponible
            $fond = Fond::getFondActuelJusque($date_debut);
            if ($fond['fond_actuel'] < $montant) {
                $db->rollBack();
                Flight::halt(400, "Fonds insuffisants pour la ligne $ligne (Client: $id_clients)");
            }

            // Insertion du prêt
            $idPret = Pret::create((object)[
                'id_clients' => $id_clients,
                'id_types_pret' => $id_types_pret,
                'montant_prets' => $montant,
                'date_debut' => $date_debut,
                'duree_en_mois' => $duree,
                'assurance' => $assurance,
                'delai_grace' => $delai_grace
            ]);

            MvtPret::ajouterMouvement([
                'id_prets' => $idPret,
                'id_status_prets' => 1,
                'date_mouvement' => $date_debut
            ]);

            Fond::ajouterAvecDetails($montant, $date_debut, $idPret);
        }

        $db->commit();
        fclose($handle);

        Flight::json(['success' => true, 'message' => 'Import CSV réussi']);
    }


}
