<?php
require_once __DIR__ . '/../models/Taux.php';
require_once __DIR__ . '/../helpers/Utils.php';


class TauxController {
    public static function getAll() {
        $result = Taux::getWithType();
        Flight::json($result);
    }
}
