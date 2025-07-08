INSERT INTO Types_pret (nom_type_pret) VALUES
('Pret Personnel'),
('Pret Immobilier'),
('Pret Auto'),
('Pret Consommation'),
('Pret Etudiant');

INSERT INTO Taux (id_types_pret, pourcentage) VALUES
(1, 6.00),
(2, 3.48),
(3, 4.56),
(4, 8.04),
(5, 2.52);

INSERT INTO Status_prets (nom_status) VALUES
('En attente'),
('Approuve'),
('Rejete'),
('En cours de remboursement'),
('Rembourse');

INSERT INTO Fonds (id_fonds, montant_fonds) VALUES
(1, 10000000.00),
(2, 5000000.00);

INSERT INTO Type_transactions (id_type_transactions, nom_type_transactions) VALUES
(1, 'Depot'),
(2, 'Retrait');

INSERT INTO Details_fonds (id_fonds, id_type_transactions, id_prets, date_details) VALUES
(1, 1, NULL, '2024-01-01'),
(2, 1, Null, '2024-03-15');
