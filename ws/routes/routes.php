<?php
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /clients', ['ClientController', 'getAll']);
Flight::route('GET /typesprets', ['PretController', 'getAllTypes']);
Flight::route('POST /prets', ['PretController', 'ajouterPret']);
