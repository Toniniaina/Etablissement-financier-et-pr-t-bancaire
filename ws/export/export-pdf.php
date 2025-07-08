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

// Récupérer la date d’approbation
$dateApprobationStr = Pret::getDateDebutReelle($idPret);
if (!$dateApprobationStr) die("Date d'approbation introuvable.");

$dateApprobation = new DateTime($dateApprobationStr);

// Date de début de remboursement = dateApprobation + delai_grace
$dateDebutRemboursement = clone $dateApprobation;
if ((int)$pret['delai_grace'] > 0) {
    $dateDebutRemboursement->modify('+' . $pret['delai_grace'] . ' months');
}

// ❗ Date de fin estimée = dateApprobation + durée_en_mois (pas dateDebutRemboursement)
$dateFinEstimee = (clone $dateApprobation)->modify('+' . $pret['duree_en_mois'] . ' months');

$pdf = new FPDF();
$pdf->AddPage();

// Titre
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('Échéancier de remboursement du prêt n°' . $idPret), 0, 1, 'C');
$pdf->Ln(5);

// Infos client et prêt
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, utf8_decode("Client : {$client['nom_clients']} {$client['prenom_clients']} (ID: {$client['id_clients']})"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Date d'approbation : " . $dateApprobation->format('d/m/Y')), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Délai de grâce : {$pret['delai_grace']} mois"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Date de début de remboursement : " . $dateDebutRemboursement->format('d/m/Y')), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Durée du prêt : {$pret['duree_en_mois']} mois"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Date de fin estimée : " . $dateFinEstimee->format('d/m/Y')), 0, 1);
$pdf->Ln(5);

// En-têtes du tableau
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 10, utf8_decode('Mois'), 1);
$pdf->Cell(40, 10, utf8_decode('Mensualité'), 1);
$pdf->Cell(30, 10, utf8_decode('Intérêt'), 1);
$pdf->Cell(40, 10, utf8_decode('Principal'), 1);
$pdf->Cell(30, 10, utf8_decode('Assurance'), 1);
$pdf->Cell(40, 10, utf8_decode('Reste à payer'), 1);
$pdf->Ln();

// Lignes échéancier
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
