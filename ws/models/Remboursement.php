<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/Pret.php';
require_once __DIR__ . '/MvtPret.php';

class Remboursement
{
    public static function insererPaiements($idPret, $nbMois)
    {
        $db = getDB();
        $echeances = Pret::getEcheancierAvecLimite($idPret, $nbMois);

        if (empty($echeances)) {
            throw new Exception("Aucune échéance calculée.");
        }

        $stmt = $db->prepare("INSERT INTO Remboursements (
            id_prets, numero_mois, date_remboursement, montant_remboursement, interet, principal, assurance, reste_a_payer
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $numeroMois = 1;
        foreach ($echeances as $echeance) {
            $stmt->execute([
                $idPret,
                $numeroMois,
                $echeance['mois'] . "-01",
                $echeance['mensualite'],
                $echeance['interet'],
                $echeance['principal'],
                $echeance['assurance'],
                $echeance['reste']
            ]);
            $numeroMois++;
        }
        $dateApprobationStr = self::getDateDebutReelle($idPret);
        if (!$dateApprobationStr) return [];

        $dateDebut  = new DateTime($dateApprobationStr);
        MvtPret::changerStatutPret($idPret, $dateDebut, "En cours de remboursement");
    }
    public static function getDateDebutReelle($idPret)
    {
        $db = getDB();
        $stmt = $db->prepare("
        SELECT mp.date_mouvement
        FROM Mouvement_prets mp
        JOIN Status_prets sp ON mp.id_status_prets = sp.id_status_prets
        WHERE mp.id_prets = ? AND sp.nom_status = 'Approuve'
        ORDER BY mp.date_mouvement ASC
        LIMIT 1
    ");

    }
}
