<?php
require_once __DIR__ . '/../libs/fpdf.php';
require_once __DIR__ . '/../models/Pret.php';
require_once __DIR__ . '/../models/Client.php';

if (!isset($_GET['id'])) die("ID de prêt manquant.");

$idPret = $_GET['id'];
$pret = Pret::getById($idPret);
if (!$pret) die("Prêt introuvable.");

$client = Client::getById($pret['id_clients']);
if (!$client) die("Client introuvable.");

$echeancier = Pret::getEcheancier($idPret);
if (empty($echeancier)) die("Aucun échéancier trouvé.");

// Récupérer la date de début réelle (date d'approbation)
$dateDebutReelleStr = Pret::getDateDebutReelle($idPret);
if (!$dateDebutReelleStr) {
    die("Date de début réelle introuvable.");
}

// Appliquer délai de grâce en mois (ajouter à la date d'approbation)
$dateDebutReelle = new DateTime($dateDebutReelleStr);
if ($pret['delai_grace'] > 0) {
    $dateDebutReelle->modify('+' . $pret['delai_grace'] . ' months');
}

// Calcul de la date de fin (date début réelle + durée prêt)
$dateFin = (clone $dateDebutReelle)->modify('+' . $pret['duree_en_mois'] . ' months');

$pdf = new FPDF();
$pdf->AddPage();

// Titre
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Échéancier de remboursement du prêt n°' . $idPret), 0, 1, 'C');
$pdf->Ln(5);

// Infos client + dates
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, utf8_decode("Client : {$client['nom_clients']} {$client['prenom_clients']} (ID: {$client['id_clients']})"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Date de début réelle du prêt (avec délai de grâce) : " . $dateDebutReelle->format('d/m/Y')), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Date de fin estimée : " . $dateFin->format('d/m/Y')), 0, 1);
$pdf->Ln(5);

// Tableau échéancier - entêtes avec utf8_decode()
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, utf8_decode('Mois'), 1);
$pdf->Cell(40, 10, utf8_decode('Mensualité'), 1);
$pdf->Cell(30, 10, utf8_decode('Intérêt'), 1);
$pdf->Cell(40, 10, utf8_decode('Principal'), 1);
$pdf->Cell(30, 10, utf8_decode('Assurance'), 1);
$pdf->Cell(40, 10, utf8_decode('Reste à payer'), 1);
$pdf->Ln();

// Contenu du tableau (les données numériques n’ont pas besoin de utf8_decode)
$pdf->SetFont('Arial', '', 10);
foreach ($echeancier as $ligne) {
    $pdf->Cell(30, 10, $ligne['mois'], 1);
    $pdf->Cell(40, 10, number_format($ligne['mensualite'], 2, ',', ' '), 1);
    $pdf->Cell(30, 10, number_format($ligne['interet'], 2, ',', ' '), 1);
    $pdf->Cell(40, 10, number_format($ligne['principal'], 2, ',', ' '), 1);
    $pdf->Cell(30, 10, number_format($ligne['assurance'], 2, ',', ' '), 1);
    $pdf->Cell(40, 10, number_format($ligne['reste'], 2, ',', ' '), 1);
    $pdf->Ln();
}

$pdf->Output('I', 'echeancier_pret_' . $idPret . '.pdf');
