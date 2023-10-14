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

// Traitement de la modification d'un domaine
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/

// Traitement de la modification d'un domaine

if (isset($_POST["editDomaine"]) && isset($_POST["domaineId"]) && isset($_POST["domaine"])) {
    $domaineId = $_POST["domaineId"];
    $domaine = $_POST["domaine"];

    $query = $web->prepare("UPDATE `website_register_domaine` SET `domaine` = ? WHERE `id` = ?");
    $web->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $query->bindParam(1, $domaine);
    $query->bindParam(2, $domaineId);

    if ($query->execute()) {
        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> Le domaine a été modifié avec succès !</div><br>';
        echo '<script>setTimeout(function() {window.location.href = "?page=adm_gestion_domaine";}, 1000); // 1000 millisecondes = 1 secondes</script>';
    } else {
        echo '<div class="alert alert-danger no-border-radius no-margin" role="alert"><strong>Oops !</strong> Une erreur s\'est produite lors de la modification du domaine.</div><br>';
    }
}

// Traitement de l'ajout d'un nouveau domaine
if (isset($_POST["action"]) && $_POST["action"] === "addDomaine" && isset($_POST["domaine"])) {
    $domaine = $_POST["domaine"];

    $query = $web->prepare("INSERT INTO `website_register_domaine` (domaine) VALUES (?)");
    $query->bindParam(1, $domaine);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> Le domaine a été ajoutée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_gestion_domaine";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}
?>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Panel des domaines</li>
    </ol>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="page-header margin-top-10"><h4>Gestion des domaines poubelle</h4></div>
                <div class="section section-default padding-25">
                    <?php
                    if (isset($_GET['wDomaine']) && is_numeric($_GET['wDomaine']) && isset($_GET['remove'])) {
                        $query = $web->prepare("DELETE FROM `website_register_domaine` WHERE `id` = ?;");
                        $query->bindParam(1, $_GET['wDomaine']);
                        $query->execute();
                        $query->closeCursor();
                        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> Le domaine a été supprimée avec succès !</div><br>';
                        echo '<script>setTimeout(function() {window.location.href = "?page=adm_gestion_domaine";}, 1000); // 1000 millisecondes = 1 secondes</script>';
                    }
                    ?>
                    <div class="box no-border-radius">
                        <table class="table table-striped no-margin">
                            <thead>
                            <tr>
                                <th>Domaine</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            session_start();
                            /* ini_set('display_errors', 1);
                             error_reporting(E_ALL);*/

                            // Calculer le nombre total de comptes dans votre tableau
                            $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_register_domaine`;");
                            $queryCount->execute();
                            $nombreDeDomaine = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                            // Nombre de comptes à afficher par page
                            $domaineParPage =10;

                            // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                            $moyenne = ceil($nombreDeDomaine / $domaineParPage); // Nombre total de pages

                            // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                            $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                            if ($page < 1) {
                                $page = 1;
                            } elseif ($page > $moyenne) {
                                $page = $moyenne;
                            }

                            // Calculer l'index de début pour la requête en fonction de la page actuelle
                            $indexDebut = max(($page - 1), 0) * $domaineParPage;

                            $query = $web->prepare("SELECT * FROM `website_register_domaine` ORDER BY `id` DESC LIMIT $indexDebut, $domaineParPage;");
                            $query->execute();

                            ?>

                            <?php if ($query->rowCount() > 0) : ?>
                                <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <tr data-domaine-id="<?php echo $row['id']; ?>">
                                        <td><?php
                                            if (isset($row['domaine'])) {
                                                echo $row['domaine'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td>
                                            <span class="btn btn-info btn-outline btn-sm" data-toggle="tooltip" title="Modifier" onclick="showEditForm(<?php echo $row['id']; ?>)"><i class="ion-edit"></i></span>
                                            <a href="?page=adm_gestion_domaine&wDomaine=<?php echo $row['id']; ?>&remove"><span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="Supprimer"><i class="ion-trash-b"></i></span></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6">
                                        <div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>
                                            <strong>Oh non!</strong> Il n'y a aucun domaine poubelle.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div><br>
                    <center>
                        <div class="btn-group">
                            <a href="?page=adm_gestion_domaine&num=1&domaineParPage=<?php echo $domaineParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                            <?php if ($page > 1) : ?>
                                <a href="?page=adm_gestion_domaine&num=<?php echo ($page - 1); ?>&domaineParPage=<?php echo $domaineParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php endif; ?>
                            <?php
                            // Afficher le numéro de page 1
                            echo '<a href="?page=adm_gestion_domaine&num=1&domaineParPage=' . $domaineParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                            // Afficher les points de suspension s'il y a plus de 4 pages avant la page courante
                            if ($page > 4) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher les numéros de page de manière groupée
                            for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                echo '<a href="?page=adm_gestion_domaine&num=' . $i . '&domaineParPage=' . $domaineParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                            }
                            // Afficher les points de suspension s'il y a plus de 4 pages après la page courante
                            if ($page < $moyenne - 3) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher le numéro de la dernière page
                            echo '<a href="?page=adm_gestion_domaine&num=' . $moyenne . '&domaineParPage=' . $domaineParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                            ?>
                            <?php if ($page < $moyenne) : ?>
                                <a href="?page=adm_gestion_domaine&num=<?php echo ($page + 1); ?>&domaineParPage=<?php echo $domaineParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                            <?php endif; ?>
                            <a href="?page=adm_gestion_domaine&num=<?php echo $moyenne; ?>&domaineParPage=<?php echo $domaineParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
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
                        <form id="editForm" method="post" action="?page=adm_gestion_domaine">
                            <!-- Ajouter les champs cachés pour la gestion de l'édition -->
                            <input type="hidden" name="editDomaine" value="1">
                            <input type="hidden" name="domaineId">
                            <div class="control-group col-md-12 no-padding col-xs-12 margin-top-15">
                                <div class="controls">
                                    <textarea class="form-control" name="domaine" rows="3" style="max-width: 100%;" placeholder="Domaine.." required></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Modifier</button>
                        </form><br/>
                    </div>

                    <!-- Formulaire d'ajout de nouvelles actualités -->
                    <div id="addFormContainer" style="display: none;">
                        <form id="addForm" method="post" action="?page=adm_gestion_domaine">
                            <input type="hidden" name="action" value="addDomaine">
                            <div class="control-group col-md-12 no-padding col-xs-12 margin-top-15">
                                <div class="controls">
                                    <textarea class="form-control" name="domaine" rows="3" style="max-width: 100%;" placeholder="Domaine.." required></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Envoyer</button>
                        </form><br/>
                    </div>

                    <!-- Condition pour afficher le bouton "Ajouter une nouvelle news" uniquement si le formulaire d'édition n'est pas actif -->
                    <?php if (!$editFormActive) { ?>
                        <div>
                            <center><button class="btn btn-primary" onclick="showAddForm()">Ajouter un domaine</button></center>
                        </div>
                    <?php } ?>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- ./leftside -->

<script>
    function showEditForm(id) {
        var formContainer = document.getElementById('editFormContainer');
        var domaineIdInput = formContainer.querySelector('input[name="domaineId"]');
        var domaineTextarea = formContainer.querySelector('textarea[name="domaine"]');

        // Remplir le formulaire avec les données de la news
        var row = document.querySelector('tr[data-domaine-id="' + id + '"]');
        var domaine = row.querySelector('td:nth-child(1)').innerText;

        domaineIdInput.value = id;
        domaineTextarea.value = domaine;

        // Afficher le formulaire d'édition et masquer le formulaire d'ajout
        formContainer.style.display = 'block';
        document.getElementById('addFormContainer').style.display = 'none';
    }

    function showAddForm() {
        var formContainer = document.getElementById('addFormContainer');
        var editFormContainer = document.getElementById('editFormContainer');

        // Vider les champs du formulaire d'édition
        var inputs = editFormContainer.querySelectorAll('input, textarea');
        for (var i = 0; i < inputs.length; i++) {
            inputs[i].value = '';
        }

        // Masquer le formulaire d'édition et afficher le formulaire d'ajout
        editFormContainer.style.display = 'none';
        formContainer.style.display = 'block';
    }
</script>

