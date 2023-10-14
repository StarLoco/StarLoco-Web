<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/

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
if (isset($_POST["editCategorie"]) && isset($_POST["categorieId"]) && isset($_POST["name"]) && isset($_POST["active"])) {
    $categorieId = $_POST["categorieId"];
    $name = $_POST["name"];
    $active = $_POST["active"];

    $query = $web->prepare("UPDATE `website_shop_categories` SET `name` = ?, `active` = ? WHERE `id` = ?");
    $query->bindParam(1, $name);
    $query->bindParam(2, $active);
    $query->bindParam(3, $categorieId);
    if ($query->execute()) {
        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La catégorie a été modifié avec succès !</div><br>';
        echo '<script>setTimeout(function() {window.location.href = "?page=adm_categorie_boutique";}, 1000); // 1000 millisecondes = 1 secondes</script>';
    } else {
        echo '<div class="alert alert-danger no-border-radius no-margin" role="alert"><strong>Oops !</strong> Une erreur s\'est produite lors de la modification du domaine.</div><br>';
    }
}

// Traitement de l'ajout d'une nouvelle actualité
if (isset($_POST["action"]) && $_POST["action"] === "addCategorie" && isset($_POST["id"]) && isset($_POST["name"]) && isset($_POST["active"])) {
    $id = $_POST["id"];
    $name = $_POST["name"];
    $active = $_POST["active"];

    $query = $web->prepare("INSERT INTO `website_shop_categories` (id, name, active) VALUES (?, ?, ?)");
    $query->bindParam(1, $id);
    $query->bindParam(2, $name);
    $query->bindParam(3, $active);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La catégorie a été ajoutée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_categorie_boutique";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}
?>

    <div class="leftside">
        <ol class="breadcrumb">
            <li><a href="?page=adm_administration">Administration</a></li>
            <li class="active">Panel des catégories boutique</li>
        </ol>

        <div class="row">
            <div class="col-md-12 col-xs-12">
                <section class="section section-white no-border no-padding-top">
                    <div class="page-header margin-top-10"><h4>Gestion des catégories</h4></div>
                    <div class="section section-default padding-25">
                        <?php
                        if (isset($_GET['wCategorie']) && is_numeric($_GET['wCategorie']) && isset($_GET['remove'])) {
                            $query = $web->prepare("DELETE FROM `website_shop_categories` WHERE `id` = ?;");
                            $query->bindParam(1, $_GET['wCategorie']);
                            $query->execute();
                            $query->closeCursor();
                            echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La catégorie a été supprimée avec succès !</div><br>';
                        }
                        ?>
                        <div class="box no-border-radius">
                            <table class="table table-striped no-margin">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Visible</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                session_start();
                                /* ini_set('display_errors', 1);
                                 error_reporting(E_ALL);*/

                                // Calculer le nombre total de comptes dans votre tableau
                                $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_shop_categories`;");
                                $queryCount->execute();
                                $nombreDeCategorie = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                                // Nombre de comptes à afficher par page
                                $categorieParPage = 10;

                                // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                                $moyenne = ceil($nombreDeCategorie / $categorieParPage); // Nombre total de pages

                                // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                                $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                                if ($page < 1) {
                                    $page = 1;
                                } elseif ($page > $moyenne) {
                                    $page = $moyenne;
                                }

                                // Calculer l'index de début pour la requête en fonction de la page actuelle
                                $indexDebut = max(($page - 1), 0) * $categorieParPage;

                                $query = $web->prepare("SELECT * FROM `website_shop_categories` ORDER BY `id` ASC LIMIT $indexDebut, $categorieParPage;");
                                $query->execute();

                                ?>

                                <?php if ($query->rowCount() > 0) : ?>
                                    <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                        <tr data-categorie-id="<?php echo $row['id']; ?>">
                                            <td><?php
                                                if (isset($row['id'])) {
                                                    echo $row['id'];
                                                } else {
                                                    echo "Non disponible";
                                                }
                                                ?></td>
                                            <td><?php
                                                if (isset($row['name'])) {
                                                    echo $row['name'];
                                                } else {
                                                    echo "Non disponible";
                                                }
                                                ?></td>
                                            <td><?php
                                                if (isset($row['active'])) {
                                                    echo $row['active'];
                                                } else {
                                                    echo "Non disponible";
                                                }
                                                ?></td>
                                            <td>
                                                <span class="btn btn-info btn-outline btn-sm" data-toggle="tooltip" title="Modifier" onclick="showEditForm(<?php echo $row['id']; ?>)"><i class="ion-edit"></i></span>
                                                <a href="?page=adm_categorie_boutique&wCategorie=<?php echo $row['id']; ?>&remove"><span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="Supprimer"><i class="ion-trash-b"></i></span></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>
                                                <strong>Oh non!</strong> Il n'y a aucune catégorie dans la boutique.
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
                                    <a href="?page=adm_categorie_boutique&num=1&categorieParPage=<?php echo $categorieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                    <a href="?page=adm_categorie_boutique&num=<?php echo ($page - 1); ?>&categorieParPage=<?php echo $categorieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                                <?php else : ?>
                                    <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                    <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                                <?php endif; ?>
                                <?php
                                // Afficher le numéro de page 1
                                echo '<a href="?page=adm_categorie_boutique&num=1&categorieParPage=' . $categorieParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                                // Afficher "..." s'il y a plus de 4 pages avant la page courante
                                if ($page > 4) {
                                    echo '<span class="btn btn-sm btn-default">...</span>';
                                }
                                // Afficher les numéros de page de manière groupée
                                for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                    echo '<a href="?page=adm_categorie_boutique&num=' . $i . '&categorieParPage=' . $categorieParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                                }
                                // Afficher "..." s'il y a plus de 4 pages après la page courante
                                if ($page < $moyenne - 3) {
                                    echo '<span class="btn btn-sm btn-default">...</span>';
                                }
                                // Afficher le numéro de la dernière page
                                echo '<a href="?page=adm_categorie_boutique&num=' . $moyenne . '&categorieParPage=' . $categorieParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                                ?>
                                <?php if ($page < $moyenne) : ?>
                                    <a href="?page=adm_categorie_boutique&num=<?php echo ($page + 1); ?>&categorieParPage=<?php echo $categorieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                    <a href="?page=adm_categorie_boutique&num=<?php echo $moyenne; ?>&categorieParPage=<?php echo $categorieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
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
                            <form id="editForm" method="post" action="?page=adm_categorie_boutique">
                                <!-- Ajouter les champs cachés pour la gestion de l'édition -->
                                <input type="hidden" name="editCategorie" value="1">
                                <input type="hidden" name="categorieId">

                                <div class="col-md-12 col-xs-12 no-padding">
                                    <div class="control-group col-md-12 no-padding">
                                        <div class="controls">
                                            <input type="text" class="form-control" name="id" placeholder="ID" required>
                                        </div>
                                        <div class="controls">
                                            <input type="text" class="form-control" name="name" placeholder="Nom" required>
                                        </div>
                                        <div class="controls">
                                            <input type="text" class="form-control" name="active" placeholder="Active" required>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Modifier</button>
                            </form><br/>
                        </div>

                        <!-- Formulaire d'ajout de nouvelles actualités -->
                        <div id="addFormContainer" style="display: none;">
                            <form id="addForm" method="post" action="?page=adm_categorie_boutique">
                                <input type="hidden" name="action" value="addCategorie">
                                <div class="col-md-12 col-xs-12 no-padding">
                                    <div class="control-group col-md-12 no-padding">
                                        <div class="controls">
                                            <input type="text" class="form-control" name="id" placeholder="ID" required>
                                        </div>
                                        <div class="controls">
                                            <input type="text" class="form-control" name="name" placeholder="Name" required>
                                        </div>
                                        <div class="controls">
                                            <select class="form-control" name="active" required>
                                                <option value="1" data-description="Visible">Visible</option>
                                                <option value="0" data-description="Non visible">Non visible</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Envoyer</button>
                            </form><br/>
                        </div>
                        <!-- Condition pour afficher le bouton "Ajouter une nouvelle news" uniquement si le formulaire d'édition n'est pas actif -->
                        <?php if (!$editFormActive) { ?>
                            <div>
                                <center><button class="btn btn-primary" onclick="showAddForm()">Ajouter une catégorie</button></center>
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
            var categorieIdInput = formContainer.querySelector('input[name="categorieId"]');
            var idInput = formContainer.querySelector('input[name="id"]');
            var nameInput = formContainer.querySelector('input[name="name"]');
            var activeInput = formContainer.querySelector('input[name="active"]');

            // Remplir le formulaire avec les données de la catégorie
            var row = document.querySelector('tr[data-categorie-id="' + id + '"]');
            var idValue = row.querySelector('td:nth-child(1)').innerText;
            var nameValue = row.querySelector('td:nth-child(2)').innerText;
            var activeValue = row.querySelector('td:nth-child(3)').innerText;

            categorieIdInput.value = id;
            idInput.value = idValue;
            nameInput.value = nameValue;
            activeInput.value = activeValue;

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
