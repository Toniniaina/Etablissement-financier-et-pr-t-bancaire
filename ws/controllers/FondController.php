<?php
require_once __DIR__ . '/../helpers/Utils.php';

use Flight;
use Models\Fond;
use Models\DetailFond;


class FondController {
    // Récupérer tous les fonds
    public static function getAll() {
        $fonds = Fond::getAll();
        Flight::json($fonds);
    }

    // Récupérer un fonds par ID
    public static function getById($id) {
        $fond = Fond::getById($id);
        Flight::json($fond);
    }

    // Créer un nouveau fonds et enregistrer dans Details_fonds
    public static function create() {
        $data = Flight::request()->data;
        $id = Fond::create($data);

        // Insertion automatique dans Details_fonds
        $detailsData = (object)[
            'id_fonds' => $id,
            'id_type_transactions' => $data->id_type_transactions ?? 1, // par défaut 1, à adapter si besoin
            'montant_transaction' => $data->montant_fonds,
            'date_details' => Utils::formatDate(date('Y-m-d')),
            'id_prets' => $data->id_prets ?? null
        ];
        DetailFond::create($detailsData);

        Flight::json(['message' => 'Fonds ajouté', 'id' => $id]);
    }

    // Modifier un fonds
    public static function update($id) {
        $data = Flight::request()->data;
        Fond::update($id, $data);
        Flight::json(['message' => 'Fonds modifié']);
    }

    // Supprimer un fonds
    public static function delete($id) {
        Fond::delete($id);
        Flight::json(['message' => 'Fonds supprimé']);
    }
}