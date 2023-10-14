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

    // Assurez-vous d'utiliser la variable de connexion correctement ici
    $insertQuery = $web->prepare("INSERT INTO website_logs (guid, account, date, page) VALUES (:guid, :account, :date, :page)");

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

function generateRandomCode() {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $code = '';
    for ($i = 0; $i < 10; $i++) {
        $randomIndex = rand(0, strlen($characters) - 1);
        $code .= $characters[$randomIndex];
    }
    return $code;
}

// Générer le code aléatoire
$codeAleatoire = generateRandomCode();
// Traitement de la modification de la loterie
if (isset($_POST["editLoterie"]) && isset($_POST["loterieId"]) && isset($_POST["name"]) && isset($_POST["gain"]) && isset($_POST["probabilite"]) && isset($_POST["img"]) && isset($_POST["code"]) && isset($_POST["utiliser"]) && isset($_POST["max"]) && isset($_POST["expire"])) {
    $loterieId = $_POST["loterieId"];
    $name = $_POST["name"];
    $gain = $_POST["gain"];
    $probabilite = $_POST["probabilite"];
    $img = $_POST["img"];
	$code = $_POST["code"];
    $utiliser = $_POST["utiliser"];
	$max = $_POST["max"];
    $expire = $_POST["expire"];

    $query = $web->prepare("UPDATE `website_loterie` SET `name` = ?, `gain` = ?, `probabilite` = ?, `img` = ?, `code` = ?, `utiliser` = ?, `max` = ?, `expire` = ? WHERE `id` = ?");
    $query->bindParam(1, $name);
    $query->bindParam(2, $gain);
    $query->bindParam(3, $probabilite);
    $query->bindParam(4, $img);
	$query->bindParam(5, $code);
    $query->bindParam(6, $utiliser);
	$query->bindParam(7, $max);
    $query->bindParam(8, $expire);
    $query->bindParam(9, $loterieId);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La loterie a été modifiée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_loterie";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}

// Traitement de l'ajout d'une nouvelle loterie
if (isset($_POST["action"]) && $_POST["action"] === "addLoterie" && isset($_POST["name"]) && isset($_POST["gain"]) && isset($_POST["probabilite"]) && isset($_POST["img"]) && isset($_POST["code"]) && isset($_POST["utiliser"]) && isset($_POST["max"])) {
    $name = $_POST["name"];
    $gain = $_POST["gain"];
    $probabilite = $_POST["probabilite"];
    $img = $_POST["img"];
	$code = $_POST["code"];
	$utiliser = $_POST["utiliser"];
    $max = $_POST["max"];
    $expire = $_POST["expire"];

    $query = $web->prepare("INSERT INTO `website_loterie` (name, gain, probabilite, img, code, utiliser, max, expire) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $query->bindParam(1, $name);
    $query->bindParam(2, $gain);
    $query->bindParam(3, $probabilite);
    $query->bindParam(4, $img);
	$query->bindParam(5, $code);
    $query->bindParam(6, $utiliser);
	$query->bindParam(7, $max);
    $query->bindParam(8, $expire);
    $query->execute();
    $query->closeCursor();

    echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La nouvel loterie a été ajoutée avec succès !</div><br>';
    echo '<script>setTimeout(function() {window.location.href = "?page=adm_loterie";}, 1000); // 1000 millisecondes = 1 secondes</script>';
}
?>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Panel de la loterie</li>
    </ol>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="page-header margin-top-10"><h4>Gestion de la loterie</h4></div>
                <div class="section section-default padding-25">
                    <?php
                    if (isset($_GET['wLoterie']) && is_numeric($_GET['wLoterie']) && isset($_GET['remove'])) {
                        $query = $web->prepare("DELETE FROM `website_loterie` WHERE `id` = ?;");
                        $query->bindParam(1, $_GET['wLoterie']);
                        $query->execute();
                        $query->closeCursor();
                        echo '<div class="alert alert-success no-border-radius no-margin" role="alert"><strong>Super !</strong> La loterie a été supprimée avec succès !</div><br>';
                        echo '<script>setTimeout(function() {window.location.href = "?page=adm_loterie";}, 1000); // 1000 millisecondes = 1 secondes</script>';
                    }
                    ?>
                    <div class="box no-border-radius">
                        <table class="table table-striped no-margin">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>gain</th>
                                    <th>Proba</th>
                                    <th>Img</th>
                                    <th>code</th>
                                    <th>Utilisé</th>
                                    <th>Max</th>
                                    <th>Expire</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                session_start();
                                /* ini_set('display_errors', 1);
                                 error_reporting(E_ALL);*/

                                // Calculer le nombre total de comptes dans votre tableau
                                $queryCount = $web->prepare("SELECT COUNT(*) FROM `website_loterie`;");
                                $queryCount->execute();
                                $nombreDeLoterie = $queryCount->fetchColumn(); // Le nombre total de comptes dans votre tableau

                                // Nombre de comptes à afficher par page
                                $loterieParPage = 10;

                                // Calculer le nombre total de pages en fonction du nombre de comptes et de la limite par page
                                $moyenne = ceil($nombreDeLoterie / $loterieParPage); // Nombre total de pages

                                // Récupérer le numéro de page à partir du paramètre "num" dans l'URL
                                $page = isset($_GET['num']) ? intval($_GET['num']) : 1;
                                if ($page < 1) {
                                    $page = 1;
                                } elseif ($page > $moyenne) {
                                    $page = $moyenne;
                                }

                                // Calculer l'index de début pour la requête en fonction de la page actuelle
                                $indexDebut = max(($page - 1), 0) * $loterieParPage;

                                $query = $web->prepare("SELECT * FROM `website_loterie` ORDER BY `id` DESC LIMIT $indexDebut, $loterieParPage;");
                                $query->execute();
                                $query->rowCount() > 0;

                                if ($query->rowCount() > 0) : ?>
                                <?php while ($row = $query->fetch(PDO::FETCH_ASSOC)) : ?>
                                    <tr data-loterie-id="<?php echo $row['id']; ?>">
                                        <td class="truncate-cell"><?php
                                            if (isset($row['name'])) {
                                                echo $row['name'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['gain'])) {
                                                echo $row['gain'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['probabilite'])) {
                                                echo $row['probabilite'];
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
                                            if (isset($row['code'])) {
                                                echo $row['code'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['utiliser'])) {
                                                echo $row['utiliser'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['max'])) {
                                                echo $row['max'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td class="truncate-cell"><?php
                                            if (isset($row['expire'])) {
                                                echo $row['expire'];
                                            } else {
                                                echo "Non disponible";
                                            }
                                            ?></td>
                                        <td>
                                            <span class="btn btn-info btn-outline btn-sm" data-toggle="tooltip" title="Modifier" onclick="showEditForm(<?php echo $row['id']; ?>)"><i class="ion-edit"></i></span>
                                            <a href="?page=adm_loterie&wLoterie=<?php echo $row['id']; ?>&remove"><span class="btn btn-danger btn-outline btn-sm" data-toggle="tooltip" title="Supprimer"><i class="ion-trash-b"></i></span></a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="9">
                                        <div class='alert alert-info no-border-radius no-margin' style='text-align: center!important;' role='alert'>
                                            <strong>Oh non!</strong> Il n'y a aucune loterie.
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
                                <a href="?page=adm_loterie&num=1&loterieParPage=<?php echo $loterieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="?page=adm_loterie&num=<?php echo ($page - 1); ?>&loterieParPage=<?php echo $loterieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php else : ?>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-angle-double-left"></i></a>
                                <a href="#" class="btn btn-sm btn-default"><i class="fa fa-chevron-left"></i></a>
                            <?php endif; ?>
                            <?php
                            // Afficher le numéro de page 1
                            echo '<a href="?page=adm_loterie&num=1&loterieParPage=' . $loterieParPage . '" class="btn btn-sm ' . ($page == 1 ? 'btn-primary' : 'btn-default') . '">1</a>';
                            // Afficher "..." s'il y a plus de 4 pages avant la page courante
                            if ($page > 4) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher les numéros de page de manière groupée
                            for ($i = max(2, $page - 3); $i <= min($page + 3, $moyenne - 1); $i++) {
                                echo '<a href="?page=adm_loterie&num=' . $i . '&loterieParPage=' . $loterieParPage . '" class="btn btn-sm ' . ($i == $page ? 'btn-primary' : 'btn-default') . '">' . $i . '</a>';
                            }
                            // Afficher "..." s'il y a plus de 4 pages après la page courante
                            if ($page < $moyenne - 3) {
                                echo '<span class="btn btn-sm btn-default">...</span>';
                            }
                            // Afficher le numéro de la dernière page
                            echo '<a href="?page=adm_loterie&num=' . $moyenne . '&loterieParPage=' . $loterieParPage . '" class="btn btn-sm ' . ($page == $moyenne ? 'btn-primary' : 'btn-default') . '">' . $moyenne . '</a>';
                            ?>
                            <?php if ($page < $moyenne) : ?>
                                <a href="?page=adm_loterie&num=<?php echo ($page + 1); ?>&loterieParPage=<?php echo $loterieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-chevron-right"></i></a>
                                <a href="?page=adm_loterie&num=<?php echo $moyenne; ?>&loterieParPage=<?php echo $loterieParPage; ?>" class="btn btn-sm btn-default"><i class="fa fa-angle-double-right"></i></a>
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
                        <form id="editForm" method="post" action="?page=adm_loterie">
                            <!-- Ajouter les champs cachés pour la gestion de l'édition -->
                            <input type="hidden" name="editLoterie" value="1">
                            <input type="hidden" name="loterieId">

                            <div class="col-md-12 col-xs-12 no-padding">
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="name" placeholder="Name" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="gain" placeholder="Gain" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="probabilite" placeholder="Probabilite" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="img" placeholder="Image"  required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="code" placeholder="Code" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="utiliser" placeholder="Utiliser" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="max" placeholder="Max" required>
                                    </div>
                                    <div class="controls">
                                        <input type="datetime-local" class="form-control" name="expire" placeholder="Expire" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Modifier</button>
                        </form><br/>
                    </div>

                    <!-- Formulaire d'ajout de nouvelles loteries -->
                    <div id="addFormContainer" style="display: none;">
                        <form id="addForm" method="post" action="?page=adm_loterie">
                            <input type="hidden" name="action" value="addLoterie">
                            <div class="col-md-12 col-xs-12 no-padding">
                                <div class="control-group col-md-12 no-padding">
                                    <div class="controls">
                                        <input type="text" class="form-control" name="name" placeholder="Nom de l'objet" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="gain" placeholder="ID objet" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="probabilite" placeholder="Probabilité de le recevoir" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="img" placeholder="Image" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="code" placeholder="Code" value="<?php echo $codeAleatoire; ?>" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="utiliser" placeholder="Utiliser" value="0" required>
                                    </div>
									<div class="controls">
                                        <input type="text" class="form-control" name="max" placeholder="Maximum de lot distribué " required>
                                    </div>
                                    <div class="controls">
                                        <?php
                                        // Obtenez la date et l'heure actuelles
                                        $currentDateTime = date('Y-m-d\TH:i');
                                        ?>
                                        <input type="datetime-local" class="form-control" name="expire" placeholder="Expire " value="<?php echo $currentDateTime; ?>" required>
                                    </div>
								</div>
                            <button type="submit" class="btn btn-success btn-outline pull-left margin-top-15" style="width:100%;">Envoyer</button>
                        </form><br/>
							</div>
							</div>
                    <!-- Condition pour afficher le bouton "Ajouter une nouvelle loterie" uniquement si le formulaire d'édition n'est pas actif -->
					
					<?php if (!$editFormActive) { ?>
						<br/><div>
                            <center><button class="btn btn-primary" onclick="showAddForm()">Ajouter une loterie</button></center>
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
		console.log('Affichage du formulaire d\'édition pour la loterie avec l\'ID :', id);
        var formContainer = document.getElementById('editFormContainer');
        var loterieIdInput = formContainer.querySelector('input[name="loterieId"]');
        var nameInput = formContainer.querySelector('input[name="name"]');
		var gainInput = formContainer.querySelector('input[name="gain"]');
		var probabiliteInput = formContainer.querySelector('input[name="probabilite"]');
        var imgInput = formContainer.querySelector('input[name="img"]');
        var codeInput = formContainer.querySelector('input[name="code"]');
		var utiliserInput = formContainer.querySelector('input[name="utiliser"]');
        var maxInput = formContainer.querySelector('input[name="max"]');
        var expireInput = formContainer.querySelector('input[name="expire"]');

        // Remplir le formulaire avec les données de la loterie
        var row = document.querySelector('tr[data-loterie-id="' + id + '"]');
        var name = row.querySelector('td:nth-child(1)').innerText;
        var gain = row.querySelector('td:nth-child(2)').innerText;
		var probabilite = row.querySelector('td:nth-child(3)').innerText;
        var img = row.querySelector('td:nth-child(4)').innerText;
        var code = row.querySelector('td:nth-child(5)').innerText;
        var utiliser = row.querySelector('td:nth-child(6)').innerText;
		var max = row.querySelector('td:nth-child(7)').innerText;
        var expire = row.querySelector('td:nth-child(8)').innerText;

        loterieIdInput.value = id;
        nameInput.value = name;
		gainInput.value = gain;
		probabiliteInput.value = probabilite;
        imgInput.value = img;
        codeInput.value = code;
        utiliserInput.value = utiliser;
		maxInput.value = max;
        expireInput.value = expire;

        // Afficher le formulaire d'édition et masquer le formulaire d'ajout
        formContainer.style.display = 'block';
        document.getElementById('addFormContainer').style.display = 'none';
    }

    function showAddForm() {
		console.log('Affichage du formulaire d\'ajout de nouvelle loterie.');
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
