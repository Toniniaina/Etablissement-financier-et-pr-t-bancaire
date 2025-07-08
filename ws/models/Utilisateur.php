<?php

require_once __DIR__ . '/../db.php';

class Utilisateur
{
    public static function getAll()
    {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM banque_utilisateurs");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id_utilisateurs)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM banque_utilisateurs WHERE id_utilisateurs = ?");
        $stmt->execute([$id_utilisateurs]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getByEmail($email)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM banque_utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO banque_utilisateurs (nom_utilisateurs, prenom_utilisateurs, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data->nom_utilisateurs,
            $data->prenom_utilisateurs,
            $data->email,
            $data->mot_de_passe 
        ]);
        return $db->lastInsertId();
    }

    public static function update($id_utilisateurs, $data)
    {
        $db = getDB();
        $stmt = $db->prepare("UPDATE banque_utilisateurs SET nom_utilisateurs = ?, prenom_utilisateurs = ?, email = ?, mot_de_passe = ? WHERE id_utilisateurs = ?");
        $stmt->execute([
            $data->nom_utilisateurs,
            $data->prenom_utilisateurs,
            $data->email,
            $data->mot_de_passe, 
            $id_utilisateurs
        ]);
    }

    public static function delete($id_utilisateurs)
    {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM banque_utilisateurs WHERE id_utilisateurs = ?");
        $stmt->execute([$id_utilisateurs]);
    }
}