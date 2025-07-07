<?php

require_once __DIR__ . '/../db.php';

class TypePret {

    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM Types_pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getById($id_types_pret) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM Types_pret WHERE id_types_pret = ?");
        $stmt->execute([$id_types_pret]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Types_pret (nom_type_pret) VALUES (?)");
        $stmt->execute([$data->nom_type_pret]);
        return $db->lastInsertId();
    }


    public static function update($id_types_pret, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE Types_pret SET nom_type_pret = ? WHERE id_types_pret = ?");
        $stmt->execute([$data->nom_type_pret, $id_types_pret]);
    }


    public static function delete($id_types_pret) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM Types_pret WHERE id_types_pret = ?");
        $stmt->execute([$id_types_pret]);
    }
}