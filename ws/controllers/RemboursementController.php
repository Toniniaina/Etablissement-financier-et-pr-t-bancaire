<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../models/Fond.php';
require_once __DIR__ . '/../models/Remboursement.php';
require_once __DIR__ . '/../models/MvtPret.php';
require_once __DIR__ . '/../models/DetailFond.php';

require_once __DIR__ . '/../helpers/Utils.php';


class RemboursementController
{
    public static function enregistrer()
{
    try {
        $idPret = $_POST['id_prets'];
        $dateRemb = $_POST['date_remboursement'];
        $nbMois = intval($_POST['nb_mois']);

        if (!$idPret || !$dateRemb || $nbMois <= 0) {
            throw new Exception("Données manquantes ou invalides.");
        }

        Remboursement::insererPaiements($idPret, $nbMois);

        echo json_encode(["message" => "Paiement enregistré avec succès."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
}

}