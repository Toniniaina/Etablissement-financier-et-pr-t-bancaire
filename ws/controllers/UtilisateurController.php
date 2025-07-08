<?php

require_once __DIR__ . '/../models/Utilisateur.php';

class UtilisateurController
{
    public static function login()
    {
        $email = $_POST['email'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        if (empty($email) || empty($mot_de_passe)) {
            echo json_encode(['success' => false, 'message' => 'Champs requis manquants.']);
            return;
        }

        $utilisateur = Utilisateur::getByEmail($email);

        if ($utilisateur && $utilisateur['mot_de_passe'] === $mot_de_passe) { // mot de passe non hashé
            // Tu peux ici démarrer une session ou retourner des infos utilisateur
            echo json_encode(['success' => true, 'id_utilisateurs' => $utilisateur['id_utilisateurs']]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect !']);
        }
    }
}