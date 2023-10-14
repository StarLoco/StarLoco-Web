<?php
session_start();

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

    // Liez les valeurs aux paramètres nommés
    $insertQuery->bindParam(':guid', $guid, PDO::PARAM_STR);
    $insertQuery->bindParam(':account', $account, PDO::PARAM_STR);
    $insertQuery->bindParam(':date', $date, PDO::PARAM_STR);
    $insertQuery->bindParam(':page', $page, PDO::PARAM_STR);

    // Exécutez la requête SQL d'insertion ici en utilisant la connexion à la base de données
    $insertQuery->execute();
}
?>
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
    /* Style pour le bouton "Clôturer le ticket" en rouge */
    .btn-red {
        background-color: #990000; /* Rouge foncé */
        color: white;
        transition: background-color 0.3s, color 0.3s; /* Ajout d'une transition pour un effet au survol */
        /* Positionnement en haut à droite */
        position: absolute;
        /*top: 899.5px; /* Ajuste la valeur selon le décalage vertical souhaité */
        /* right: 1180px; /* Ajuste la valeur selon le décalage horizontal souhaité */
    }


    /* Changement de couleur au survol */
    .btn-red:hover {
        background-color: #660000; /* Nouvelle couleur au survol */
    }
</style>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li><a href="?page=adm_bugtracker">bugtracker</a></li>
        <li class="active">Bug en cours</li>
    </ol>
    <div class="page-header margin-top-10">
        <h4>Bug en cours</h4>
    </div>

    <div class="wheel" id="wheel">
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $configFile = 'configuration/configuration.php';
        require_once($configFile);

        $ticketId = $_GET['id'];
        $query = $web->prepare("SELECT * FROM website_bugtracker_site WHERE id = ?");
        $query->execute([$ticketId]);
        $ticketDetails = $query->fetch(PDO::FETCH_ASSOC);

        echo '<h1>Sujet : ' . $ticketDetails['subject'] . '</h1>';

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
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Récupérez les réponses associées à ce ticket
        $queryReplies = $web->prepare("SELECT * FROM website_bugtracker_replies WHERE ticket_id = ?");
        $queryReplies->execute([$ticketId]);
        $replies = $queryReplies->fetchAll(PDO::FETCH_ASSOC);

        //permet de cloturer le ticket
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'configurations/configuration.php'; // Inclure ta configuration

            $ticketId = $_POST['ticket_id'];

            try {
                $updateQuery = $web->prepare("UPDATE website_bugtracker_site SET status = 'clôturé' WHERE id = :ticket_id");
                $updateQuery->bindParam(':ticket_id', $ticketId);
                $updateQuery->execute();

                header("Location:?page=adm_bugtracker_view&id=" . $ticketId);
                exit();
            } catch (PDOException $e) {
                echo 'Erreur : ' . $e->getMessage();
            }
        }

        // Affichez les réponses
        echo '<h3>Réponses :</h3>';

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
                echo '<p class="date-en-cours"><i>Il y a ' . $timeAgo . '</i></p>';
                echo '<p>' . $reply['reply_content'] . '</p>';
                echo '</div>';
                echo '<hr/>';
            }
        }

        // Afficher le message de demande close
        if ($ticketStatus === 'Résolu') {
            echo "<div class='alert alert-danger no-border-radius no-margin' style='text-align: center!important;' role='success'>Cette demande est close à tout commentaire.</div>";
        } else {
        ?>
            <!-- Afficher le formulaire de réponse -->
            <form action="?page=adm_bugtracker_reply" method="post" onsubmit="return validateForm()">
                <input type="hidden" name="ticket_id" value="<?php echo $ticketId; ?>">
                <div class="form-group">
                    <textarea class="form-control" name="reply_content" id="editor" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Répondre</button>
            </form><button id="clotureButton" class="btn btn-primary btn-red">Clôturer le bug</button><br>

        <!-- Formulaire pour clôturer le bug -->
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const clotureButton = document.getElementById("clotureButton");
                const ticketId = <?php echo $ticketId; ?>; // Récupère l'ID du ticket depuis PHP

                clotureButton.addEventListener("click", function () {
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", "?page=adm_bugtracker_cloture", true);
                    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Redirige vers la page actuelle avec l'ID du ticket
                            window.location.href = "?page=adm_bugtracker";
                        }
                    };
                    xhr.send("ticket_id=" + ticketId);
                });
            });
        </script>

        <!-- Formulaire pour envoyer le ticket -->
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
            <?php } ?>
    </div>

    <div style="text-align: right;">
        <a href="?page=adm_bugtracker">Retour à la liste des bugs</a>
    </div>
</div>