INSERT INTO Types_pret (id_types_pret, nom_type_pret) VALUES
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
(1, 100000.00),
(2, 50000.00);

INSERT INTO Type_transactions (id_type_transactions, nom_type_transactions) VALUES
(1, 'Depot'),
(2, 'Retrait'),

INSERT INTO Details_fonds (id_fonds, id_type_transactions, id_prets, date_details) VALUES
(1, 1, NULL, '2024-01-01'),
(2, 1, Null, '2024-03-15');

INSERT INTO Prets (id_types_pret, id_clients, date_debut, duree_en_mois, montant_prets, assurance, delai_grace) VALUES
(1, 1, '2024-01-01', 36, 15000000.00, 10.00, 0),
(2, 2, '2024-03-15', 180, 200000000.00, 5.00, 0),
(3, 3, '2024-06-01', 60, 25000000.00, 12.50, 0),
(4, 4, '2024-09-01', 24, 5000000.00, 1.00, 0),
(5, 5, '2024-11-01', 48, 10000000.00, 3.00, 0),
(3, 1, '2025-01-01', 48, 30000000.00, 7.50, 0),
(1, 2, '2025-02-01', 24, 10000000.00, 2.00, 0),
(2, 3, '2025-03-01', 120, 150000000.00, 6.00, 0),
(5, 4, '2025-04-01', 36, 8000000.00, 4.00, 0),
(4, 5, '2025-05-01', 12, 3000000.00, 1.50, 0);

INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES
(1, 1, '2024-01-01'),
(2, 1, '2024-03-15'),
(3, 1, '2024-06-01'),
(4, 1, '2024-09-01'),
(5, 1, '2024-11-01'),
(6, 1, '2025-01-01'),
(7, 1, '2025-02-01'),
(8, 1, '2025-03-01'),
(9, 1, '2025-04-01'),
(10, 1, '2025-05-01');
