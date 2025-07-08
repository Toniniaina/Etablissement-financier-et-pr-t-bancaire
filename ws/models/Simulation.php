<?php

require_once __DIR__ . '/../db.php';

class Simulation
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM simulations ORDER BY id_simulation DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM simulations WHERE id_simulation = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("
            INSERT INTO simulations (
                montant, duree_mois, taux_annuel, interet_total, montant_total, mensualite, id_types_pret
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data->montant,
            $data->duree,
            $data->taux_annuel,
            $data->interet_total,
            $data->montant_total,
            $data->mensualite,
            $data->id_types_pret
        ]);

        return $db->lastInsertId();
    }

    public static function delete($id)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM simulations WHERE id_simulation = ?");
        $stmt->execute([$id]);
    }
}
