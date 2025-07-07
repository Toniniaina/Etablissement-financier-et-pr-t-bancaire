<?php

require_once __DIR__ . '/../models/DetailFond.php';

class DetailFondController
{
    // Récupérer tous les détails de fonds
    public static function getAll()
    {
        $details = DetailFond::getAll();
        Flight::json($details);
    }

    // Récupérer un détail de fonds par ID
    public static function getById($id)
    {
        $detail = DetailFond::getById($id);
        if ($detail) {
            Flight::json($detail);
        } else {
            Flight::json(['error' => 'Détail non trouvé'], 404);
        }
    }

    // Créer un nouveau détail de fonds
    public static function create()
    {
        try {
            $data = Flight::request()->data;
            $id = DetailFond::create($data);
            Flight::json(['message' => 'Détail de fonds ajouté', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // Modifier un détail de fonds
    public static function update($id)
    {
        try {
            $data = Flight::request()->data;
            DetailFond::update($id, $data);
            Flight::json(['message' => 'Détail de fonds modifié']);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // Supprimer un détail de fonds
    public static function delete($id)
    {
        try {
            DetailFond::delete($id);
            Flight::json(['message' => 'Détail de fonds supprimé']);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getFondActuel(){
        $fonds = DetailFond::getFondActuelDetails();
        Flight::json($fonds);
    }
}