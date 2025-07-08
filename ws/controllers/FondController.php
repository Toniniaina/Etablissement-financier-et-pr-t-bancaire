<?php

require_once __DIR__ . '/../helpers/Utils.php';
require_once __DIR__ . '/../models/Fond.php';
require_once __DIR__ . '/../models/DetailFond.php';



class FondController {
    // Récupérer tous les fonds
    public static function getAll() {
        $fonds = Fond::getAll();
        Flight::json($fonds);
    }

    public static function getById($id) {
        $fond = Fond::getById($id);
        Flight::json($fond);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Fond::create($data);
    
        $date_details = (!empty($data->date_details)) ? $data->date_details : Utils::formatDate(date('Y-m-d'));
    
        $detailsData = (object)[
            'id_fonds' => $id,
            'id_type_transactions' => $data->id_type_transactions ?? 1,
            'date_details' => $date_details,
            'id_prets' => $data->id_prets ?? null
        ];
        DetailFond::create($detailsData);
    
        Flight::json(['message' => 'Fonds ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Fond::update($id, $data);
        Flight::json(['message' => 'Fonds modifié']);
    }

    public static function delete($id) {
        Fond::delete($id);
        Flight::json(['message' => 'Fonds supprimé']);
    }

    public static function fondsParMois()
    {
        $moisDebut = $_POST['mois_debut'] ?? '';
        $moisFin = $_POST['mois_fin'] ?? '';

        try {
            if (!$moisDebut || !$moisFin) {
                // Pas de filtre -> tous les mois
                $result = Fond::getFondsParMois(null, null);
                Flight::json($result);
            } elseif ($moisDebut === $moisFin) {
                // Un seul mois
                $result = Fond::getFondsPourUnMois($moisDebut);
                Flight::json([$result]);
            }
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }
}