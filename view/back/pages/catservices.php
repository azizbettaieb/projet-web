<?php

include_once "../../../config.php";
include_once "../../../Controller/categorieC.php";

include_once '../../../Controller/serviceC.php';
$serviceC = new ServiceC();
$listeServices = $serviceC->afficherServices();

$categorieC = new CategorieC();
$categoriesList = $categorieC->recupererToutesCategories(); 
$categories = [];
$counts = [];

// Manually count services per category
$categoryCount = [];
foreach ($listeServices as $service) {
    $categorie = $service['nom_categorie'];
    if (!isset($categoryCount[$categorie])) {
        $categoryCount[$categorie] = 0;
    }
    $categoryCount[$categorie]++;
}

// Prepare data for chart
foreach ($categoryCount as $categorie => $count) {
    $categories[] = $categorie;
    $counts[] = $count;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../assets/img/favicon.png">
    <title>
        Material Dashboard 3 by Creative Tim
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        .sortable-header {
            cursor: pointer;
            user-select: none;
        }
        .sortable-header:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .sort-icon {
            vertical-align: middle;
            opacity: 0.5;
        }
    </style>
    <!-- CSS Files -->
    <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="g-sidenav-show  bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0"
                href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
                <img src="../assets/img/logo-ct-dark.png" class="navbar-brand-img" width="26" height="26"
                    alt="main_logo">
                <span class="ms-1 text-sm text-dark">Creative Tim</span>
            </a>
        </div>
        <hr class="horizontal dark mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active bg-gradient-dark text-white" href="../pages/dashboard.html">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/cattrans.php">
                        <span class="nav-link-text ms-1">Transport</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/catservices.php">
                        <span class="nav-link-text ms-1">Services</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/billing.html">
                        <span class="nav-link-text ms-1">liste produits</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/notifications.html">
                        <span class="nav-link-text ms-1">liste evenements et formations </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/notifications.html">
                        <span class="nav-link-text ms-1">liste blogs </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/notifications.html">
                        <span class="nav-link-text ms-1">liste reclamations</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Account pages
                    </h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/profile.html">
                        <i class="material-symbols-rounded opacity-5">person</i>
                        <span class="nav-link-text ms-1">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="../pages/sign-in.html">
                        <i class="material-symbols-rounded opacity-5">login</i>
                        <span class="nav-link-text ms-1">Log Out</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Liste des Services</h6>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <input type="text" id="searchServices" class="form-control form-control-sm bg-white text-dark" placeholder="Rechercher...">
                                    </div>
                                    <div class="me-3">
                                        <button id="toggleEcologique" class="btn btn-sm btn-outline-secondary" data-filter="all">
                                            Tous
                                        </button>
                                    </div>
                                    <a href="ajouter_service.php" class="btn btn-outline-light me-3">
                                        <i class="material-symbols-rounded me-1">add</i>
                                        Ajouter Service
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nom</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Description</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 sortable-header" data-sort="price">
                                                Prix
                                                <span class="sort-icon ms-1">
                                                    <i class="material-symbols-rounded" style="font-size: 14px;">swap_vert</i>
                                                </span>
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Écologique</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Catégorie</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
if (!empty($listeServices)) {
    foreach ($listeServices as $index => $service) {
        echo "<tr>
            <td>
                <div class='d-flex flex-column justify-content-center'>
                    <p class='text-xs text-secondary mb-0'>" . $service['id_service'] . "</p>
                </div>
            </td>
            <td>
                <div class='d-flex flex-column justify-content-center'>
                    <h6 class='mb-0 text-sm'>" . htmlspecialchars($service['service_name']) . "</h6>
                </div>
            </td>
            <td class='align-middle text-center text-sm'>
                <span class='text-secondary text-xs font-weight-bold'>" . htmlspecialchars($service['service_description']) . "</span>
            </td>
            <td class='align-middle text-center text-sm' data-price='" . htmlspecialchars($service['price']) . "'>
                <span class='text-secondary text-xs font-weight-bold'>" . htmlspecialchars($service['price']) . " DT</span>
            </td>
            <td class='align-middle text-center text-sm'>
                <span class='badge badge-sm " . ($service['eco_friendly'] ? 'bg-gradient-success' : 'bg-gradient-secondary') . "'>" . ($service['eco_friendly'] ? 'Oui' : 'Non') . "</span>
            </td>
            <td class='align-middle text-center text-sm'>
                <span class='text-secondary text-xs font-weight-bold'>" . htmlspecialchars($service['nom_categorie']) . "</span>
            </td>
            <td class='align-middle text-center'>
                <a href='modservice.php?id=" . $service['id_service'] . "' class='btn btn-warning btn-sm me-1'>Modifier</a>
                <a href='#' onclick='confirmDelete(" . $service['id_service'] . ")' class='btn btn-danger btn-sm'>Supprimer</a>
            </td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='7' class='text-center'>Aucun service trouvé.</td>
    </tr>";
}
?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                                <h6 class="text-white text-capitalize ps-3">Catégories de Services</h6>
                                <a href="ajouter_categorie_services.php" class="btn btn-outline-light me-3">
                                    <i class="material-symbols-rounded me-1">add_circle</i>
                                    Ajouter Catégorie
                                </a>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center justify-content-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nom</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Description</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
if (!empty($categoriesList)) {
    foreach ($categoriesList as $index => $categorie) {
        echo "<tr>
            <td>
                <div class='d-flex px-2'>
                    <div class='my-auto'>
                        <h6 class='mb-0 text-sm'>" . ($index + 1) . "</h6>
                    </div>
                </div>
            </td>
            <td>
                <p class='text-sm font-weight-bold mb-0'>" . htmlspecialchars($categorie['nom_categorie']) . "</p>
            </td>
            <td>
                <span class='text-xs font-weight-bold'>" . htmlspecialchars($categorie['description']) . "</span>
            </td>
            <td class='align-middle text-center'>
                <a href='modcategorie_services.php?id=" . $categorie['id_categorie'] . "' class='btn btn-warning btn-sm me-1'>Modifier</a>
                <a href='supprimercat_services.php?id=" . $categorie['id_categorie'] . "' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette catégorie ?\")' class='btn btn-danger btn-sm'>Supprimer</a>            </td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='4' class='text-center'>Aucune catégorie trouvée.</td>
    </tr>";
}
?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3 d-flex justify-content-between align-items-center">
                            <h6 class="text-white text-capitalize ps-3">Nombre de Service par Categorie
                            </h6>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="categorieChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>  
    <footer class="footer py-4">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-muted text-lg-start">
                        &copy;
                        <script>
                            document.write(new Date().getFullYear())
                        </script>,
                        made with
                        <i class="fa fa-heart"></i> by
                        <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                        for a better web.
                    </div>
                </div>
            </div>
        </div>
    </footer>
    </div>

    <div class="fixed-plugin">
        <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
            <i class="material-symbols-rounded py-2">settings</i>
        </a>
        <div class="card shadow-lg">
            <div class="card-header pb-0 pt-3">
                <div class="float-start">
                    <h5 class="mt-3 mb-0">Material UI Configurator</h5>
                    <p>See our dashboard options.</p>
                </div>
                <div class="float-end mt-4">
                    <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
                        <i class="material-symbols-rounded">clear</i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(id) {
        if (confirm('Voulez-vous vraiment supprimer ce service ?')) {
            window.location.href = 'supprimerservice.php?id=' + id;
        }
    }

    var ctx = document.getElementById('categorieChart').getContext('2d');
    var categorieChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($categories); ?>,
            datasets: [{
                label: 'Nombre de Services',
                data: <?php echo json_encode($counts); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Nombre de services par catégorie',
                    color: 'white',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255,255,255,0.1)'
                    },
                    ticks: {
                        color: 'white',
                        font: {
                            size: 12
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255,255,255,0.1)'
                    },
                    ticks: {
                        color: 'white',
                        font: {
                            size: 12
                        },
                        precision: 0
                    }
                }
            }
        }
    });
    </script>

    <!--   Core JS Files   -->
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <style>
        .sortable-header {
            cursor: pointer;
            user-select: none;
        }
        .sortable-header:hover {
            background-color: rgba(0,0,0,0.05);
        }
        .sort-icon {
            vertical-align: middle;
            opacity: 0.5;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchServices');
            const tableRows = document.querySelectorAll('table tbody tr');
            const sortableHeaders = document.querySelectorAll('.sortable-header');

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                tableRows.forEach(row => {
                    const cells = row.getElementsByTagName('td');
                    let rowVisible = false;

                    // Search across multiple columns
                    for (let cell of cells) {
                        const cellText = cell.textContent.toLowerCase();
                        if (cellText.includes(searchTerm)) {
                            rowVisible = true;
                            break;
                        }
                    }

                    row.style.display = rowVisible ? '' : 'none';
                });
            });

            // Sorting functionality
            function initTableSorting() {
                const priceHeader = document.querySelector('.sortable-header[data-sort="price"]');
                
                if (!priceHeader) {
                    console.error('Price header not found');
                    return;
                }

                console.log('Price header found:', priceHeader);

                priceHeader.addEventListener('click', function() {
                    console.log('Price header clicked');

                    const table = this.closest('table');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    const sortIcon = this.querySelector('.sort-icon i');

                    console.log('Total rows:', rows.length);

                    // Determine current sort direction
                    const currentDirection = this.getAttribute('data-sort-direction') || 'asc';
                    const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';

                    console.log('Current direction:', currentDirection, 'New direction:', newDirection);

                    // Sort rows
                    rows.sort((a, b) => {
                        const priceCell_a = a.querySelector('td[data-price]');
                        const priceCell_b = b.querySelector('td[data-price]');

                        if (!priceCell_a || !priceCell_b) {
                            console.error('Price cell missing', priceCell_a, priceCell_b);
                            return 0;
                        }

                        const priceA = parseFloat(priceCell_a.getAttribute('data-price'));
                        const priceB = parseFloat(priceCell_b.getAttribute('data-price'));

                        console.log('Comparing prices:', priceA, priceB);

                        return newDirection === 'asc' ? priceA - priceB : priceB - priceA;
                    });

                    // Clear and repopulate tbody
                    tbody.innerHTML = '';
                    rows.forEach(row => tbody.appendChild(row));

                    // Update sort direction and icon
                    this.setAttribute('data-sort-direction', newDirection);
                    sortIcon.textContent = newDirection === 'asc' ? 'arrow_upward' : 'arrow_downward';

                    console.log('Sorting complete');
                });
            }

            // Try multiple ways to ensure the script runs
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTableSorting);
            } else {
                initTableSorting();

                // Écologique filtering
                const toggleEcologiqueBtn = document.getElementById('toggleEcologique');
                
                toggleEcologiqueBtn.addEventListener('click', function() {
                    const currentFilter = this.getAttribute('data-filter');
                    
                    // Cycle through filter states: all -> oui -> non -> all
                    const newFilter = currentFilter === 'all' ? 'oui' : 
                                      currentFilter === 'oui' ? 'non' : 'all';
                    
                    this.setAttribute('data-filter', newFilter);
                    
                    // Update button text and styling
                    switch(newFilter) {
                        case 'all':
                            this.textContent = 'Tous';
                            this.className = 'btn btn-sm btn-outline-secondary';
                            break;
                        case 'oui':
                            this.textContent = 'Écologique';
                            this.className = 'btn btn-sm btn-outline-success';
                            break;
                        case 'non':
                            this.textContent = 'Non Écologique';
                            this.className = 'btn btn-sm btn-outline-danger';
                            break;
                    }

                    // Filter rows
                    tableRows.forEach(row => {
                        // Ecological status is in the 5th column (index 4)
                        const ecologiqueCell = row.querySelector('td:nth-child(5) .badge');
                        
                        if (!ecologiqueCell) {
                            row.style.display = '';
                            return;
                        }

                        const isEcologique = ecologiqueCell.textContent.trim() === 'Oui';

                        switch(newFilter) {
                            case 'all':
                                row.style.display = '';
                                break;
                            case 'oui':
                                row.style.display = isEcologique ? '' : 'none';
                                break;
                            case 'non':
                                row.style.display = !isEcologique ? '' : 'none';
                                break;
                        }
                    });
                });
            }
        });
    </script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>
