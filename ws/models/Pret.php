<?php

require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/StatusPret.php';

class Pret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Prets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function getById($id_prets) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Prets WHERE id_prets = ?");
        $stmt->execute([$id_prets]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Prets 
            (id_types_pret, id_clients, montant_prets, date_debut, duree_en_mois, assurance, delai_grace)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $data->id_types_pret,
            $data->id_clients,
            $data->montant_prets,
            $data->date_debut,
            $data->duree_en_mois,
            isset($data->assurance) ? $data->assurance : 0.00,
            isset($data->delai_grace) ? $data->delai_grace : 0
        ]);
        return $db->lastInsertId();
    }

    // Mettre à jour un prêt
    public static function update($id_prets, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Prets SET 
            id_types_pret = ?, id_clients = ?, montant_prets = ?, date_debut = ?, duree_en_mois = ?, assurance = ?, delai_grace = ?
            WHERE id_prets = ?");
        $stmt->execute([
            $data->id_types_pret,
            $data->id_clients,
            $data->montant_prets,
            $data->date_debut,
            $data->duree_en_mois,
            isset($data->assurance) ? $data->assurance : 0.00,
            isset($data->delai_grace) ? $data->delai_grace : 0,
            $id_prets
        ]);
    }

    // Supprimer un prêt
    public static function delete($id_prets) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Prets WHERE id_prets = ?");
        $stmt->execute([$id_prets]);
    }

    public static function getAllPretsAvecTaux()
    {
        $db = getDB();
        $stmt = $db->query("SELECT p.*, t.pourcentage FROM Prets p JOIN Taux t ON p.id_types_pret = t.id_types_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getLastMvtPret($idPret)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT * FROM Mouvement_prets
            WHERE id_prets = ?
            ORDER BY date_mouvement DESC
            LIMIT 1
        ");
        $stmt->execute([$idPret]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    
    public static function getDernierStatutPret($idPret)
    {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT id_status_prets FROM Mouvement_prets
            WHERE id_prets = ?
            ORDER BY date_mouvement DESC
            LIMIT 1
        ");
        $stmt->execute([$idPret]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? StatusPret::getById($row['id_status_prets']) : null;
    }

    public static function getDateApprobation($idPret) {
        $db = getDB();
        $stmt = $db->prepare("
            SELECT date_mouvement 
            FROM Mouvement_prets mp
            JOIN Status_prets s ON mp.id_status_prets = s.id_status_prets
            WHERE mp.id_prets = ? AND s.nom_status = 'Approuve'
            ORDER BY date_mouvement ASC
            LIMIT 1
        ");
        $stmt->execute([$idPret]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['date_mouvement'] : null;
    }

    public static function getInteretsParMois($moisDebut, $moisFin)
    {
        $tousPrets = self::getAllPretsAvecTaux();
        $resultat = [];

        foreach ($tousPrets as $pret) {
            $statut = self::getDernierStatutPret($pret['id_prets']);
            if (!$statut || !in_array($statut['nom_status'], ['approuve', 'En cours de remboursement'])) continue;
            $delaiGrace = isset($pret['delai_grace']) ? (int)$pret['delai_grace'] : 0;
            $dateDebut = $pret['date_debut'];
            if ($delaiGrace > 0) {
                $dateApprobation = self::getDateApprobation($pret['id_prets']);
                if ($dateApprobation) {
                    $dateDebut = $dateApprobation;
                }
            }

            $interets = self::calculerInteretsMensuels(
                $pret['montant_prets'],
                $pret['pourcentage'],
                $pret['duree_en_mois'],
                $dateDebut,
                $delaiGrace
            );

            foreach ($interets as $mois => $interet) {
                if ($mois >= $moisDebut && $mois <= $moisFin) {
                    if (!isset($resultat[$mois])) $resultat[$mois] = 0;
                    $resultat[$mois] += $interet;
                }
            }
        }

        ksort($resultat);
        $resultatFinal = [];
        foreach ($resultat as $mois => $total) {
            $resultatFinal[] = ['mois_annee' => $mois, 'interets_totaux' => round($total, 2)];
        }
        return $resultatFinal;
    }

    public static function calculerInteretsMensuels($capital, $tauxAnnuel, $dureeMois, $dateDebut, $delaiGrace = 0) {
        $mensualite = self::calculerMensualite($capital, $tauxAnnuel, $dureeMois);
        $interetsParMois = [];
        $reste = $capital;
        $tauxMensuel = $tauxAnnuel / 12 / 100;
        $mois = new DateTime($dateDebut);

        for ($i = 0; $i < $dureeMois; $i++) {
            if ($i < $delaiGrace) {
                $interetsParMois[$mois->format('Y-m')] = 0.00;
            } else {
                $interet = $reste * $tauxMensuel;
                $amortissement = $mensualite - $interet;
                $reste -= $amortissement;
                $interetsParMois[$mois->format('Y-m')] = round($interet, 2);
            }
            $mois->modify('+1 month');
        }
        return $interetsParMois;
    }

    public static function calculerMensualite($capital, $tauxAnnuel, $dureeMois)
    {
        $t = $tauxAnnuel / 12 / 100;
        return $capital * $t / (1 - pow(1 + $t, -$dureeMois));
    }

    public static function rechercheAvecFiltre($data) {
        $db = getDB();
        $conditions = [];
        $params = [];
    
        if (!empty($data->nom)) {
            $conditions[] = "LOWER(c.nom_clients) LIKE ?";
            $params[] = "%" . strtolower($data->nom) . "%";
        }
    
        if (!empty($data->prenom)) {
            $conditions[] = "LOWER(c.prenom_clients) LIKE ?";
            $params[] = "%" . strtolower($data->prenom) . "%";
        }
    
        if (!empty($data->id_types_pret)) {
            $conditions[] = "p.id_types_pret = ?";
            $params[] = $data->id_types_pret;
        }
    
        $where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";
    
        $sql = "
            SELECT
                p.*,
                c.nom_clients,
                c.prenom_clients,
                t.nom_type_pret,
                s.nom_status AS statut_actuel
            FROM Prets p
            JOIN Clients c ON p.id_clients = c.id_clients
            JOIN Types_pret t ON p.id_types_pret = t.id_types_pret
            LEFT JOIN Mouvement_prets m ON m.id_prets = p.id_prets
            LEFT JOIN Status_prets s ON s.id_status_prets = m.id_status_prets
            INNER JOIN (
                SELECT id_prets, MAX(date_mouvement) AS max_date
                FROM Mouvement_prets
                GROUP BY id_prets
            ) m2 ON m.id_prets = m2.id_prets AND m.date_mouvement = m2.max_date
            $where
            ORDER BY p.date_debut DESC
        ";
    
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getEcheancier($idPret)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT p.*, t.pourcentage FROM Prets p JOIN Taux t ON p.id_types_pret = t.id_types_pret WHERE p.id_prets = ?");
        $stmt->execute([$idPret]);
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pret) return [];

        $capital = $pret['montant_prets'];
        $tauxAnnuel = $pret['pourcentage'];
        $dureeMois = $pret['duree_en_mois'];
        $delaiGrace = $pret['delai_grace'];
        $dateDebut = $pret['date_debut'];
        $assurance = $pret['assurance'];
        $nbMensualites = max(1, $dureeMois - $delaiGrace);

        $assuranceParmois = self::calculerAssurance($assurance, $capital, $nbMensualites);


        // Cas limite : si delai >= durée -> 1 remboursement global
        $nbMensualites = max(1, $dureeMois - $delaiGrace);

        $mensualites = self::calculerMensualite($capital, $tauxAnnuel, $nbMensualites);
        $mensualite = $mensualites + $assuranceParmois;
        $tauxMensuel = $tauxAnnuel / 12 / 100;
        $reste = $capital;

        $mois = new DateTime($dateDebut);
        $echeancier = [];

        // Décaler la date de début après le délai de grâce
        $mois->modify("+$delaiGrace month");

        for ($i = 0; $i < $nbMensualites; $i++) {
            $interet = round($reste * $tauxMensuel, 2);
            $principal = round($mensualite - $interet - $assuranceParmois, 2);
            $reste = round($reste - $principal, 2);
            $echeancier[] = [
                'mois' => $mois->format('Y-m'),
                'mensualite' => round($mensualite, 2),
                'interet' => $interet,
                'principal' => $principal,
                'reste' => max($reste, 0),
                'assurance' => $assuranceParmois
            ];
            $mois->modify('+1 month');
        }

        return $echeancier;
    }

    public static function getDateDebutReelle($idPret) {
        $db = getDB();
        $stmt = $db->prepare("
        SELECT mp.date_mouvement
        FROM Mouvement_prets mp
        JOIN Status_prets sp ON mp.id_status_prets = sp.id_status_prets
        WHERE mp.id_prets = ? AND sp.nom_status = 'Approuve'
        ORDER BY mp.date_mouvement ASC
        LIMIT 1
    ");
        $stmt->execute([$idPret]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['date_mouvement'] : null;
    }
    public static function calculerAssurance($assurance, $capital, $nbMoisRemboursement) {
        $valeur = $assurance * $capital / 100;
        $nbMois = max(1, $nbMoisRemboursement); // éviter division par 0
        return $valeur / $nbMois;
    }



}