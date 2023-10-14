<?php
ob_start(); // Démarrer la mise en mémoire tampon de sortie

if (!isset($_SESSION['user']) || !in_array($_SESSION['data']->guid, ADMIN_GUID)) {
    // Code de sortie ou de redirection si les conditions ne sont pas satisfaites
    exit;
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $guid = $_SESSION['id'];
    $account = $_SESSION['user']; // Assurez-vous de récupérer l'identifiant de l'utilisateur connecté correctement
    $date = date('Y-m-d H:i:s');

    /// Requête pour obtenir la valeur du champ 'log' depuis la table 'website_general'
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


// Gestion de la mise à jour de la minuterie d'ouverture
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["date_heure_ouverture"])) {
    $dateHeureOuverture = $_POST["date_heure_ouverture"];

        $query = $web->prepare("UPDATE website_general SET minuterie_ouverture = :dateHeureOuverture");
        $query->bindParam(':dateHeureOuverture', $dateHeureOuverture);
        $query->execute();

}
?>

<div class="leftside">
	<ol class="breadcrumb">
		<li><a href="?page=adm_administration">Administration</a></li>
		<li class="active">Gestion du site</li>
	</ol>

				<div class="page-header margin-top-10">
					<h4>Gestion du site</h4>
				</div>
	<?php
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		if (isset($_POST["field"]) && isset($_POST["value"])) {
			$field = $_POST["field"];
			$value = $_POST["value"];

			$query = $web->prepare("UPDATE website_general SET $field = :value");
			$query->bindParam(":value", $value);
			$query->execute();

			exit; // Arrêtez l'exécution du reste de la page après la mise à jour AJAX


		}
	}
	?>

	<style>
		/* Styles du Toggle Switch */
		/* taille du Toggle Switch */
		.switch {
			position: relative;
			display: inline-block;
			width: 50px;
			height: 24px;
		}

		.switch input {
			opacity: 0;
			width: 0;
			height: 0;
		}

		.slider {
			position: absolute;
			cursor: pointer;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: #ccc;
			-webkit-transition: .4s;
			transition: .4s;
		}

		/* taille du rond du Toggle Switch */
		.slider:before {
			position: absolute;
			content: "";
			height: 20px;
			width: 20px;
			left: 2px;
			bottom: 1.7px;
			background-color: white;
			-webkit-transition: .4s;
			transition: .4s;
		}

		input:checked + .slider {
			background-color: #2196F3;
		}

		input:focus + .slider {
			box-shadow: 0 0 1px #2196F3;
		}

		input:checked + .slider:before {
			-webkit-transform: translateX(26px);
			-ms-transform: translateX(26px);
			transform: translateX(26px);
		}

		/* Rounded sliders */
		.slider.round {
			border-radius: 34px;
		}

		.slider.round:before {
			border-radius: 50%;
		}
	</style>
					<?php
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);

					$query = $web->prepare("SELECT login, inscription, maintenance, ouverture, support, loterie, viewdrop, profile, ladder, rejoin, bugtracker, shop, log, lost_password, commentaire, prehome FROM website_general");
					$query->execute();
					$row = $query->fetch(PDO::FETCH_OBJ);

					// Ajouter le var_dump() ici pour afficher les valeurs de $row
					//var_dump($row);
					?>
    <!-- Formulaire pour les switches -->
					<form method="post">
                        <form method="post">
                            <!-- Ouverture du site -->
                            <div class="form-group">
                                <label>Ouverture:</label>
                                <label class="switch">
                                    <input type="checkbox" name="ouverture" id="ouverture-toggle" <?php echo $row->ouverture == 'oui' ? 'checked' : ''; ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label>Date et Heure d'ouverture:</label>
                                <input type="datetime-local" name="date_heure_ouverture" value=""> <button type="submit" class="btn btn-primary">Enregistrer</button>
                            </div>
                        </form>
						<!-- Maintenance du site -->
						<div class="form-group">
							<label>Maintenance:</label>
							<label class="switch">
								<input type="checkbox" name="maintenance" id="maintenance-toggle" <?php echo $row->maintenance == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label><br>
							*assurez-vous d'être connecté sur le site, car impossible de se connecter durant la maintenance.
						</div>
                        <!-- Prehome du site -->
                        <div class="form-group">
                            <label>Prehome:</label>
                            <label class="switch">
                                <input type="checkbox" name="prehome" id="prehome-toggle" <?php echo $row->prehome == 'oui' ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div><br/>
                        <!-- Gestion des pages -->
						<div class="page-header margin-top-10">
							<h4>Gestion des pages</h4>
						</div>
						<!-- Login du site -->
						<div class="form-group">
							<label>Login:</label>
							<label class="switch">
								<input type="checkbox" name="login" id="login-toggle" <?php echo $row->login == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
						<!-- Inscription du site -->
						<div class="form-group">
							<label>Inscription:</label>
							<label class="switch">
								<input type="checkbox" name="inscription" id="inscription-toggle" <?php echo $row->inscription == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
                        <!-- recuperation mot de passe du site -->
                        <div class="form-group">
                            <label>Lost password:</label>
                            <label class="switch">
                                <input type="checkbox" name="lost_password" id="lost_password-toggle" <?php echo $row->lost_password == 'oui' ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <!-- Commentaires du site -->
                        <div class="form-group">
                            <label>Commentaires:</label>
                            <label class="switch">
                                <input type="checkbox" name="commentaire" id="commentaire-toggle" <?php echo $row->commentaire == 'oui' ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
						<!-- Support du site -->
						<div class="form-group">
							<label>Support:</label>
							<label class="switch">
								<input type="checkbox" name="support" id="support-toggle" <?php echo $row->support == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
						<!-- bugtracker du site -->
						<div class="form-group">
							<label>Bugtracker:</label>
							<label class="switch">
								<input type="checkbox" name="bugtracker" id="bugtracker-toggle" <?php echo $row->bugtracker == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
						<!-- Loterie du site -->
						<div class="form-group">
							<label>Loterie:</label>
							<label class="switch">
								<input type="checkbox" name="loterie" id="loterie-toggle" <?php echo $row->loterie == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
						<!-- encyclopedie du site -->
						<div class="form-group">
							<label>Encyclopedie:</label>
							<label class="switch">
								<input type="checkbox" name="viewdrop" id="viewdrop-toggle" <?php echo $row->viewdrop == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
						<!-- Profil joueur du site -->
						<div class="form-group">
							<label>Profil:</label>
							<label class="switch">
								<input type="checkbox" name="profile" id="profile-toggle" <?php echo $row->profile == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
						<!-- Ladder du site -->
						<div class="form-group">
							<label>Ladder:</label>
							<label class="switch">
								<input type="checkbox" name="ladder" id="ladder-toggle" <?php echo $row->ladder == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
						<!-- Rejoindre du site -->
						<div class="form-group">
							<label>Rejoindre:</label>
							<label class="switch">
								<input type="checkbox" name="rejoin" id="rejoin-toggle" <?php echo $row->rejoin == 'oui' ? 'checked' : ''; ?>>
								<span class="slider round"></span>
							</label>
						</div>
                        <!-- Shop du site -->
                        <div class="form-group">
                            <label>Boutique:</label>
                            <label class="switch">
                                <input type="checkbox" name="shop" id="shop-toggle" <?php echo $row->shop == 'oui' ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <!-- Logs du site -->
                        <div class="form-group">
                            <label>Logs:</label>
                            <label class="switch">
                                <input type="checkbox" name="logs" id="log-toggle" <?php echo $row->log == 'oui' ? 'checked' : ''; ?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
					</form>
					<!-- Fin du formulaire pour les switches -->



	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(document).ready(function() {
			// Fonction pour mettre à jour la valeur dans la base de données avec AJAX
			function updateValue(field, value) {
				$.ajax({
					url: '',
					type: 'POST',
					data: { field: field, value: value },
					success: function(response) {
						console.log('Mise à jour réussie.');
					},
					error: function(xhr, status, error) {
						console.error('Erreur lors de la mise à jour : ' + error);
					}
				});
			}

			// Gestion des événements de changement pour les Toggle Switches
			$('#login-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('login', currentValue);
			});

			$('#inscription-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('inscription', currentValue);
			});

            $('#lost_password-toggle').change(function() {
                var currentValue = $(this).prop('checked') ? 'oui' : 'non';
                updateValue('lost_password', currentValue);
            });

            $('#maintenance-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('maintenance', currentValue);
			});

			$('#ouverture-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('ouverture', currentValue);
			});

			$('#support-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('support', currentValue);
			});

			$('#bugtracker-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('bugtracker', currentValue);
			});

			$('#loterie-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('loterie', currentValue);
			});

			$('#viewdrop-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('viewdrop', currentValue);
			});

			$('#profile-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('profile', currentValue);
			});

			$('#ladder-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('ladder', currentValue);
			});

			$('#rejoin-toggle').change(function() {
				var currentValue = $(this).prop('checked') ? 'oui' : 'non';
				updateValue('rejoin', currentValue);
			});

            $('#shop-toggle').change(function() {
                var currentValue = $(this).prop('checked') ? 'oui' : 'non';
                updateValue('shop', currentValue);
            });
            $('#log-toggle').change(function() {
                var currentValue = $(this).prop('checked') ? 'oui' : 'non';
                updateValue('log', currentValue);
            });
            $('#commentaire-toggle').change(function() {
                var currentValue = $(this).prop('checked') ? 'oui' : 'non';
                updateValue('commentaire', currentValue);
            });
            $('#prehome-toggle').change(function() {
                var currentValue = $(this).prop('checked') ? 'oui' : 'non';
                updateValue('prehome', currentValue);
            });
		});
	</script>
</div>
