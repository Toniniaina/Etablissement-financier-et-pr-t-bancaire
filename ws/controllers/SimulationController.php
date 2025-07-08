<?php

require_once __DIR__ . '/../models/Simulation.php';

class SimulationController
{
    public static function getAll()
    {
        $simulations = Simulation::getAll();
        Flight::json($simulations);
    }

    public static function getById($id)
    {
        $simulation = Simulation::getById($id);

        if ($simulation) {
            Flight::json($simulation);
        } else {
            Flight::halt(404, 'Simulation non trouvée.');
        }
    }

    public static function create()
    {
        $data = Flight::request()->data;

        // Vérification des champs obligatoires
        if (
            !isset($data->montant) ||
            !isset($data->duree) ||
            !isset($data->taux_annuel) ||
            !isset($data->interet_total) ||
            !isset($data->montant_total) ||
            !isset($data->mensualite) ||
            !isset($data->id_types_pret)
        ) {
            Flight::halt(400, 'Champs requis manquants.');
            return;
        }

        $id = Simulation::create($data);
        Flight::json(['success' => true, 'id_simulation' => $id]);
    }

    public static function delete($id)
    {
        $simulation = Simulation::getById($id);
        if (!$simulation) {
            Flight::halt(404, 'Simulation non trouvée.');
        }

        Simulation::delete($id);
        Flight::json(['success' => true, 'message' => 'Simulation supprimée.']);
    }
}