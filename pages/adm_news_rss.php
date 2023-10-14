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

// Traitement de l'ajout ou de la modification d'une actualité
if (isset($_POST["gameNews"]) && isset($_POST["title"]) && isset($_POST["content"]) && isset($_POST["type"]) && isset($_POST["link"]) && isset($_POST["date"])) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $type = $_POST["type"];
    $link = $_POST["link"];
    $date = $_POST["date"];

    if (!empty($title) && !empty($content) && !empty($type) && !empty($link) && !empty($date)) {
        if (isset($_POST["editNewsId"]) && is_numeric($_POST["editNewsId"])) {
            // Modification d'une actualité existante
            $newsId = $_POST["editNewsId"];
            $query = $web->prepare("UPDATE `client_rss_news` SET `title` = ?, `content` = ?, `icon` = ?, `link` = ?, `date` = ? WHERE `id` = ?");
            if (!$query) {
                die('Erreur lors de la préparation de la requête d\'édition : ' . print_r($web->errorInfo(), true));
            }
            $query->bindParam(1, $title);
            $query->bindParam(2, $content);
            $query->bindParam(3, $type);
            $query->bindParam(4, $link);
            $query->bindParam(5, $date);
            $query->bindParam(6, $newsId);
            if (!$query->execute()) {
                die('Erreur lors de l\'exécution de la requête d\'édition : ' . print_r($query->errorInfo(), true));
            }
            echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La news RSS a été modifiée avec succès !</div><br>';
            echo '<script>setTimeout(function() {window.location.href = "?page=adm_news_rss";}, 1000); // 1000 millisecondes = 1 secondes</script>';
        } else {
            // Ajout d'une nouvelle actualité
            $query = $web->prepare("INSERT INTO `client_rss_news` (`title`, `content`, `icon`, `link`, `date`) VALUES (?, ?, ?, ?, ?)");
            if (!$query) {
                die('Erreur lors de la préparation de la requête d\'ajout : ' . print_r($web->errorInfo(), true));
            }
            $query->bindParam(1, $title);
            $query->bindParam(2, $content);
            $query->bindParam(3, $type);
            $query->bindParam(4, $link);
            $query->bindParam(5, $date);
            if (!$query->execute()) {
                die('Erreur lors de l\'exécution de la requête d\'ajout : ' . print_r($query->errorInfo(), true));
            }
            echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La news RSS a été ajoutée avec succès !</div><br>';
            echo '<script>setTimeout(function() {window.location.href = "?page=adm_news_rss";}, 1000); // 1000 millisecondes = 1 secondes</script>';
        }
    } else {
        die('Tous les champs sont obligatoires pour ajouter ou modifier une nouvelle.');
    }
}

