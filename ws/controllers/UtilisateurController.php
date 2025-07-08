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

    public static function register()
    {
        $nom = $_POST['nom_utilisateurs'] ?? '';
        $prenom = $_POST['prenom_utilisateurs'] ?? '';
        $email = $_POST['email'] ?? '';
        $mot_de_passe = $_POST['mot_de_passe'] ?? '';

        if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe)) {
            Flight::json(['success' => false, 'message' => 'Tous les champs sont obligatoires.']);
            return;
        }

        // Vérifier l'unicité de l'email
        if (Utilisateur::getByEmail($email)) {
            Flight::json(['success' => false, 'message' => "Cet email est déjà utilisé."]);
            return;
        }

        $data = (object)[
            "nom_utilisateurs" => $nom,
            "prenom_utilisateurs" => $prenom,
            "email" => $email,
            "mot_de_passe" => $mot_de_passe // mot de passe en clair pour la consigne, à hasher en prod
        ];

        $id = Utilisateur::create($data);

        if ($id) {
            Flight::json(['success' => true]);
        } else {
            Flight::json(['success' => false, 'message' => "Erreur lors de l'inscription."]);
        }
    }
}