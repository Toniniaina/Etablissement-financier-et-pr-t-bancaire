INSERT INTO Fonds (montant_fonds) VALUES (100000000.00);
INSERT INTO Types_pret (nom_type_pret) VALUES
                                           ('Crédit immobilier'),
                                           ('Crédit consommation'),
                                           ('Crédit auto');
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
                                          ('Approuvé'),
                                          ('Rejeté'),
                                          ('En cours de remboursement'),
                                          ('Remboursé');
-- Prêt 1 (Jean) : Approuvé puis en cours
INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES
                                                                            (1, 2, '2025-01-12'),  -- Approuvé
                                                                            (1, 4, '2025-02-01');  -- En cours

-- Prêt 2 (Mickael) : Approuvé puis en cours
INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES
                                                                            (2, 2, '2025-02-07'),
                                                                            (2, 4, '2025-03-01');

-- Prêt 3 (Fanja) : En attente, pas encore approuvé
INSERT INTO Mouvement_prets (id_prets, id_status_prets, date_mouvement) VALUES
    (3, 1, '2025-03-03');
INSERT INTO Type_transactions (nom_type_transactions) VALUES
                                                          ('Dépôt'),
                                                          ('Retrait'),
                                                          ('Prélèvement prêt');
-- Prélèvements liés aux remboursements de prêt (exemple simplifié)
INSERT INTO Details_fonds (id_fonds, id_type_transactions, montant_transaction, date_details, id_prets) VALUES
                                                                                                            (1, 3, 500000.00, '2025-02-10', 1),
                                                                                                            (1, 3, 500000.00, '2025-03-10', 1),
                                                                                                            (1, 3, 150000.00, '2025-03-15', 2);
