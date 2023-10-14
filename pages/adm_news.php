<style>
    .truncate-cell {
        max-width: 100px; /* Ajustez la largeur maximale en fonction de vos besoins */
        white-space: nowrap; /* Empêche le texte de passer à la ligne */
        overflow: hidden; /* Masque le contenu excédentaire */
        text-overflow: ellipsis; /* Ajoute des points de suspension (...) à la fin du texte tronqué */
    }
</style>
<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/

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

// Traitement de la modification d'une actualité
if (isset($_POST["editNews"]) && isset($_POST["newsId"]) && isset($_POST["title"]) && isset($_POST["content"]) && isset($_POST["img"]) && isset($_POST["visibilite"]) && isset($_POST["icon"])) {
    $newsId = $_POST["newsId"];
    $title = $_POST["title"];
    $content = $_POST["content"];
    $img = $_POST["img"];
    $visibilite = $_POST["visibilite"];
    $icon = $_POST["icon"];

    $query = $web->prepare("UPDATE `website_timeline_news` SET `title` = ?, `content` = ?, `img` = ?, `visibilite` = ?, `icon` = ? WHERE `id` = ?");
    $query->bindParam(1, $title);
    $query->bindParam(2, $content);
    $query->bindParam(3, $img);
    $query->bindParam(4, $visibilite);
    $query->bindParam(5, $icon);
    $query->bindParam(6, $newsId);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La nouvelle a été modifiée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_news";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}

// Traitement de l'ajout d'une nouvelle actualité
if (isset($_POST["action"]) && $_POST["action"] === "addNews" && isset($_POST["title"]) && isset($_POST["content"]) && isset($_POST["img"]) && isset($_POST["visibilite"]) && isset($_POST["icon"])) {
    $title = $_POST["title"];
    $content = $_POST["content"];
    $img = $_POST["img"];
    $visibilite = $_POST["visibilite"];
    $icon = $_POST["icon"];

    $query = $web->prepare("INSERT INTO `website_timeline_news` (title, content, visibilite, img, icon) VALUES (?, ?, ?, ?, ?)");
    $query->bindParam(1, $title);
    $query->bindParam(2, $content);
    $query->bindParam(3, $visibilite);
    $query->bindParam(4, $img);
    $query->bindParam(5, $icon);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La nouvelle a été ajoutée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_news";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}
?>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Panel des news</li>
    </ol>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="page-header margin-top-10"><h4>Gestion des actualités</h4></div>
                <div class="section section-default padding-25">
                    <?php
                    if (isset($_GET['wNews']) && is_numeric($_GET['wNews']) && isset($_GET['remove'])) {
                        $query = $web->prepare("DELETE FROM `website_timeline_news` WHERE `id` = ?;");
                        $query->bindParam(1, $_GET['wNews']);
                        $query->execute();
                        $query->closeCursor();
                        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La nouvelle a été supprimée avec succès !</div><br>';
                        echo '<script>setTimeout(function() {window.location.href = "?page=adm_news";}, 1000); // 1000 millisecondes = 1 secondes</script>';
                    }
                    ?>
                    <div class="box no-border-radius">
                        <table class="table table-striped no-margin">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Contenu</th>
                                    <th>Visible</th>
                                    <th>Img</th>
                                    <th>Icone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            session_start();
                           /* ini_set('display_errors', 1);
                            error_reporting(E_ALL);*/

                            // Calculer le nombre total de comptes dans votre tableau
                            $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_timeline_news`;");
                            $queryCount->execute();
                            $nombreDeNews = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                            // Nombre de comptes à afficher par page
                            $newsParPage = 5;

                            // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                            $moyenne = ceil($nombreDeNews / $newsParPage); // Nombre total de pages

                            // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                            $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                            if ($page < 1) {
                                $page = 1;
                            } elseif ($page > $moyenne) {
                                $page = $moyenne;
                            }

                            // Calculer l'index de début pour la requête en fonction de la page actuelle
                            $indexDebut = max(($page - 1), 0) * $newsParPage;

                            $query = $web->prepare("SELECT * FROM `website_timeline_news` ORDER BY `id` DESC LIMIT $indexDebut, $newsParPage;");
                            $query->execute();

                            ?>

                            <?php if ($query->rowCount() > 0) : ?>
                                <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <tr data-news-id="<?php echo $row['id']; ?>">
                                        <td class="truncate-cell"><?php
                                            if (isset($row['title'])) {
                                                echo $row['title'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['content'])) {
                                                echo $row['content'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['visibilite'])) {
                                                $visibilite = $row['visibilite'];
                                                $date = date_create($visibilite);
                                                $formattedDate = date_format($date, "d M, y H:i");

                                                echo $formattedDate;
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['img'])) {
                                                echo $row['img'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['icon'])) {
                                                echo $row['icon'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td>
                                            <span class="btn btn-info btn-outline btn-sm" data-toggle="tooltip" title="Modifier" onclick="showEditForm(<?php echo $row['id']; ?>)"><i class="ion-edit"></i></span>
                                            <a href="?page=adm_news&wNews=<?php echo $row['id']; ?>&remove"><span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="Supprimer"><i class="ion-trash-b"></i></span></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="6">
                                        <div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>
                                            <strong>Oh non!</strong> Il n'y a aucune news.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div><br>
                    <center>
                        <div class="btn-group">
                            <?php if ($page > 1) : ?>
                                <a href="?page=adm_news&num=1&newsParPage=<?php echo $newsParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="?page=adm_news&num=<?php echo ($page - 1); ?>&newsParPage=<?php echo $newsParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php endif; ?>
                            <?php
                            // Afficher le numéro de page 1
                            echo '<a href="?page=adm_news&num=1&newsParPage=' . $newsParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                            // Afficher "..." s'il y a plus de 4 pages avant la page courante
                            if ($page > 4) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher les numéros de page de manière groupée
                            for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                echo '<a href="?page=adm_news&num=' . $i . '&newsParPage=' . $newsParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                            }
                            // Afficher "..." s'il y a plus de 4 pages après la page courante
                            if ($page < $moyenne - 3) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher le numéro de la dernière page
                            echo '<a href="?page=adm_news&num=' . $moyenne . '&newsParPage=' . $newsParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                            ?>
                            <?php if ($page < $moyenne) : ?>
                                <a href="?page=adm_news&num=<?php echo ($page + 1); ?>&newsParPage=<?php echo $newsParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                <a href="?page=adm_news&num=<?php echo $moyenne; ?>&newsParPage=<?php echo $newsParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
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

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="no-border no-padding-top">
                <div class="section section-default padding-25">
                    <!-- Formulaire d'édition (masqué par défaut) -->
                    <div id="editFormContainer" style="display: none;">
                        <form id="editForm" method="post" action="?page=adm_news">
                            <!-- Ajouter les champs cachés pour la gestion de l'édition -->
                            <input type="hidden" name="editNews" value="1">
                            <input type="hidden" name="newsId">

                            <div class="col-md-12 col-xs-12 no-padding">
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="title" placeholder="Titre" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="img" placeholder="Image" value="./img/news/" required>
                                    </div>
                                    <div class="control-group col-md-12 no-padding">
                                        <div class="controls">
                                            <input type="datetime-local" class="form-control" name="visibilite" placeholder="Date et heure" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group col-md-12 no-padding col-xs-12 margin-top-15">
                                <div class="controls">
                                    <textarea class="form-control" name="content" rows="3" style="max-width: 100%;" placeholder="Votre contenu.." required></textarea>
                                </div>
                            </div>
                            <div class="controls">
                                <select class="form-control" name="icon" required>
                                    <option value="news" data-description="Jet parfait">News</option>
                                    <option value="warning" data-description="Pas parfait">Warning</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Modifier</button>
                        </form><br/>
                    </div>

                    <!-- Formulaire d'ajout de nouvelles actualités -->
                    <div id="addFormContainer" style="display: none;">
                        <form id="addForm" method="post" action="?page=adm_news">
                            <input type="hidden" name="action" value="addNews">
                            <div class="col-md-12 col-xs-12 no-padding">
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="title" placeholder="Titre" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="img" placeholder="Image" required>
                                    </div>
                                    <div class="controls">
                                        <?php
                                        // Obtenez la date et l'heure actuelles
                                        $currentDateTime = date('Y-m-d\TH:i');
                                        ?>
                                        <input type="datetime-local" class="form-control" name="visibilite" value="<?php echo $currentDateTime; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group col-md-12 no-padding col-xs-12 margin-top-15">
                                <div class="controls">
                                    <textarea class="form-control" name="content" rows="3" style="max-width: 100%;" placeholder="Votre contenu.." required></textarea>
                                </div>
                            </div>
                            <div class="controls">
                                <select class="form-control" name="icon" required>
                                    <option value="news" data-description="News">News</option>
                                    <option value="warning" data-description="Warning">Warning</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Envoyer</button>
                        </form><br/>
                    </div>

                    <!-- Condition pour afficher le bouton "Ajouter une nouvelle news" uniquement si le formulaire d'édition n'est pas actif -->
					<?php if (!$editFormActive) { ?>
						<div>
                            <center><button class="btn btn-primary" onclick="showAddForm()">Ajouter une news</button></center>
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
        var newsIdInput = formContainer.querySelector('input[name="newsId"]');
        var titleInput = formContainer.querySelector('input[name="title"]');
        var contentTextarea = formContainer.querySelector('textarea[name="content"]');
        var imgInput = formContainer.querySelector('input[name="img"]');
        var visibiliteInput = formContainer.querySelector('input[name="visibilite"]');
        var iconInput = formContainer.querySelector('select[name="icon"]');

        // Remplir le formulaire avec les données de la news
        var row = document.querySelector('tr[data-news-id="' + id + '"]');
        var title = row.querySelector('td:nth-child(1)').innerText;
        var content = row.querySelector('td:nth-child(2)').innerText;
        var img = row.querySelector('td:nth-child(4)').innerText;
        var visibilite = row.querySelector('td:nth-child(3)').innerText;
        var icon = row.querySelector('td:nth-child(5)').innerText;

        newsIdInput.value = id;
        titleInput.value = title;
        contentTextarea.value = content;
        imgInput.value = img;
        visibiliteInput.value = visibilite;
        iconInput.value = icon;

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
