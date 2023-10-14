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
        <li class="active">Bugtracker</li>
    </ol>
    <style>
        /* Styles for open and closed tickets */
        .reponse-open {
            color: green; /* Change the color as desired */
            /* You can also add additional styles here */
        }

        .reponse-closed {
            color: red; /* Change the color as desired */
            /* You can also add additional styles here */
        }
    </style>
        <div class="page-header margin-top-10">
            <h4>Listes des bugs</h4>
        </div>
        <div class="page-header margin-top-10">
            <div class="section section-default padding-25">
                <div class="row">
                    <div class="wheel" id="wheel">
                        <?php
                        session_start();

                        // Calculer le nombre total de comptes dans votre tableau
                        $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_bugtracker_site`;");
                        $queryCount->execute();
                        $nombreDeTicket = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                        // Nombre de comptes à afficher par page
                        $ticketParPage = 10;

                        // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                        $moyenne = ceil($nombreDeTicket / $ticketParPage); // Nombre total de pages

                        // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                        $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                        if ($page < 1) {
                            $page = 1;
                        } elseif ($page > $moyenne) {
                            $page = $moyenne;
                        }

                        // Calculer l'index de début pour la requête en fonction de la page actuelle
                        $indexDebut = max(($page - 1), 0) * $ticketParPage;

                        $query = $web->prepare("SELECT * FROM website_bugtracker_site WHERE status = 'En cours' ORDER BY date LIMIT $indexDebut, $ticketParPage;");
                        $query->execute();
                        $tickets = $query->fetchAll(PDO::FETCH_ASSOC);

                        // Afficher les tickets de l'utilisateur
                        if (count($tickets) > 0) {
                            echo '<table class="table table-bordered">';
                            echo '<thead>';
                            echo '<tr>';
                            echo '<th class="text-center">ID</th>';
                            echo '<th class="text-center">GUID</th>';
                            echo '<th class="text-center">Account</th>';
                            echo '<th class="text-center">Sujet</th>';
                            echo '<th class="text-center">Date</th>';
                            echo '<th class="text-center">Status</th>';
                            echo '<th class="text-center">Voir</th>';
                            echo '</tr>';
                            echo '</thead>';
                            echo '<tbody>';

                            foreach ($tickets as $ticket) {
                                echo '<tr>';
                                echo '<td style="text-align: center;">' . $ticket['id'] . '</td>';
                                echo '<td style="text-align: center;">' . $ticket['guid'] . '</td>';
                                echo '<td style="text-align: center;">' . $ticket['account'] . '</td>';
                                echo '<td style="text-align: center;">' . $ticket['subject'] . '</td>';
                                echo '<td style="text-align: center;">' . date('d-m-y H:i:s', strtotime($ticket['date'])) . '</td>';
                                $statusClass = ($ticket['status'] === 'En cours') ? 'reponse-open' : 'reponse-closed';
                                echo '<td style="text-align: center;"><span class="' . $statusClass . '">' . $ticket['status'] . '</span></td>';
                                echo '<td style="text-align: center;"><a href="?page=adm_bugtracker_view&id=' .$ticket['id'] . '"><img src="img/devtool/eye.png" alt="Détails du bug"></a></td>';
                                echo '</tr>';
                            }

                            echo '</tbody>';
                            echo '</table>';
                        } else {
                            echo "<div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>Il n'y a aucun bug, profite ça va arriver.</div>";
                        }
                        ?>
                    </div><br>
                    <center>
                        <div class="btn-group">
                            <?php if ($page > 1) : ?>
                                <a href="?page=adm_bugtracker&num=1&bugtrackerParPage=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="?page=adm_bugtracker&num=<?php echo ($page - 1); ?>&bugtrackerParPage=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php endif; ?>
                            <?php
                            // Afficher le numéro de page 1
                            echo '<a href="?page=adm_bugtracker&num=1&bugtrackerParPage=' . $ticketParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                            // Afficher "..." s'il y a plus de 4 pages avant la page courante
                            if ($page > 4) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher les numéros de page de manière groupée
                            for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                echo '<a href="?page=adm_bugtracker&num=' . $i . '&bugtrackerParPage=' . $ticketParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                            }
                            // Afficher "..." s'il y a plus de 4 pages après la page courante
                            if ($page < $moyenne - 3) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher le numéro de la dernière page
                            echo '<a href="?page=adm_bugtracker&num=' . $moyenne . '&bugtrackerParPage=' . $ticketParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                            ?>
                            <?php if ($page < $moyenne) : ?>
                                <a href="?page=adm_bugtracker&num=<?php echo ($page + 1); ?>&bugtrackerParPage=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                <a href="?page=adm_bugtracker&num=<?php echo $moyenne; ?>&bugtrackerParPage=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </center>
                </div>
            </div>
        </div>
</div>