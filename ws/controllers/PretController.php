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
    public static function ajouterPret($returnJson = true) {
        $data = Flight::request()->data;
    
        $fonds = Fond::getFondActuelJusque($data->date_debut);
        $fondsDisponible = $fonds['fond_actuel'];
        error_log("Fonds disponibles: " . $fondsDisponible . ", Montant prêt demandé: " . $data->montant_prets);
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
    
        if ($returnJson) {
            Flight::json(['success' => true, 'id_pret' => $idPret]);
        } else {
            return ['success' => true, 'id_pret' => $idPret];
        }
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

    public static function importCSV() {
        if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
            Flight::halt(400, "Fichier invalide");
        }
    
        $fichier = $_FILES['fichier']['tmp_name'];
        $handle = fopen($fichier, 'r');
        if (!$handle) {
            Flight::halt(500, "Erreur lors de la lecture du fichier");
        }
    
        $db = getDB();
    
        $ligne = 0;
        $success = 0;
        $erreurs = 0;
        $entetes = [];
    
        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $ligne++;
    
            // Lire l'en-tête
            if ($ligne === 1) {
                $entetes = $row;
                continue;
            }
    
            // Associer en-têtes et valeurs
            $dataAssoc = array_combine($entetes, $row);
            if (!$dataAssoc) {
                $erreurs++;
                continue;
            }
    
            // Convertir nom_type_pret en id_types_pret si nécessaire
            if (isset($dataAssoc['nom_type_pret']) && !isset($dataAssoc['id_types_pret'])) {
                $stmt = $db->prepare("SELECT id_types_pret FROM Types_pret WHERE LOWER(nom_type_pret) = LOWER(?)");
                $stmt->execute([$dataAssoc['nom_type_pret']]);
                $idType = $stmt->fetchColumn();
    
                if (!$idType) {
                    $erreurs++;
                    continue; // Type non trouvé
                }
    
                $dataAssoc['id_types_pret'] = $idType;
            }
    
            // Vérification de l'existence des champs nécessaires
            $champsRequis = ['id_clients', 'id_types_pret', 'montant_prets', 'duree_en_mois', 'date_debut'];
            foreach ($champsRequis as $champ) {
                if (!isset($dataAssoc[$champ]) || $dataAssoc[$champ] === '') {
                    $erreurs++;
                    continue 2;
                }
            }
    
            // Champs optionnels
            $dataAssoc['assurance'] = $dataAssoc['assurance'] ?? 0;
            $dataAssoc['delai_grace'] = $dataAssoc['delai_grace'] ?? 0;

            Flight::request()->data = new \flight\util\Collection($dataAssoc);

    
            try {
                self::ajouterPret(); // Réutilisation directe
                $success++;
            } catch (Exception $e) {
                $erreurs++;
                // Optionnel : logger $e->getMessage()
            }
        }
    
        fclose($handle);
    
        Flight::json(['success' => $success, 'erreurs' => $erreurs]);
    }
    

}
