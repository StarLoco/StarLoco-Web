<style>
    /* Style pour les tickets résolus */
    .reponse-resolu {
        color: red; /* Changez la couleur en rouge ou selon vos préférences */
    }

    /* Style pour les tickets en cours */
    .reponse-en-cours {
        color: green; /* Changez la couleur en vert ou selon vos préférences */
    }
	/* Style pour le pseudo en bleu */
.pseudo-blue {
    color: darkblue; /* Changez la couleur en bleu ou selon vos préférences */
	margin-bottom: -5px; /* Ajustez la marge à votre préférence */
	}
 /* Style pour la date en gris clair */
    .date-en-cours {
        color: lightgray; /* Changez la couleur en vert ou selon vos préférences */
		
    }
</style>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_070"]; ?></li>
    </ol>
    <div class="page-header margin-top-10">
        <h4>Mes bugs</h4>
    </div>
   
                <div class="wheel" id="wheel">
                    <?php
                    if (!isset($_SESSION['user'])) {
                        echo "<script>window.location.replace(\"?page=signin\")</script>";
                        exit();
                    }

                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);

                    $configFile = 'configuration/configuration.php';
                    require_once($configFile);

                    $ticketId = $_GET['id'];
                    $query = $web->prepare("SELECT * FROM website_bugtracker_site WHERE id = ?");
                    $query->execute([$ticketId]);
                    $ticketDetails = $query->fetch(PDO::FETCH_ASSOC);

                    echo '<h1>' . $translations["MOTS_084"] . ' ' . $ticketDetails['subject'] . '</h1>';

                    $ticketStatus = $ticketDetails['status'];
                    $statusClass = '';

                    if ($ticketStatus === 'Résolu') {
                        $statusClass = 'reponse-resolu';
                    } elseif ($ticketStatus === 'En cours') {
                        $statusClass = 'reponse-en-cours';
                    }

                    echo '<p>Statut : <span class="' . $statusClass . '">' . $ticketStatus . '</span></p>';
                    echo '<p>Date : ' . date('d-m-y H:i:s', strtotime($ticketDetails['date'])) . '</p>';
                    echo '<p>' . $ticketDetails['content'] . '</p>';
                    ?>

                    <hr/>
                    <?php
                    // Récupérez les réponses associées à ce ticket
                    $queryReplies = $web->prepare("SELECT * FROM website_bugtracker_replies WHERE ticket_id = ?");
                    $queryReplies->execute([$ticketId]);
                    $replies = $queryReplies->fetchAll(PDO::FETCH_ASSOC);

                    // Affichez les réponses
				echo $translations["MOTS_081"];
                    foreach ($replies as $reply) {
                        $ticketId = $reply['ticket_id'];
                        $replyPseudo = $reply['pseudo'];

                        if ($replyPseudo === false) {
                            $replyPseudo = "Aucun pseudo trouvé";
                        }

                        if (isset($reply['date'])) {
						$currentTimestamp = time();
						$replyTimestamp = strtotime($reply['date']);
						$timeDifference = $currentTimestamp - $replyTimestamp;

						if ($timeDifference < 60) {
							$timeAgo = $timeDifference . ' secondes';
						} elseif ($timeDifference < 3600) {
							$timeAgo = floor($timeDifference / 60) . ' minutes';
						} elseif ($timeDifference < 86400) {
							$timeAgo = floor($timeDifference / 3600) . ' heures';
						} elseif ($timeDifference < 2592000) {
							$timeAgo = floor($timeDifference / 86400) . ' jours';
						} elseif ($timeDifference < 31536000) {
							$timeAgo = floor($timeDifference / 2592000) . ' mois';
						} else {
							$timeAgo = floor($timeDifference / 31536000) . ' années';
						}

						echo '<div class="reply">';
						echo '<p class="pseudo-blue"><b>' . $replyPseudo . '</b></p>';
						echo '<p class="date-en-cours"><i>'. $translations["MOTS_085"] .' ' . $timeAgo . '</i></p>';
						echo '<p>' . $reply['reply_content'] . '</p>';
						echo '</div>';
						echo '<hr/>';
					}
				}

					// Afficher le message de demande close
					if ($ticketStatus === 'Résolu') {
						echo $translations["ALERTES_002"];
					} else {
						?>
						<!-- Afficher le formulaire de réponse -->
						<form action="?page=bugtracker_reply" method="post" onsubmit="return validateForm()">
							<input type="hidden" name="ticket_id" value="<?php echo $ticketId; ?>">
							<div class="form-group">
								<textarea class="form-control" name="reply_content" id="editor" rows="4"></textarea>
							</div>
							<button type="submit" class="btn btn-primary"><?php echo $translations["MOTS_082"]; ?></button>
						</form>

						<script>
							function validateForm() {
								var editorData = CKEDITOR.instances.editor.getData();
								if (editorData.trim() === "") {
									alert("Le contenu de la réponse ne peut pas être vide.");
									return false; // Empêche l'envoi du formulaire
								}
								return true; // Soumet le formulaire
							};
						</script>
					<?php
					}
					?>

                </div>

    <div style="text-align: right;">
        <a href="?page=bugtracker"><?php echo $translations["MOTS_083"]; ?></a>
    </div>
</div>