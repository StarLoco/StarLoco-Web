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

// Traitement de la modification d'un objet dans la boutique
if (isset($_POST["editBoutique"]) && isset($_POST["boutiqueId"]) && isset($_POST["name"]) && isset($_POST["template"]) && isset($_POST["jp"]) && isset($_POST["price"]) && isset($_POST["category"]) && isset($_POST["server"]) && isset($_POST["active"]) && isset($_POST["img"])) {
    $boutiqueId = $_POST["boutiqueId"];
    $name = $_POST["name"];
    $template = $_POST["template"];
    $jp = $_POST["jp"];
    $price = $_POST["price"];
    $category = $_POST["category"];
    $server = $_POST["server"];
    $active = $_POST["active"];
    $img = $_POST["img"];

    $query = $web->prepare("UPDATE `website_shop_objects` SET `name` = ?, `template` = ?, `jp` = ?, `price` = ?, `category` = ?, `server` = ?, `active` = ?, `img` = ? WHERE `id` = ?");
    $query->bindParam(1, $name);
    $query->bindParam(2, $template);
    $query->bindParam(3, $jp);
    $query->bindParam(4, $price);
    $query->bindParam(5, $category);
    $query->bindParam(6, $server);
    $query->bindParam(7, $active);
    $query->bindParam(8, $img);
    $query->bindParam(9, $boutiqueId);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> L\'objet de la boutique a été modifiée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_boutique";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}

// Traitement de l'ajout d'un objet de la boutique
if (isset($_POST["action"]) && $_POST["action"] === "addBoutique" && isset($_POST["name"]) && isset($_POST["template"]) && isset($_POST["jp"]) && isset($_POST["price"]) && isset($_POST["category"]) && isset($_POST["server"]) && isset($_POST["active"]) && isset($_POST["img"])) {
    $name = $_POST["name"];
    $template = $_POST["template"];
    $jp = $_POST["jp"];
    $price = $_POST["price"];
    $category = $_POST["category"];
    $server = $_POST["server"];
    $active = $_POST["active"];
    $img = $_POST["img"];

    $query = $web->prepare("INSERT INTO `website_shop_objects` (name, template, jp, price, category, server, active, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bindParam(1, $name);
    $query->bindParam(2, $template);
    $query->bindParam(3, $jp);
    $query->bindParam(4, $price);
    $query->bindParam(5, $category);
    $query->bindParam(6, $server);
    $query->bindParam(7, $active);
    $query->bindParam(8, $img);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> L\'objet de la boutique a été ajoutée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_boutique";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}
?>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Panel de la boutique</li>
    </ol>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="page-header margin-top-10"><h4>Gestion de la boutique</h4></div>
                <div class="section section-default padding-25">
                    <?php
                    if (isset($_GET['wBoutique']) && is_numeric($_GET['wBoutique']) && isset($_GET['remove'])) {
                        $query = $web->prepare("DELETE FROM `website_shop_objects` WHERE `id` = ?;");
                        $query->bindParam(1, $_GET['wBoutique']);
                        $query->execute();
                        $query->closeCursor();
                        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> L\'objet de la boutique a été supprimée avec succès !</div><br>';
                        echo '<script>setTimeout(function() {window.location.href = "?page=adm_boutique";}, 1000); // 1000 millisecondes = 1 secondes</script>';
                    }
                    ?>
                    <div class="box no-border-radius">
                        <table class="table table-striped no-margin">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Objet</th>
                                <th>JP</th>
                                <th>Prix</th>
                                <th>Catégorie</th>
                                <th>server</th>
                                <th>Visible</th>
                                <th>Img</th>
                                <th>Reduc</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            session_start();
                            // Calculer le nombre total de objet dans votre tableau
                            $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_shop_objects`;");
                            $queryCount->execute();
                            $nombreDeBoutique = $queryCount->fetchColumn(); // Le nombre total d'objet dans votre tableau

                            // Nombre d'objet à afficher par page
                            $boutiqueParPage = 10;

                            // Calculer le nombre total de pages en fonction du nombre d'objet et de la limite par page
                            $moyenne = ceil($nombreDeBoutique / $boutiqueParPage); // Nombre total de pages

                            // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                            $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                            if ($page < 1) {
                                $page = 1;
                            } elseif ($page > $moyenne) {
                                $page = $moyenne;
                            }

                            // Calculer l'index de début pour la requête en fonction de la page actuelle
                            $indexDebut = max(($page - 1), 0) * $boutiqueParPage;

                            $query = $web->prepare("SELECT * FROM `website_shop_objects` ORDER BY `id` ASC LIMIT $indexDebut, $boutiqueParPage;");
                            $query->execute();

                            ?>
                            <?php if ($query->rowCount() > 0) : ?>
                                <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <tr data-boutique-id="<?php echo $row['id']; ?>">
                                        <td class="truncate-cell"><?php
                                            if (isset($row['name'])) {
                                                echo $row['name'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['template'])) {
                                                echo $row['template'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['jp'])) {
                                                echo $row['jp'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['price'])) {
                                                echo $row['price'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['category'])) {
                                                echo $row['category'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td><?php
                                            if (isset($row['server'])) {
                                                echo $row['server'];
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
                                        <td><?php
                                            if (isset($row['img'])) {
                                                echo $row['img'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['reduc'])) {
                                                echo $row['reduc'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td>
                                            <span class="btn btn-info btn-outline btn-sm" data-toggle="tooltip" title="Modifier" onclick="showEditForm(<?php echo $row['id']; ?>)"><i class="ion-edit"></i></span>
                                            <a href="?page=adm_boutique&wBoutique=<?php echo $row['id']; ?>&remove"><span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="Supprimer"><i class="ion-trash-b"></i></span></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="9">
                                        <div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>
                                            <strong>Oh non!</strong> Il n'y a aucun objet dans la boutique.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div><br>
                    <center>
                        <div class="btn-group">
                            <a href=<?php if ($page - 1 > 0) echo "?page=adm_boutique&num=" . ($page - 1) . "&boutiqueParPage=" . $boutiqueParPage; else echo "#"; ?> class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <div class="btn btn-sm btn-default"><?php echo $page . " / " . $moyenne; ?></div>
                            <a href=<?php if ($page + 1 <= $moyenne) echo "?page=adm_boutique&num=" . ($page + 1) . "&boutiqueParPage=" . $boutiqueParPage; else echo "#"; ?> class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
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
                        <form id="editForm" method="post" action="?page=adm_boutique">
                            <!-- Ajouter les champs cachés pour la gestion de l'édition -->
                            <input type="hidden" name="editBoutique" value="1">
                            <input type="hidden" name="boutiqueId">

                            <div class="col-md-12 col-xs-12 no-padding">
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="name" placeholder="Nom" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="template" placeholder="Objet" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="jp" placeholder="Jet Parfait O ou 1" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="price" placeholder="Prix" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="category" placeholder="Categorie" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="server" placeholder="Serveur" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="active" placeholder="Visible" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="img" placeholder="Image" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Modifier</button>
                        </form><br/>
                    </div>

                    <!-- Formulaire d'ajout de nouveau objet -->
                    <div id="addFormContainer" style="display: none;">
                        <form id="addForm" method="post" action="?page=adm_boutique">
                            <input type="hidden" name="action" value="addBoutique">
                            <div class="col-md-12 col-xs-12 no-padding">
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="name" placeholder="Nom" required>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="template" placeholder="Objet" required>
                                    </div>
                                    <div class="controls">
                                        <select class="form-control" name="jp" required>
                                            <option value="1" data-description="Jet parfait">Jet parfait</option>
                                            <option value="0" data-description="Pas parfait">Pas parfait</option>
                                        </select>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="price" placeholder="Prix" required>
                                    </div>
                                    <div class="controls">
                                        <select class="form-control" name="category" required>
                                            <option value="">Catégorie:</option>
                                            <option value="1">Amulette</option>
                                            <option value="2">Arc</option>
                                            <option value="3">Baguette</option>
                                            <option value="4">Bâton</option>
                                            <option value="5">Dague</option>
                                            <option value="6">Epée</option>
                                            <option value="7">Marteau</option>
                                            <option value="8">Pelles</option>
                                            <option value="9">Anneau</option>
                                            <option value="10">Ceinture</option>
                                            <option value="11">Botte</option>
                                            <option value="12">Potion</option>
                                            <option value="16">Chapeau</option>
                                            <option value="17">Cape</option>
                                            <option value="18">Familier</option>
                                            <option value="19">Hache</option>
                                            <option value="20">Outil</option>
                                            <option value="21">Pioche</option>
                                            <option value="22">Faux</option>
                                            <option value="23">Dofus</option>
                                            <option value="26">Potion de forgemagie</option>
                                            <option value="50">Guildalogemme</option>
                                            <option value="75">Parchemin de sort</option>
                                            <option value="76">Parchemin de caractéristique</option>
                                            <option value="81">Sac à dos</option>
                                            <option value="82">Bouclier</option>
                                            <option value="83">Pierre d'âme</option>
                                            <option value="89">Autres & Pack Parchemin de caractéristique</option>
                                            <option value="97">Dragodindes</option>
                                            <option value="102">Arbalète</option>
                                            <option value="112">Prisme</option>
                                            <option value="113">Obvijevans</option>
                                            <option value="114">Tourmenteurs</option>
                                            <option value="115">Coffres</option>
                                        </select>
                                    </div>
                                    <div class="controls">
                                        <select class="form-control" name="server" required>
                                            <option value="">Serveur:</option>
                                            <option value="636">Osiris</option>
                                        </select>
                                    </div>
                                    <div class="controls">
                                        <select class="form-control" name="active" required>
                                            <option value="1" data-description="Visible">Visible</option>
                                            <option value="0" data-description="Non visible">Non visible</option>
                                        </select>
                                    </div>
                                    <div class="controls">
                                        <input type="text" class="form-control" name="img" placeholder="Image" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Envoyer</button>
                        </form><br/>
                    </div>

                    <!-- Condition pour afficher le bouton "Ajouter une nouveau objet" uniquement si le formulaire d'édition n'est pas actif -->
                    <?php if (!$editFormActive) { ?>
                        <div>
                            <center><button class="btn btn-primary" onclick="showAddForm()">Ajouter un objet dans la boutique</button></center>
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
        var boutiqueIdInput = formContainer.querySelector('input[name="boutiqueId"]');
        var nameInput = formContainer.querySelector('input[name="name"]');
        var templateInput = formContainer.querySelector('input[name="template"]');
        var jpInput = formContainer.querySelector('input[name="jp"]');
        var priceInput = formContainer.querySelector('input[name="price"]');
        var categoryInput = formContainer.querySelector('input[name="category"]');
        var serverInput = formContainer.querySelector('input[name="server"]');
        var activeInput = formContainer.querySelector('input[name="active"]');
        var imgInput = formContainer.querySelector('input[name="img"]');

        // Remplir le formulaire avec les données de la boutique
        var row = document.querySelector('tr[data-boutique-id="' + id + '"]');
        var name = row.querySelector('td:nth-child(1)').innerText;
        var template = row.querySelector('td:nth-child(2)').innerText;
        var jp = row.querySelector('td:nth-child(3)').innerText;
        var price = row.querySelector('td:nth-child(4)').innerText;
        var category = row.querySelector('td:nth-child(5)').innerText;
        var server = row.querySelector('td:nth-child(6)').innerText;
        var active = row.querySelector('td:nth-child(7)').innerText;
        var img = row.querySelector('td:nth-child(8)').innerText;

        boutiqueIdInput.value = id;
        nameInput.value = name;
        templateInput.value = template;
        jpInput.value = jp;
        priceInput.value = price;
        categoryInput.value = category;
        serverInput.value = server;
        activeInput.value = active;
        imgInput.value = img;

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
