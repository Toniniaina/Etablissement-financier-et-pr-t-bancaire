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
}
