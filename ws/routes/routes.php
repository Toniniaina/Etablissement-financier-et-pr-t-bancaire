<?php
require_once __DIR__ . '/../controllers/EtudiantController.php';
require_once __DIR__ . '/../controllers/FondsController.php';

Flight::route('GET /etudiants', ['EtudiantController', 'getAll']);
Flight::route('GET /etudiants/@id', ['EtudiantController', 'getById']);
Flight::route('POST /etudiants', ['EtudiantController', 'create']);
Flight::route('PUT /etudiants/@id', ['EtudiantController', 'update']);
Flight::route('DELETE /etudiants/@id', ['EtudiantController', 'delete']);

Flight::route('GET /fonds', ['FondsController', 'getAll']);
Flight::route('GET /fonds/@id', ['FondsController', 'getById']);
Flight::route('POST /fonds', ['FondsController', 'create']);
Flight::route('PUT /fonds/@id', ['FondsController', 'update']);
Flight::route('DELETE /fonds/@id', ['FondsController', 'delete']);
