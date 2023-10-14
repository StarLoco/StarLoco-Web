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
        <li class="active">Panel des achats boutique</li>
    </ol>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="no-border no-padding-top">
                <div class="page-header margin-top-10"><h4>Gestions des achats boutique</h4></div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="section section-default padding-25">
                    <?php
                    if(isset($_GET['wAchat']) && is_numeric($_GET['wAchat']) && isset($_GET['remove'])) {
                        $query = $web -> prepare("DELETE FROM `website_shop_objects_purchases` WHERE `id` = ?;");
                        $query -> bindParam(1, $_GET['wAchat']);
                        $query -> execute();
                        $query -> closeCursor();
                        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Oh good!</strong> L\'achat de la boutique a été supprimer avec succès !</div><br>';
                    } ?>
                    <div class="box no-border-radius" align = "center">
                        <table class="table table-striped no-margin">
                            <thead>
                            <tr>
                                <th class="padding-left-15">#</th>
                                <th>Account ID</th>
                                <th>Objet</th>
                                <th>Quantité</th>
                                <th>Serveur</th>
                                <th>Date</th>
                                <!--<th>Actions</th>-->
                            </tr>
                            </thead>
                            <?php
                            session_start();
                            /* ini_set('display_errors', 1);
                             error_reporting(E_ALL);*/

                            // Calculer le nombre total de comptes dans votre tableau
                            $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_shop_objects_purchases`;");
                            $queryCount->execute();
                            $nombreDeAchat = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                            // Nombre de comptes à afficher par page
                            $achatParPage = 10;

                            // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                            $moyenne = ceil($nombreDeAchat / $achatParPage); // Nombre total de pages

                            // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                            $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                            if ($page < 1) {
                                $page = 1;
                            } elseif ($page > $moyenne) {
                                $page = $moyenne;
                            }

                            // Calculer l'index de début pour la requête en fonction de la page actuelle
                            $indexDebut = max(($page - 1), 0) * $achatParPage;

                            $query = $web -> prepare("SELECT * FROM `website_shop_objects_purchases` ORDER BY `id` DESC LIMIT $indexDebut, $achatParPage;");
                            $query -> execute();

                            if ($query->rowCount() > 0) : ?>
                                <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <tr>
                                        <td class="padding-left-15"><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['accountID']; ?></td>
                                        <td><?php echo $row['template']; ?></td>
                                        <td><?php echo $row['quantite']; ?></td>
                                        <?php echo '<td style="text-align: center;">';
                                            if ($row['server'] == 636) {
                                            echo SERVEUR_1;
                                            }
                                            echo '</td>';?>
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
                                        <!--<td>
                                            <a href="?page=adm_achat&wAchat=<?php /*echo $row['id']; */?>&remove"<span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="" data-original-title="Supprimer"><i class="ion-trash-b"></i></span>
                                        </td>-->
                            </tr><?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7">
                                        <div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>
                                            <strong>Oh non!</strong> Il n'y a aucun achat d'objet dans la boutique !
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <center>
                        <div class="btn-group">
                            <?php if ($page > 1) : ?>
                                <a href="?page=adm_achat_boutique&num=1&achatParPage=<?php echo $achatParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="?page=adm_achat_boutique&num=<?php echo ($page - 1); ?>&achatParPage=<?php echo $achatParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php endif; ?>
                            <?php
                            // Afficher le numéro de page 1
                            echo '<a href="?page=adm_achat_boutique&num=1&achatParPage=' . $achatParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                            // Afficher "..." s'il y a plus de 4 pages avant la page courante
                            if ($page > 4) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher les numéros de page de manière groupée
                            for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                echo '<a href="?page=adm_achat_boutique&num=' . $i . '&achatParPage=' . $achatParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                            }
                            // Afficher "..." s'il y a plus de 4 pages après la page courante
                            if ($page < $moyenne - 3) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher le numéro de la dernière page
                            echo '<a href="?page=adm_achat_boutique&num=' . $moyenne . '&achatParPage=' . $achatParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                            ?>
                            <?php if ($page < $moyenne) : ?>
                                <a href="?page=adm_achat_boutique&num=<?php echo ($page + 1); ?>&ticketParPage=<?php echo $achatParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                <a href="?page=adm_achat_boutique&num=<?php echo $moyenne; ?>&ticketParPage=<?php echo $achatParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
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