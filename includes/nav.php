<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="http://localhost/DBManager">
                <div class="sidebar-brand-text mx-3">DB Manager</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            
            <li class='nav-item'><a class='nav-link' href='http://localhost/DBManager'>Accueil</a></li>

            <?php
                if(isset($_SESSION['login']) && $_SESSION['login'] === true) {
                    echo "<li class='nav-item'><a class='nav-link' href='index.php?page=dblist'>Bases de données</a></li>";
                    echo "<li class='nav-item'><a class='nav-link' href=\"index.php?page=logout\">Déconnexion</a></li>";
                }
                else {
                    echo "<li class='nav-item'><a class='nav-link' href=\"index.php?page=login\">Connexion</a></li>";
                    echo "<li class='nav-item'><a class='nav-link' href=\"index.php?page=inscription\">Inscription</a></li>";
                }
            ?>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 medium">
                                    <?php echo ((isset($_SESSION['login']))
                                        ? $_SESSION['prenom'] . " " . $_SESSION['nom'] : ""); ?></span>
                            </a>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- 404 Error Text -->
                    <div class="text-center">