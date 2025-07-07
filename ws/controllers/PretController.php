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

        $fondsDisponible = Fond::getFondActuelJusque($data->date_debut);
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
}
