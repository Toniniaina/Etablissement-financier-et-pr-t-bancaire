<?php
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../helpers/Utils.php';



class ClientController {
    public static function getAll() {
        $etudiants = Client::getAll();
        Flight::json($etudiants);
    }

    public static function getById($id) {
        $etudiant = Client::getById($id);
        Flight::json($etudiant);
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Client::create($data);
        $dateFormatted = Utils::formatDate('2025-01-01');
        Flight::json(['message' => 'Étudiant ajouté', 'id' => $id]);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Client::update($id, $data);
        Flight::json(['message' => 'Étudiant modifié']);
    }

    public static function delete($id) {
        Client::delete($id);
        Flight::json(['message' => 'Étudiant supprimé']);
    }
}
