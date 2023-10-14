<?php

if (!isset($_SESSION['user']) || !in_array($_SESSION['data']->guid, ADMIN_GUID)) {
    // Code de sortie ou de redirection si les conditions ne sont pas satisfaites
    exit;
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $guid = $_SESSION['id'];
    $account = $_SESSION['user']; // Assurez-vous de récupérer l'identifiant de l'utilisateur connecté correctement
    $date = date('Y-m-d H:i:s');

    // Requête pour obtenir la valeur du champ 'log' depuis la table 'website_general'
    $getLogSettingQuery = $web->prepare("SELECT log FROM website_general");
    $getLogSettingQuery->execute();
    $logSetting = $getLogSettingQuery->fetchColumn(); // Récupère la valeur du champ 'log'

// Si la valeur du champ 'log' est "oui", procédez à la journalisation
    if ($logSetting === "oui") {
        $insertQuery = $web->prepare("INSERT INTO website_logs (guid, account, date, page) VALUES (:guid, :account, :date, :page)");

        // Liez les valeurs aux paramètres nommés
        $insertQuery->bindParam(':guid', $guid, PDO::PARAM_STR);
        $insertQuery->bindParam(':account', $account, PDO::PARAM_STR);
        $insertQuery->bindParam(':date', $date, PDO::PARAM_STR);
        $insertQuery->bindParam(':page', $page, PDO::PARAM_STR);

        // Exécutez la requête SQL d'insertion ici en utilisant la connexion à la base de données
        $insertQuery->execute();
    }
}
?>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Panel des Personnages</li>
    </ol>
    <div class="page-header margin-top-10">
        <h4>Gestion des Personnages</h4>
    </div>
    <div class="page-header margin-top-10">
        <div class="section section-default padding-25">
            <div class="row">
                <div class="wheel" id="wheel">
                    <?php
                    session_start();
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);

                    // Calculer le nombre total de comptes dans votre tableau
                    $queryCount = $login->prepare("SELECT COUNT(*) FROM `world_players`;");
                    $queryCount->execute();
                    $nombreDePersos = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                    // Nombre de comptes à afficher par page
                    $persosParPage = 10;

                    // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                    $moyenne = ceil($nombreDePersos / $persosParPage); // Nombre total de pages

                    // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                    $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                    if ($page < 1) {
                        $page = 1;
                    } elseif ($page > $moyenne) {
                        $page = $moyenne;
                    }

                    // Calculer l'index de début pour la requête en fonction de la page actuelle
                    $indexDebut = ($page - 1) * $persosParPage;

                    // Modifier la requête pour prendre en compte la pagination et l'index de début
                    $query = $login->prepare("SELECT id, account, server, name, logged, groupe FROM `world_players` LIMIT $indexDebut, $persosParPage;");
                    $query->execute();
                    $persos = $query->fetchAll(PDO::FETCH_ASSOC);

                    // Afficher les tickets de l'utilisateur
                    if ($persos !== false) {
                        echo '<table class="table table-bordered">';
                        echo '<thead>';
                        echo '<tr>';
                        echo '<th class="text-center">ID</th>';
                        echo '<th class="text-center">AccountID </th>';
                        echo '<th class="text-center">Account</th>';
                        echo '<th class="text-center">Server</th>';
                        echo '<th class="text-center">name</th>';
                        echo '<th class="text-center">Logger</th>';
                        echo '<th class="text-center">Groupe</th>';
                        echo '<th class="text-center">Voir</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody>';

                        foreach ($persos as $persos) {
                            $queryAccount = $login->prepare("SELECT account FROM world_accounts WHERE guid = :guid");
                            $queryAccount->bindParam(':guid', $persos['account'], PDO::PARAM_STR);
                            $queryAccount->execute();
                            $resultAccount = $queryAccount->fetch(PDO::FETCH_ASSOC);

                            echo '<tr>';
                            echo '<td style="text-align: center;">' . $persos['id'] . '</td>';
                            echo '<td style="text-align: center;">' . $persos['account'] .'</td>';
                            echo '<td style="text-align: center;">' . $resultAccount['account'] .'</td>';
                            echo '<td style="text-align: center;">';
                            if ($persos['server'] == 636) {
                                echo SERVEUR_1;
                            }
                            echo '</td>';
                            echo '<td style="text-align: center;">' . $persos['name'] . '</td>';
                            echo '<td style="text-align: center; color: ' . ($persos['logged'] == 1 ? 'green' : 'red') . ';">' . ($persos['logged'] == 1 ? 'oui' : 'non') . '</td>';
                            echo '<td style="text-align: center;">';
                            $groupe = $persos['groupe'];
                            if ($groupe == 0) {
                                echo 'Joueur';
                            } elseif ($groupe == 1) {
                                echo 'Fondateur';
                            } elseif ($groupe == 2) {
                                echo 'Administrateur';
                            } elseif ($groupe == 3) {
                                echo 'Community';
                            } elseif ($groupe == 4) {
                                echo 'Developpeur';
                            } elseif ($groupe == 5) {
                                echo 'Developpeuse';
                            } elseif ($groupe == 6) {
                                echo 'Chef modo';
                            } elseif ($groupe == 7) {
                                echo 'Modo';
                            } elseif ($groupe == 9) {
                                echo 'Modo test';
                            } elseif ($groupe == 8) {
                                echo 'Animateur';
                            } elseif ($groupe == 10) {
                                echo 'Testeur';
                            } elseif ($groupe == 11) {
                                echo 'Mercenaire';
                            } elseif ($groupe == 12) {
                                echo 'Debugueur';
                            }
                            echo '</td>';
                            echo '<td style="text-align: center;"><a href="?page=adm_perso_view&id=' . $persos['id'] . '"><img src="img/devtool/eye.png" alt="Détails du ticket"></a></td>';
                            echo '</tr>';
                        }

                        echo '</tbody>';
                        echo '</table>';
                    } else {
                        echo "<div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>Il n'y a aucun compte.</div>";
                    }
                    ?>
                </div>
                <center>
                    <div class="btn-group">
                        <?php if ($page > 1) : ?>
                            <a href="?page=adm_perso&num=1&persosParPage=<?php echo $persosParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                            <a href="?page=adm_perso&num=<?php echo ($page - 1); ?>&persosParPage=<?php echo $persosParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                        <?php else : ?>
                            <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                            <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <?php
                        // Afficher le numéro de page 1
                        echo '<a href="?page=adm_perso&num=1&persosParPage=' . $persosParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                        // Afficher "..." s'il y a plus de 4 pages avant la page courante
                        if ($page > 4) {
                            echo '<span class="btn btn-sm btn-default">...</span>';
                        }
                        // Afficher les numéros de page de manière groupée
                        for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                            echo '<a href="?page=adm_perso&num=' . $i . '&persosParPage=' . $persosParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                        }
                        // Afficher "..." s'il y a plus de 4 pages après la page courante
                        if ($page < $moyenne - 3) {
                            echo '<span class="btn btn-sm btn-default">...</span>';
                        }
                        // Afficher le numéro de la dernière page
                        echo '<a href="?page=adm_perso&num=' . $moyenne . '&persosParPage=' . $persosParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                        ?>
                        <?php if ($page < $moyenne) : ?>
                            <a href="?page=adm_perso&num=<?php echo ($page + 1); ?>&persosParPage=<?php echo $persosParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                            <a href="?page=adm_perso&num=<?php echo $moyenne; ?>&persosParPage=<?php echo $persosParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                        <?php else : ?>
                            <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                            <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                        <?php endif; ?>
                    </div>
                </center>
            </div>
        </div>
    </div>
    <div class="page-header margin-top-10">
        <h4>Recherche de Personnage</h4>
    </div>
    <center>
        <form method="post">
            <input type="search" name="search_query" id="perso-search" />
            <label for="search_type">Rechercher par :</label>
            <select name="search_type" id="search_type">
                <option value="name">Nom de Personnage</option>
                <option value="account">Account ID</option>
            </select>
            <button type="submit" name="search_button">Rechercher</button>
        </form>

    </center>

    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Assurez-vous d'inclure le code de connexion à la base de données ici

    if (isset($_POST['search_button'])) {
        $search_query = $_POST['search_query'];
        $search_type = $_POST['search_type'];

        if ($search_type === 'account') {
            $query = $login->prepare("SELECT name, id FROM `world_players` WHERE account LIKE :search_query");
        } elseif ($search_type === 'name') {
            $query = $login->prepare("SELECT name, id FROM `world_players` WHERE name LIKE :search_query");
        }

        $query->bindValue(':search_query', "%$search_query%", PDO::PARAM_STR);
        $query->execute();
        $persos = $query->fetchAll(PDO::FETCH_ASSOC);


        if (!empty($persos)) {
            echo '<table class="table table-bordered">';
            echo '<thead>';
            echo '<br/>';
            echo '<tr>';
            echo '<th class="text-center">Nom</th>';
            echo '<th class="text-center">Voir</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($persos as $perso) {
                echo '<tr>';
                echo '<td style="text-align: center;">' . $perso['name'] . '</td>';
                echo '<td style="text-align: center;"><a href="?page=adm_perso_view&id=' . $perso['id'] . '"><img src="img/devtool/eye.png" alt="Détails du ticket"></a></td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo "<br/><div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>Aucun joueur trouvé avec le nom.</div>";
        }
    }
    ?>


</div>