<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Établissement Financier - Gestion des Fonds</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
      
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container-fluid">
            <div class="d-flex align-items-center">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="logo ms-3">
                    <i class="fas fa-university"></i>
                    <span>FinanceBank</span>
                </div>
            </div>
            
            <div class="header-actions">
                <button class="btn btn-outline-light btn-sm">
                    <i class="fas fa-user"></i> Profil
                </button>
                <button class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </button>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h5>Menu Principal</h5>
            <div class="sidebar-subtitle">Gestion Financière</div>
        </div>
        
        <div class="nav-menu">
            <div class="nav-item">
                <a href="#" class="nav-link active" onclick="showSection('fonds')">
                    <i class="fas fa-coins"></i>
                    <span>Gestion des Fonds</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="views/Pret/simulation.html" class="nav-link" onclick="showSection('simulation')">

                    <i class="fas fa-calculator"></i>
                    <span>Simulation Prêt</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="views/Pret/pretForm.html" class="nav-link" onclick="showSection('ajout-pret')">
                    <i class="fas fa-plus-circle"></i>
                    <span>Ajouter Prêt</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="views/Pret/liste.html" class="nav-link" onclick="showSection('liste-prets')">
                    <i class="fas fa-list"></i>
                    <span>Liste des Prêts</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="views/Pret/echeancier.html" class="nav-link" onclick="showSection('echeancier')">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Échéancier</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="views/Pret/interet.html" class="nav-link" onclick="showSection('interets')">
                    <i class="fas fa-chart-line"></i>
                    <span>Intérêts par Mois</span>
                </a>
            </div>
            <div class="nav-item"><a href="views/Pret/simulationCompare.html" class="nav-link"><i class="fas fa-balance-scale mr-3"></i><span>Comparer Simulation</span></a></div>

        </div>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="user-details">
                    <div class="user-name">Admin</div>
                    <div class="user-role">Gestionnaire</div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-wrapper">
            <!-- Section Gestion des Fonds -->
            <div id="section-fonds" class="content-section">
                <div class="page-header">
                    <h1><i class="fas fa-coins me-3"></i>Gestion des Fonds</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                            <li class="breadcrumb-item active">Gestion des Fonds</li>
                        </ol>
                    </nav>
                </div>

                <!-- Current Fund Display -->
                <div class="current-fund fade-in">
                    <div class="fund-label">Fond Actuel</div>
                    <div class="fund-value">
                        <span id="valeur-fond-actuel">Chargement...</span>
                    </div>
                    <div class="fund-currency">Ariary</div>
                </div>

                <!-- Form Section -->
                <div class="form-section fade-in">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="montant_fonds">Montant du fonds</label>
                                <input type="number" class="form-control" id="montant_fonds" 
                                       placeholder="Entrez le montant" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_details">Date du détail</label>
                                <input type="date" class="form-control" id="date_details">
                            </div>
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="ajouterOuModifierFonds()">
                            <i class="fas fa-save me-2"></i>Ajouter / Modifier Fonds
                        </button>
                        <button class="btn btn-secondary" onclick="resetFormFonds()">
                            <i class="fas fa-undo me-2"></i>Réinitialiser
                        </button>
                    </div>
                    <input type="hidden" id="id_fonds" />
                </div>

                <!-- Table Section -->
                <div class="financial-card fade-in">
                    <div class="card-header">
                        <h5><i class="fas fa-table me-2"></i>Liste des Fonds</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-container">
                            <table class="table table-hover mb-0" id="table-fonds">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Montant</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Autres sections seront ajoutées ici -->
            <div id="section-simulation" class="content-section" style="display: none;">
                <div class="page-header">
                    <h1><i class="fas fa-calculator me-3"></i>Simulation de Prêt</h1>
                </div>
                <!-- Contenu de simulation -->
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
        });

        // Navigation
        function showSection(sectionName) {
            // Hide all sections
            const sections = document.querySelectorAll('.content-section');
            sections.forEach(section => {
                section.style.display = 'none';
            });
            
            // Show selected section
            document.getElementById('section-' + sectionName).style.display = 'block';
            
            // Update active nav link
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Votre logique JavaScript existante
        const apiBase = "http://localhost/Etablissement-financier-et-pr-t-bancaire/ws";

        function ajax(method, url, data, callback) {
            const xhr = new XMLHttpRequest();
            xhr.open(method, apiBase + url, true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = () => {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    callback(JSON.parse(xhr.responseText));
                }
            };
            xhr.send(data);
        }

        function afficherFondActuel() {
            ajax("GET", "/fond_actuel", null, (data) => {
                let valeur = data.fond_actuel !== undefined ? data.fond_actuel :
                    data.total_depot !== undefined && data.total_retrait !== undefined ?
                    data.total_depot - data.total_retrait : "Erreur";
                document.getElementById("valeur-fond-actuel").textContent = valeur;
            });
        }

        function chargerFonds() {
            ajax("GET", "/fonds", null, (data) => {
                const tbody = document.querySelector("#table-fonds tbody");
                tbody.innerHTML = "";
                if (Array.isArray(data)) {
                    data.forEach((f) => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${f.id_fonds}</td>
                            <td>${f.montant_fonds}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary me-2" onclick='remplirFormulaireFonds(${JSON.stringify(f)})'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick='supprimerFonds(${f.id_fonds})'>
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = "<tr><td colspan='3' class='text-center'>Aucun fonds trouvé</td></tr>";
                }
            });
        }

        function ajouterOuModifierFonds() {
            const id = document.getElementById("id_fonds").value;
            const montant = document.getElementById("montant_fonds").value;
            const date_details = document.getElementById("date_details").value;
            
            const data = `montant_fonds=${encodeURIComponent(montant)}&date_details=${encodeURIComponent(date_details)}`;
            if (id) {
                ajax("PUT", `/fonds/${id}`, data, () => {
                    resetFormFonds();
                    chargerFonds();
                    afficherFondActuel();
                });
            } else {
                ajax("POST", "/fonds", data, () => {
                    resetFormFonds();
                    chargerFonds();
                    afficherFondActuel();
                });
            }
        }

        function remplirFormulaireFonds(f) {
            document.getElementById("id_fonds").value = f.id_fonds;
            document.getElementById("montant_fonds").value = f.montant_fonds;
        }

        function supprimerFonds(id) {
            if (confirm("Supprimer ce fonds ?")) {
                ajax("DELETE", `/fonds/${id}`, null, () => {
                    chargerFonds();
                    afficherFondActuel();
                });
            }
        }

        function resetFormFonds() {
            document.getElementById("id_fonds").value = "";
            document.getElementById("montant_fonds").value = "";
            document.getElementById("date_details").value = "";
        }

        // Responsive sidebar
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            
            if (window.innerWidth <= 768) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('sidebar-collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('sidebar-collapsed');
            }
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            chargerFonds();
            afficherFondActuel();
        });
    </script>
</body>
</html>