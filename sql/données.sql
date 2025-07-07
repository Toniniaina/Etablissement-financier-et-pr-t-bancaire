
INSERT INTO Status_prets (nom_status) VALUES
                                          ('En attente'),
                                          ('Approuvé'),
                                          ('Rejeté'),
                                          ('En cours de remboursement'),
                                          ('Remboursé');

INSERT INTO Type_transactions (nom_type_transactions) VALUES
                                                          ('Depot'),
                                                          ('Retrait');


INSERT INTO Types_pret (nom_type_pret) VALUES
                                          ('Prêt personnel'),
                                          ('Prêt immobilier'),
                                          ('Prêt auto'),
                                          ('Prêt étudiant');

INSERT INTO Taux (id_types_pret, pourcentage) VALUES
(1, 5.50),   -- Prêt personnel
(2, 3.75),   -- Prêt immobilier
(3, 7.00),   -- Prêt étudiant
(4, 4.20);   -- Prêt auto


INSERT INTO Clients (nom_clients, prenom_clients, date_naissance, salaire) VALUES
('ANDRIAMAMPIANINA', 'Hery', '1980-09-12', 1300000.00),
('RAZAFINDRAKOTO', 'Nirina', '1992-01-30', 1000000.00),
('RAKOTOBE', 'Tiana', '1998-06-25', 800000.00);


