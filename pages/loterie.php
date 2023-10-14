<?php

if (!isset($_SESSION['user'])) {
    echo "<script>window.location.replace(\"?page=signin\")</script>";
    return;
}

function getLoterieGain($web)
{
    $code_lottery = $_POST['code_lottery'];

    // Vérifier si le nombre maximal de participations est atteint pour ce gain
    $queryCheckMaxParticipations = "SELECT COUNT(*) AS count FROM website_loterie WHERE code = ? AND utiliser >= max AND NOW() <= expire";
    $stmtCheckMaxParticipations = $web->prepare($queryCheckMaxParticipations);
    $stmtCheckMaxParticipations->execute([$code_lottery]);
    $countMaxParticipationsRow = $stmtCheckMaxParticipations->fetch(PDO::FETCH_ASSOC);

    if ($countMaxParticipationsRow['count'] > 0) {
        // Le nombre maximal de participations est atteint pour ce gain, renvoyer une valeur spéciale
        return 'max_participations_reached';
    }

    // Sélectionner les cadeaux valides avec leur probabilité
    $query = "SELECT name, gain, img, probabilite
              FROM website_loterie 
              WHERE code = ? AND utiliser < max AND NOW() <= expire";

    $stmt = $web->prepare($query);
    $stmt->execute([$code_lottery]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($results) {
        // Trier les cadeaux en fonction de leur probabilité, du plus petit au plus grand
        usort($results, function ($a, $b) {
            return $a['probabilite'] <=> $b['probabilite'];
        });

        // Générer un nombre aléatoire entre 0 et 1
        $randomValue = mt_rand() / mt_getrandmax();

        // Parcourir les cadeaux valides triés et sélectionner le cadeau attribué en fonction de la probabilité
        $cumulativeProbability = 0;
        foreach ($results as $gift) {
            $cumulativeProbability += $gift['probabilite'];
            if ($randomValue <= $cumulativeProbability) {
                return $gift;
            }
        }

        return null;
    }

    return null;
}

// Fonction pour incrémenter le champ "utiliser" dans la base de données
function incrementUtiliser($web, $code_lottery)
{
    $query = "UPDATE website_loterie SET utiliser = utiliser + 1 WHERE code = ?";
    $stmt = $web->prepare($query);
    $stmt->execute([$code_lottery]);
}

// Fonction pour insérer les informations du gagnant dans la table website_loterie_gagnant
function insertWinnerToDatabase($web, $name, $gain, $code, $guid, $account)
{
    // Vérifier si le code avec le même compte existe déjà dans la table website_loterie_gagnant
    $queryCheck = "SELECT COUNT(*) AS count FROM website_loterie_gagnant WHERE code = ? AND account = ?";
    $stmtCheck = $web->prepare($queryCheck);
    $stmtCheck->execute([$code, $account]);
    $countRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    if ($countRow['count'] > 0) {
        // Le code avec le même compte existe déjà, afficher un message d'erreur
        echo '<script>
                document.getElementById("lottery-error").style.display = "block";
                document.getElementById("lottery-error").innerHTML = "' . $translations["ALERTES_010"] . '";
              </script>';
        return;
    }

    // Utiliser une requête préparée pour insérer les informations du gagnant dans la table website_loterie_gagnant
    $query = "INSERT INTO website_loterie_gagnant (name, gain, code, guid, account, date) VALUES (?, ?, ?, ?, ?, NOW())";
    $stmt = $web->prepare($query);

    // Lier les paramètres et exécuter la requête préparée
    $stmt->execute([$name, $gain, $code, $guid, $account]);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $translations["MOTS_013"]; ?></title>
    <!-- Mettez ici les liens vers les fichiers CSS et JS nécessaires (par exemple Bootstrap) -->
</head>
<body>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_013"]; ?></li>
    </ol>
	<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	
    // Vérifier si la connexion est autorisée
    $query = $web->prepare("SELECT loterie FROM website_general;");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $loterieAllowed = $result['loterie'];
    $query->closeCursor();
	
	 // Afficher un message si la loterie est désactivé
    if ($loterieAllowed == 'non') {
        echo $translations["ALERTES_011"];
    } else {
        // Si la loterie est activé, afficher le contenu
        ?>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="no-border no-padding-top">
                <div class="page-header margin-top-10"><h4><?php echo $translations["MOTS_013"]; ?></h4></div>
                <div class="section section-default padding-25">
                    <p class="no-margin">
                    <div class="wheel-container">
                        <div class="wheel" id="wheel">
                            <center>
                                <!-- Formulaire de participation -->
                                <form id="lottery-form" method="POST">
                                    <center><img src="img/loterie/accueil/loterie.png" style="width: 500px; height: auto;"></center><br/>
                                    <?php echo $translations["MOTS_120"]; ?> <?php  echo TITLE; ?> ?<br/>
                                    <br/>
                                    <?php echo $translations["MOTS_121"]; ?><br/><br/>
                                    <label for="code_lottery"><?php echo $translations["MOTS_122"]; ?></label>
                                    <input type="text" name="code_lottery" required>
                                    <!-- Bouton pour soumettre le formulaire -->
                                    <button type="submit" class="btn btn-primary" name="participer"><?php echo $translations["MOTS_123"]; ?></button>
                                    <!-- <button type="submit" name="participer">Valider</button>-->
                                </form>
                                <!-- Élément pour afficher le résultat de la loterie -->
                                <div id="lottery-result" style="display: none;"></div>
                                <!-- Élément pour afficher un message d'erreur -->
                                <div id="lottery-error" style="display: none; color: red;"></div>
                            </center>
                        </div>
                    </div>
                    </p>
                </div>
            </section>
        </div>
    </div>
<?php } ?>
</div>

<?php
if (isset($_POST['participer'])) {
    $code_lottery = $_POST['code_lottery'];
    $account = $_SESSION['user'];
    $guid = $_SESSION['id'];

    // Vérifier si le code avec le même compte existe déjà dans la table website_loterie_gagnant
    $queryCheck = "SELECT COUNT(*) AS count FROM website_loterie_gagnant WHERE code = ? AND account = ?";
    $stmtCheck = $web->prepare($queryCheck);
    $stmtCheck->execute([$code_lottery, $account]);
    $countRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le code a expiré
    $queryExpire = "SELECT COUNT(*) AS count FROM website_loterie WHERE code = ? AND NOW() > expire";
    $stmtExpire = $web->prepare($queryExpire);
    $stmtExpire->execute([$code_lottery]);
    $countExpireRow = $stmtExpire->fetch(PDO::FETCH_ASSOC);

    if ($countRow['count'] > 0) {
        // Le code avec le même compte existe déjà, afficher un message d'erreur spécifique
        echo '<script>
                document.getElementById("lottery-error").style.display = "block";
                document.getElementById("lottery-error").innerHTML = "<br/>'. $translations["ALERTES_012"] .'";
              </script>';
    } elseif ($countExpireRow['count'] > 0) {
        // Le code a expiré, afficher un message d'erreur spécifique
        echo '<script>
            document.getElementById("lottery-error").style.display = "block";
            document.getElementById("lottery-error").innerHTML = "<br/>'. $translations["ALERTES_013"] .'";
          </script>';
    } else {
        // Le code n'a pas été utilisé avec le même compte, procéder à la vérification du gain
        $loterieGain = getLoterieGain($web);
        if ($loterieGain === 'max_participations_reached') {
            // Afficher un message d'erreur si le nombre maximal de participations pour ce gain est atteint
            echo '<script>
                    document.getElementById("lottery-error").style.display = "block";
                    document.getElementById("lottery-error").innerHTML = "<br/>'. $translations["ALERTES_014"] .'";
                  </script>';
        } elseif ($loterieGain) {
            // Afficher le résultat de la loterie si un gain est trouvé
            echo '<script>
                    document.getElementById("lottery-form").style.display = "none";
                    document.getElementById("lottery-result").style.display = "block";
                    document.getElementById("lottery-result").innerHTML = "</p><img src= img/loterie/accueil/loterie-gagnant.png>'. $translations["MOTS_124"] .'<h1><p>' . $loterieGain['name'] . '</p></h1><img src=\'img/loterie/gains/' . $loterieGain['img'] . '\' style=\'width: 200px; height: auto;\'>";
                  </script>';

            // Insérer les informations du gagnant dans la nouvelle table website_loterie_gagnant
            insertWinnerToDatabase($web, $loterieGain['name'], $loterieGain['gain'], $code_lottery, $guid, $account);

            // Incrémenter le champ "utiliser" dans la base de données pour le code utilisé avec succès
            incrementUtiliser($web, $code_lottery);

            // Redirection vers la page de la loterie après 30 secondes
            echo '<script>setTimeout(function() {
                     window.location.href = "' . URL_SITE . '?page=loterie";
                        }, 30000);
                  </script>';

        } else {
            // Afficher un message d'erreur si le code entré par l'utilisateur n'est pas valide
            echo '<script>
                    document.getElementById("lottery-error").style.display = "block";
                    document.getElementById("lottery-error").innerHTML = "<br/>'. $translations["ALERTES_015"] .'";
                  </script>';
        }
    }
}
?>

</body>
</html>