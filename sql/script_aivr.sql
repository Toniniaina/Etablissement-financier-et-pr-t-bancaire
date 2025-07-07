DROP DATABASE IF EXISTS banque;

CREATE DATABASE banque;
USE banque;

CREATE TABLE Fonds(
    id_fonds INT AUTO_INCREMENT PRIMARY KEY,
    montant_fonds DECIMAL(20, 2) NOT NULL
);

CREATE TABLE Types_pret(
    id_types_pret INT AUTO_INCREMENT PRIMARY KEY,
    nom_type_pret VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Taux(
    id_taux INT AUTO_INCREMENT PRIMARY KEY,
    id_types_pret INT NOT NULL,
    pourcentage DECIMAL(5, 2) NOT NULL,
    FOREIGN KEY (id_types_pret) REFERENCES Types_pret(id_types_pret)
);

CREATE TABLE Clients(
    id_clients INT AUTO_INCREMENT PRIMARY KEY,
    nom_clients VARCHAR(50) NOT NULL,
    prenom_clients VARCHAR(50) NOT NULL,
    date_naissance DATE,
    salaire DECIMAL(10, 2)
);

CREATE TABLE Prets(
    id_prets INT AUTO_INCREMENT PRIMARY KEY,
    id_types_pret INT NOT NULL,
    id_clients INT NOT NULL,
    montant_prets DECIMAL(15, 2) NOT NULL,
    date_debut DATE NOT NULL,
    duree_en_mois INT NOT NULL,
    FOREIGN KEY (id_types_pret) REFERENCES Types_pret(id_types_pret),
    FOREIGN KEY (id_clients) REFERENCES Clients(id_clients)
);

CREATE TABLE Status_prets(
    id_status_prets INT AUTO_INCREMENT PRIMARY KEY,
    nom_status VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Mouvement_prets(
    id_mouvement_prets INT AUTO_INCREMENT PRIMARY KEY,
    id_prets INT NOT NULL,
    id_status_prets INT NOT NULL,
    date_mouvement DATE NOT NULL,
    FOREIGN KEY (id_prets) REFERENCES Prets(id_prets),
    FOREIGN KEY (id_status_prets) REFERENCES Status_prets(id_status_prets)
);


CREATE TABLE Type_transactions(
    id_type_transactions INT AUTO_INCREMENT PRIMARY KEY,
    nom_type_transactions VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE Details_fonds(
    id_details_fonds INT AUTO_INCREMENT PRIMARY KEY,
    id_fonds INT NOT NULL,
    id_type_transactions INT NOT NULL,
    id_prets INT NULL,
    date_details DATE NOT NULL,
    FOREIGN KEY (id_fonds) REFERENCES Fonds(id_fonds),
    FOREIGN KEY (id_prets) REFERENCES Prets(id_prets)
);