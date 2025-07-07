<?php
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/PretController.php';
require_once __DIR__ . '/../controllers/FondController.php';
require_once __DIR__ . '/../controllers/DetailFondController.php';
require_once __DIR__ . '/../controllers/TauxController.php';

Flight::route('GET /prets/interets', ['PretController', 'getInteretsParMois']);



Flight::route('GET /etudiants', ['EtudiantController', 'getAll']);
Flight::route('GET /etudiants/@id', ['EtudiantController', 'getById']);
Flight::route('POST /etudiants', ['EtudiantController', 'create']);
Flight::route('PUT /etudiants/@id', ['EtudiantController', 'update']);
Flight::route('DELETE /etudiants/@id', ['EtudiantController', 'delete']);


Flight::route('GET /clients', ['ClientController', 'getAll']);
Flight::route('GET /typesprets', ['PretController', 'getAllTypes']);
Flight::route('POST /prets', ['PretController', 'ajouterPret']);
Flight::route('POST /prets/search', ['PretController', 'rechercher']);
Flight::route('POST /prets/approuver', ['PretController', 'approuver']);
Flight::route('POST /prets/rejeter', ['PretController', 'rejeter']);


Flight::route('GET /fonds', ['FondController', 'getAll']);
Flight::route('GET /fonds/@id', ['FondController', 'getById']);
Flight::route('POST /fonds', ['FondController', 'create']);
Flight::route('PUT /fonds/@id', ['FondController', 'update']);
Flight::route('DELETE /fonds/@id', ['FondController', 'delete']);
Flight::route('GET /fond_actuel', ['DetailFondController', 'getFondActuel']);

Flight::route('GET /taux', ['TauxController', 'getAll']);


