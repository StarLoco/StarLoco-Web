<?php
session_start();

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
        <li class="active">Panel tickets encyclopédie</li>
    </ol>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="no-border no-padding-top">
                <div class="page-header margin-top-10">
                    <h4>Gestion des tickets encyclopédie</h4>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="section section-default padding-25">
                    <?php
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);

                    if (isset($_GET['wTicket']) && is_numeric($_GET['wTicket']) && isset($_GET['remove'])) {
                        $query = $web->prepare("DELETE FROM `website_ticket_encyclopedie` WHERE `id` = ?;");
                        $query->bindParam(1, $_GET['wTicket']);
                        $query->execute();
                        $query->closeCursor();
                        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Oh good!</strong> Le ticket a été supprimé avec succès !</div><br>';
                        echo '<script>setTimeout(function() {window.location.href = "?page=adm_tickets_encyclopedie";}, 1000); // 1000 millisecondes = 1 secondes</script>';
                    } ?>

                    <?php
                    // Calculer le nombre total de comptes dans votre tableau
                    $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_ticket_encyclopedie`;");
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

                    // Récupérer les paramètres de tri et de filtre depuis l'URL
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id'; // Tri par défaut selon l'identifiant du ticket
                    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all'; // Filtre par défaut pour afficher tous les tickets

                    // Modifier la requête SQL pour utiliser les paramètres de tri et de filtre
                    $query = $web->prepare("SELECT * FROM `website_ticket_encyclopedie` 
                         WHERE (:filter = 'all' OR `type` = :filter) 
                         AND (CASE WHEN :sort = 'monster' THEN `type` = 'Monstre'
                                   WHEN :sort = 'item' THEN `type` = 'Objet'
                                   ELSE TRUE END)
                         ORDER BY 
                            CASE 
                                WHEN :sort = 'monster' THEN `recherche`
                                WHEN :sort = 'item' THEN `type`
                                ELSE `id` 
                            END 
                         ASC LIMIT $indexDebut, $ticketParPage;");
                    $query->bindParam(':filter', $filter);
                    $query->bindParam(':sort', $sort);
                    $query->execute();
                    ?>
                    Filtrer par :
                    <?php if ($sort === 'all') { ?>
                        <strong>Tous</strong>
                    <?php } else { ?>
                        <a href="?page=adm_tickets_encyclopedie&sort=all">Tous</a>
                    <?php } ?>
                    <?php if ($sort === 'monster') { ?>
                        | <strong>Monstre</strong>
                    <?php } else { ?>
                        | <a href="?page=adm_tickets_encyclopedie&sort=monster">Monstre</a>
                    <?php } ?>

                    <?php if ($sort === 'item') { ?>
                        | <strong>Objet</strong>
                    <?php } else { ?>
                        | <a href="?page=adm_tickets_encyclopedie&sort=item">Objet</a>
                    <?php } ?>
                    <br> <div class="box no-border-radius">
                        <table class="table table-striped no-margin">
                            <thead>
                            <tr>
                                <th class="padding-left-15">#</th>
                                <th>Account</th>
                                <th>Type</th>
                                <th>Recherche</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            ini_set('display_errors', 1);
                            ini_set('display_startup_errors', 1);
                            error_reporting(E_ALL);

                            while ($row = $query->fetch()) {
                                ?>
                                <tr>
                                    <td class="padding-left-15"><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['account']; ?></td>
                                    <td><?php echo $row['type']; ?></td>
                                    <td><?php echo $row['recherche']; ?></td>
                                    <?php echo '<td>';
                                    if (isset($row['date'])) {
                                        $formattedDate = str_replace('~', '-', $row['date']);
                                        $timestamp = strtotime($formattedDate);
                                        $date = date('d M y, H:i', $timestamp);
                                        echo $date;
                                    } else {
                                        echo "Non dispo";
                                    }
                                    echo '</td>'; ?>
                                    <td>
                                        <a href="?page=adm_tickets_encyclopedie&wTicket=<?php echo $row['id']; ?>&remove&filter=<?php echo $filter; ?>&sort=<?php echo $sort; ?>">
                                                <span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="" data-original-title="Supprimer">
                                                    <i class="ion-trash-b"></i>
                                                </span>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                            $query->closeCursor();

                            if ($query->rowCount() === 0) {
                                echo '<tr><td colspan="6"><center><div class="alert alert-info no-border-radius no-margin" role="alert"><strong>Oh non!</strong> Aucun ticket encyclopédie trouvé pour le moment.</div></center></td></tr>';
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <center>
                        <div class="btn-group">
                            <?php if ($page > 1) : ?>
                                <a href="?page=adm_tickets_encyclopedie&num=1&tickets_encyclopedie=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="?page=adm_tickets_encyclopedie&num=<?php echo ($page - 1); ?>&ticketParPage=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php endif; ?>
                            <?php
                            // Afficher le numéro de page 1
                            echo '<a href="?page=adm_tickets_encyclopedie&num=1&tickets_encyclopedie=' . $ticketParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                            // Afficher "..." s'il y a plus de 4 pages avant la page courante
                            if ($page > 4) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher les numéros de page de manière groupée
                            for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                echo '<a href="?page=adm_tickets_encyclopedie&num=' . $i . '&tickets_encyclopedie=' . $ticketParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                            }
                            // Afficher "..." s'il y a plus de 4 pages après la page courante
                            if ($page < $moyenne - 3) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher le numéro de la dernière page
                            echo '<a href="?page=adm_tickets_encyclopedie&num=' . $moyenne . '&tickets_encyclopedies=' . $ticketParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                            ?>
                            <?php if ($page < $moyenne) : ?>
                                <a href="?page=adm_tickets_encyclopedie&num=<?php echo ($page + 1); ?>&ticketParPage=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                <a href="?page=adm_tickets_encyclopedie&num=<?php echo $moyenne; ?>&ticketParPage=<?php echo $ticketParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </center>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- ./leftside -->
