<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /prets/interets', ['PretController', 'getInteretsParMois']);

Flight::route('GET /etudiants', ['EtudiantController', 'getAll']);
Flight::route('GET /etudiants/@id', ['EtudiantController', 'getById']);
Flight::route('POST /etudiants', ['EtudiantController', 'create']);
Flight::route('PUT /etudiants/@id', ['EtudiantController', 'update']);
Flight::route('DELETE /etudiants/@id', ['EtudiantController', 'delete']);
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('GET /clients', ['ClientController', 'getAll']);
Flight::route('GET /typesprets', ['PretController', 'getAllTypes']);
Flight::route('POST /prets', ['PretController', 'ajouterPret']);
