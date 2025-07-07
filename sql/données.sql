INSERT INTO Types_pret (nom_type_pret) VALUES
                                           ('Credit immobilier'),
                                           ('Credit consommation'),
                                           ('Credit auto');
INSERT INTO Taux (id_types_pret, pourcentage) VALUES
                                                  (1, 5.50),  -- Crédit immobilier
                                                  (2, 9.25),  -- Crédit consommation
                                                  (3, 7.75);  -- Crédit auto
INSERT INTO Clients (nom_clients, prenom_clients, date_naissance, salaire) VALUES
                                                                               ('RANAIVO', 'Jean', '1990-04-15', 1200000.00),
                                                                               ('RAKOTO', 'Mickael', '1985-10-22', 950000.00),
                                                                               ('RASOA', 'Fanja', '1995-08-07', 750000.00);
INSERT INTO Prets (id_types_pret, id_clients, montant_prets, date_debut, duree_en_mois) VALUES
                                                                                            (1, 1, 50000000.00, '2025-01-10', 240),  -- Jean
                                                                                            (2, 2, 3000000.00, '2025-02-05', 24),    -- Mickael
                                                                                            (3, 3, 6000000.00, '2025-03-01', 36);    -- Fanja
INSERT INTO Status_prets (nom_status) VALUES
                                          ('En attente'),
                                          ('Approuve'),
                                          ('Rejete'),
                                          ('En cours de remboursement'),
                                          ('Rembourse');
-- Prêt 1 (Jean) : Approuvé puis en cours
INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES
                                                                            (1, 2, '2025-01-12'),  -- Approuvé
                                                                            (1, 4, '2025-02-01');  -- En cours

INSERT INTO Type_transactions (nom_type_transactions) VALUES
                                                          ('Depot'),
                                                          ('Retrait');
INSERT INTO Fonds (montant_fonds) VALUES
(500000.00),  -- id_fonds = 1 (Dépôt)
(200000.00),  -- id_fonds = 2 (Dépôt)
(100000.00);  -- id_fonds = 3 (Prélèvement prêt)

-- 2 dépôts
INSERT INTO Details_fonds (id_fonds, id_type_transactions, id_prets, date_details)
VALUES
(1, 1, NULL, '2025-07-01'),  -- 500000.00 dépôt
(2, 1, NULL, '2025-07-05');  -- 200000.00 dépôt

-- 1 prélèvement pour un prêt
INSERT INTO Details_fonds (id_fonds, id_type_transactions, id_prets, date_details)
VALUES
(3, 2, NULL, '2025-07-06');  -- 100000.00 sorti pour le prêt id=1
