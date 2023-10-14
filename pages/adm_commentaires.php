<style>
    .truncate-cell {
        max-width: 100px; /* Ajustez la largeur maximale en fonction de vos besoins */
        white-space: nowrap; /* Empêche le texte de passer à la ligne */
        overflow: hidden; /* Masque le contenu excédentaire */
        text-overflow: ellipsis; /* Ajoute des points de suspension (...) à la fin du texte tronqué */
    }
</style>
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

// Traitement de la modification du commentaire
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/

// Traitement de la modification du commentaire

if (isset($_POST["editCommentaire"]) && isset($_POST["comment_id"]) && isset($_POST["comment_text"]) && isset($_POST["article_id"]) && isset($_POST["pseudo"]) && isset($_POST["comment_date"])) {
    $comment_id = $_POST["comment_id"];
    $comment_text = $_POST["comment_text"];
    $article_id = $_POST["article_id"];
    $pseudo = $_POST["pseudo"];
    $comment_date = $_POST["comment_date"];

    $query = $web->prepare("UPDATE `website_timeline_news_comments` SET `comment_text` = ?, `article_id` = ?, `comment_date` = ?, `pseudo` = ? WHERE `comment_id` = ?");
    $web->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query->bindParam(1, $comment_text);
    $query->bindParam(2, $article_id);
    $query->bindParam(3, $comment_date);
    $query->bindParam(4, $pseudo);
    $query->bindParam(5, $comment_id);

    if ($query->execute()) {
        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> Le commentaire a été modifié avec succès !</div><br>';
        echo '<script>setTimeout(function() {window.location.href = "?page=adm_commentaires";}, 1000); // 1000 millisecondes = 1 seconde</script>';
    } else {
        echo '<div class="alert alert-danger no-border-radius no-margin" role="alert"><strong>Oops !</strong> Une erreur s\'est produite lors de la modification du commentaire.</div><br>';
    }
}
?>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Panel des Commentaires</li>
    </ol>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="page-header margin-top-10"><h4>Gestion des commentaires</h4></div>
                <div class="section section-default padding-25">
                    <?php
                    if (isset($_GET['wCommentaire']) && is_numeric($_GET['wCommentaire']) && isset($_GET['remove'])) {
                        $query = $web->prepare("DELETE FROM `website_timeline_news_comments` WHERE `comment_id` = ?;");
                        $query->bindParam(1, $_GET['wCommentaire']);
                        $query->execute();
                        $query->closeCursor();
                        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> Le commentaire a été supprimée avec succès !</div><br>';
                        echo '<script>setTimeout(function() {window.location.href = "?page=adm_commentaires";}, 1000); // 1000 millisecondes = 1 secondes</script>';
                    }
                    ?>
                    <div class="box no-border-radius">
                        <table class="table table-striped no-margin">
                            <thead>
                            <tr>
                                <th>Id</th>
                                <th>Article</th>
                                <th>Pseudo</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            session_start();
                          /*  ini_set('display_errors', 1);
                             error_reporting(E_ALL);*/

                            // Calculer le nombre total de comptes dans votre tableau
                            $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_timeline_news_comments`;");
                            $queryCount->execute();
                            $nombreDeCommentaire = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                            // Nombre de comptes à afficher par page
                            $commentaireParPage =10;

                            // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                            $moyenne = ceil($nombreDeCommentaire / $commentaireParPage); // Nombre total de pages

                            // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                            $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                            if ($page < 1) {
                                $page = 1;
                            } elseif ($page > $moyenne) {
                                $page = $moyenne;
                            }

                            // Calculer l'index de début pour la requête en fonction de la page actuelle
                            $indexDebut = max(($page - 1), 0) * $commentaireParPage;

                            $query = $web->prepare("SELECT * FROM `website_timeline_news_comments` ORDER BY `comment_id` DESC LIMIT $indexDebut, $commentaireParPage;");
                            $query->execute();

                            ?>

                            <?php if ($query->rowCount() > 0) : ?>
                                <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <tr data-commentaire-id="<?php echo $row['comment_id']; ?>">
                                        <td><?php
                                            if (isset($row['comment_id'])) {
                                                echo $row['comment_id'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['article_id'])) {
                                                echo $row['article_id'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['pseudo'])) {
                                                echo $row['pseudo'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['comment_text'])) {
                                                echo $row['comment_text'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['comment_date'])) {
                                                echo $row['comment_date'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td>
                                            <span class="btn btn-info btn-outline btn-sm" data-toggle="tooltip" title="Modifier" onclick="showEditForm(<?php echo $row['comment_id']; ?>)"><i class="ion-edit"></i></span>
                                            <a href="?page=adm_commentaires&wCommentaire=<?php echo $row['comment_id']; ?>&remove"><span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="Supprimer"><i class="ion-trash-b"></i></span></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6">
                                        <div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>
                                            <strong>Oh non!</strong> Il n'y a aucun commentaire.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div><br>
                    <center>
                        <div class="btn-group">
                            <a href="?page=adm_commentaires&num=1&commentaireParPage=<?php echo $commentaireParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                            <?php if ($page > 1) : ?>
                                <a href="?page=adm_commentaires&num=<?php echo ($page - 1); ?>&commentaireParPage=<?php echo $commentaireParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php endif; ?>
                            <?php
                            // Afficher le numéro de page 1
                            echo '<a href="?page=adm_commentaires&num=1&commentaireParPage=' . $commentaireParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                            // Afficher les points de suspension s'il y a plus de 4 pages avant la page courante
                            if ($page > 4) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher les numéros de page de manière groupée
                            for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                echo '<a href="?page=adm_commentaires&num=' . $i . '&commentaireParPage=' . $commentaireParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                            }
                            // Afficher les points de suspension s'il y a plus de 4 pages après la page courante
                            if ($page < $moyenne - 3) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher le numéro de la dernière page
                            echo '<a href="?page=adm_commentaires&num=' . $moyenne . '&commentaireParPage=' . $commentaireParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                            ?>
                            <?php if ($page < $moyenne) : ?>
                                <a href="?page=adm_commentaires&num=<?php echo ($page + 1); ?>&commentaireParPage=<?php echo $commentaireParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                            <?php endif; ?>
                            <a href="?page=adm_commentaires&num=<?php echo $moyenne; ?>&commentaireParPage=<?php echo $commentaireParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
                        </div>
                    </center>
                </div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="no-border no-padding-top">
                <div class="section section-default padding-25">
                    <!-- Formulaire d'édition (masqué par défaut) -->
                    <div id="editFormContainer" style="display: none;">
                        <form id="editForm" method="post" action="?page=adm_commentaires">
                            <!-- Ajouter les champs cachés pour la gestion de l'édition -->
                            <input type="hidden" name="editCommentaire" value="1">
                            <input type="hidden" name="commentaireId">
                            <div class="control-group col-md-12 no-padding col-xs-12 margin-top-15">
                                <div class="controls">
                                    <input class="form-control" name="comment_id" rows="3" style="max-width: 100%;" placeholder="Commentaire.." required readonly></input>
                                </div>
                                <div class="controls">
                                    <input class="form-control" name="article_id" rows="3" style="max-width: 100%;" placeholder="Article.." required readonly></input>
                                </div>
                                <div class="controls">
                                    <input class="form-control" name="pseudo" rows="3" style="max-width: 100%;" placeholder="Pseudo.." required readonly></input>
                                </div>
                                <div class="controls">
                                    <input class="form-control" name="comment_text" rows="3" style="max-width: 100%;" placeholder="Commentaire.." required></input>
                                </div>
                                <div class="controls">
                                    <input class="form-control" name="comment_date" rows="3" style="max-width: 100%;" placeholder="Date.." required readonly></input>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Modifier</button>
                        </form><br/>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- ./leftside -->

<script>
    function showEditForm(id) {
        var formContainer = document.getElementById('editFormContainer');
        var commentaireIdInput = formContainer.querySelector('input[name="comment_id"]');
        var articleIdInput = formContainer.querySelector('input[name="article_id"]');
        var pseudoIdInput = formContainer.querySelector('input[name="pseudo"]');
        var commentaireInput = formContainer.querySelector('input[name="comment_text"]');
        var dateIdInput = formContainer.querySelector('input[name="comment_date"]');

        // Remplir le formulaire avec les données du commentaire
        var row = document.querySelector('tr[data-commentaire-id="' + id + '"]');
        var comment_id = row.querySelector('td:nth-child(1)').innerText;
        var article_id = row.querySelector('td:nth-child(2)').innerText;
        var pseudo = row.querySelector('td:nth-child(3)').innerText;
        var comment_text = row.querySelector('td:nth-child(4)').innerText;
        var comment_date = row.querySelector('td:nth-child(5)').innerText;

        commentaireIdInput.value = comment_id;
        articleIdInput.value = article_id;
        pseudoIdInput.value = pseudo;
        commentaireInput.value = comment_text;
        dateIdInput.value = comment_date;

        // Afficher le formulaire d'édition et masquer le formulaire d'ajout
        formContainer.style.display = 'block';
        document.getElementById('addFormContainer').style.display = 'none';
    }
</script>