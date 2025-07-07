<?php
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class PretController
{
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
}