// Traitement de la suppression d'une actualité
if (isset($_GET['gNews']) && is_numeric($_GET['gNews']) && isset($_GET['remove'])) {
    $query = $web->prepare("DELETE FROM `client_rss_news` WHERE `id` = ?;");
    $query->bindParam(1, $_GET['gNews']);
    if (!$query->execute()) {
        die('Erreur lors de la suppression de la nouvelle : ' . print_r($query->errorInfo(), true));
    }
    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La news RSS a été supprimée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_news_rss";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}
?>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Panel des news RSS</li>
    </ol>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="page-header margin-top-10">
                    <h4>Gestions des game news</h4>
                </div>
                <div class="section section-default padding-25">
                    <?php
                    $query = $web->prepare("SELECT * FROM `client_rss_news` ORDER BY `id` DESC;");
                    $query->execute();
                    $newsCount = $query->rowCount();
                    ?>

                    <?php if ($newsCount > 0) : ?>
                        <!-- Afficher la liste des news RSS -->
                        <div class="box no-border-radius">
                            <table class="table table-striped no-margin">
                                <thead>
                                <tr>
                                    <th class="padding-left-15">#</th>
                                    <th>Titre</th>
                                    <th>Contenu</th>
                                    <th>Date</th>
                                    <th>Icone</th>
                                    <th>Link</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                while ($row = $query->fetch()) { ?>
                                    <tr data-id="<?php echo $row['id']; ?>">
                                        <td class="padding-left-15"><?php echo $row['id']; ?></td>
                                        <td><?php
                                            if (isset($row['title'])) {
                                                echo $row['title'];
                                            } else {
                                                echo "Titre non disponible";
                                            }
                                            ?></td>
                                        <td>
                                            <?php
                                            if (isset($row['content'])) {
                                                $content = $row['content'];
                                                $shortContent = substr($content, 0, 15);
                                                if (strlen($content) > 15) {
                                                    $shortContent .= "...";
                                                }
                                                echo $shortContent;
                                            } else {
                                                echo "Contenu non disponible";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($row['date'])) {
                                                $date = date('d M y, H:i', strtotime($row['date']));
                                                echo $date;
                                            } else {
                                                echo "Date non disponible";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            switch ($row['icon']) {
                                                case "News":
                                                    echo "Nouvelle";
                                                    break;
                                                case "Event":
                                                    echo "Evénement";
                                                    break;
                                                case "Update_fr":
                                                    echo "Mise à jour";
                                                    break;
                                                case "Maintenance":
                                                    echo "Maintenance";
                                                    break;
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (isset($row['link'])) {
                                                echo $row['link'];
                                            } else {
                                                echo "Lien non disponible";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <span class="btn btn-info btn-outline btn-sm" data-toggle="tooltip" title="Modifier" onclick="showEditForm(<?php echo $row['id']; ?>)"><i class="ion-edit"></i></span><a href="?page=adm_news_rss&gNews=<?php echo $row['id']; ?>&remove"><span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="Supprimer"><i class="ion-trash-b"></i></span></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else : ?>
                        <!-- Afficher le message s'il n'y a pas de news RSS -->
                        <center><div class="alert alert-info no-border-radius no-margin" role="alert">
                            Aucune news RSS disponible pour le moment.
                            </div></center>
                    <?php endif; ?>

                    <?php
                    $query->closeCursor();
                    ?>
                </div>
            </section>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="no-border no-padding-top">
                <div class="section section-default padding-25">
                    <div id="editFormContainer" style="display: none;">
                        <form method="post" action="?page=adm_news_rss">
                            <input type="hidden" name="editNewsId" id="editNewsId" value="">
                            <div class="col-md-12 col-xs-12 no-padding">
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="title" id="title" placeholder="Titre" required>
                                    </div>
                                </div>
                                <div class="control-group col-md-12 no-padding col-xs-12 margin-top-15">
                                    <div class="controls">
                                        <textarea class="form-control" name="content" id="content" rows="3" style="max-width: 100%;" placeholder="Votre contenu.." required></textarea>
                                    </div>
                                </div>
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="link" id="link" placeholder="Url" required>
                                    </div>
                                </div>
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="datetime-local" class="form-control" name="date" id="date" placeholder="Date et heure" required>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group col-md-12 no-padding col-xs-12 margin-top-15">
                                <div class="controls">
                                    <select name="type" id="type" class="form-control">
                                        <option disabled selected>Intitulé</option>
                                        <option value="News">Nouvelle</option>
                                        <option value="Event">Evénement</option>
                                        <option value="Update_fr">Mise à jour</option>
                                        <option value="Maintenance">Maintenance</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" name="gameNews" style="width:100%;">Envoyer</button>
                        </form>
                    </div>
                    <br/>
                    <center><button class="btn btn-primary" onclick="showAddForm()">Ajouter une news RSS</button></center>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    function showAddForm() {
        var formContainer = document.getElementById('editFormContainer');
        var submitButton = formContainer.querySelector('button[type="submit"]');

        formContainer.style.display = 'block';
        document.getElementById('editNewsId').value = '';
        document.getElementById('title').value = '';
        document.getElementById('content').value = '';
        document.getElementById('link').value = '';
        document.getElementById('date').value = getCurrentDateTime(); // Mettre la date et l'heure actuelles
        document.getElementById('type').value = '';

        submitButton.innerText = 'Envoyer';
    }


    function showEditForm(id) {
        var formContainer = document.getElementById('editFormContainer');
        var submitButton = formContainer.querySelector('button[type="submit"]');
        var editNewsIdInput = formContainer.querySelector('input[name="editNewsId"]');
        var titleInput = formContainer.querySelector('input[name="title"]');
        var contentTextarea = formContainer.querySelector('textarea[name="content"]');
        var linkInput = formContainer.querySelector('input[name="link"]');
        var dateInput = formContainer.querySelector('input[name="date"]');
        var typeSelect = formContainer.querySelector('select[name="type"]');

        // Remplir le formulaire avec les données de la news
        var row = document.querySelector('tr[data-id="' + id + '"]');
        var title = row.querySelector('td:nth-child(2)').innerText;
        var content = row.querySelector('td:nth-child(3)').innerText;
        var link = row.querySelector('td:nth-child(6)').innerText;
        var date = row.querySelector('td:nth-child(4)').innerText;
        var type = row.querySelector('td:nth-child(5)').innerText;

        editNewsIdInput.value = id;
        titleInput.value = title;
        contentTextarea.value = content;
        linkInput.value = link;
        dateInput.value = formatDateForInput(date);
        typeSelect.value = getTypeValue(type);

        // Afficher le formulaire d'édition et masquer le formulaire d'ajout
        formContainer.style.display = 'block';
        submitButton.innerText = 'Modifier';
    }

    function getCurrentDateTime() {
        var now = new Date();
        var year = now.getFullYear();
        var month = (now.getMonth() + 1).toString().padStart(2, '0');
        var day = now.getDate().toString().padStart(2, '0');
        var hours = now.getHours().toString().padStart(2, '0');
        var minutes = now.getMinutes().toString().padStart(2, '0');

        return year + '-' + month + '-' + day + 'T' + hours + ':' + minutes;
    }


    function formatDateForInput(dateString) {
        // Convertir la date au format adapté pour l'input 'datetime-local'
        var date = new Date(dateString);
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var formattedDate = date.getFullYear() + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day + 'T' + (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
        return formattedDate;
    }

    function getTypeValue(typeString) {
        switch (typeString) {
            case "Nouvelle":
                return "News";
            case "Evénement":
                return "Event";
            case "Mise à jour":
                return "Update_fr";
            case "Maintenance":
                return "Maintenance";
            default:
                return "";
        }
    }
</script>
