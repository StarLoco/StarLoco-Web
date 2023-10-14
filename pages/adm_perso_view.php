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
    /* Style pour le bouton "suprimer le compte" en rouge */
    .btn-red {
        background-color: #990000; /* Rouge foncé */
        color: white;
        transition: background-color 0.3s, color 0.3s; /* Ajout d'une transition pour un effet au survol */
        /* Positionnement en haut à droite */
        position: absolute;
        /*top: 1040px; /* Ajuste la valeur selon le décalage vertical souhaité */
        right: 700px; /* Ajuste la valeur selon le décalage horizontal souhaité */
    }
    /* Changement de couleur au survol */
    .btn-red:hover {
        background-color: #660000; /* Nouvelle couleur au survol */
    }
</style>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li><a href="?page=adm_perso">Personnages</a></li>
        <li class="active">Persos en cours</li>
    </ol>
    <div class="page-header margin-top-10">
        <h4>Visualiser le compte</h4>
    </div>

    <div class="wheel" id="wheel">
        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $configFile = 'configuration/configuration.php';
        require_once($configFile);

        $persosId = $_GET['id'];
        $query = $login->prepare("SELECT * FROM world_players WHERE id = ?");
        $query->execute([$persosId]);
        $persoDetails = $query->fetch(PDO::FETCH_ASSOC);

        echo '<p><u>Id :</u> ' . $persoDetails['id'] . '</p>';
        echo '<p><u>Account ID :</u> ' . $persoDetails['account'] . '</p>';
        echo '<p><u>Nom :</u> ' . $persoDetails['name'] . '</p>';
        echo '<p><u>Groupe :</u> ';
        $groupe = $persoDetails['groupe'];
        if ($groupe == 0) {
            echo 'joueur';
        } elseif ($groupe == 1) {
            echo 'fondateur';
        } elseif ($groupe == 2) {
            echo 'administrateur';
        } elseif ($groupe == 3) {
            echo 'community';
        } elseif ($groupe == 4) {
            echo 'developpeur';
        } elseif ($groupe == 5) {
            echo 'developpeuse';
        } elseif ($groupe == 6) {
            echo 'chef modo';
        } elseif ($groupe == 7) {
            echo 'modo';
        } elseif ($groupe == 9) {
            echo 'modo test';
        } elseif ($groupe == 8) {
            echo 'animateur';
        } elseif ($groupe == 10) {
            echo 'testeur';
        } elseif ($groupe == 11) {
            echo 'mercenaire';
        } elseif ($groupe == 12) {
            echo 'Debugueur';
        }
        echo '</p>';
        $logged = $persoDetails['logged'];
        if ($logged == 1) {
            echo '<p><u>Connecté au jeu :</u> <b><span style="color: green;">oui</span></b></p>';
        } else {
            echo '<p><u>Connecté au jeu :</u> <b><span style="color: red;">non</span></b></p>';
        }
        echo '<p><u>Classe :</u> ' . convertClassIdToString($persoDetails['class'], $persoDetails['sexe']) . '</p>';
        echo '<p><u>Sexe :</u> ' . ($persoDetails['sexe'] == 0 ? "masculin" : "féminin") . '</p>';
        $kamas = $persoDetails['kamas'];
        $formattedKamas = number_format($kamas, 0, ',', ' ');
        echo '<p><u>Kamas :</u> ' . $formattedKamas . '</p>';
        $energy = $persoDetails['energy'];
        $formattedEnergy = number_format($energy, 0, ',', ' ');

        echo '<p><u>Energie :</u> ' . $formattedEnergy . '</p>';
        echo '<p><u>Level :</u> ' . $persoDetails['level'] . '</p>';
        $xp = $persoDetails['xp'];
        $formattedXp = number_format($xp, 0, ',', ' ');
        echo '<p><u>Expérience :</u> ' . $formattedXp . '</p>';
        $alignement = $persoDetails['alignement'];
        switch ($alignement) {
            case 0:
                $alignementText = 'Neutre';
                break;
            case 1:
                $alignementText = 'Bontarien';
                break;
            case 2:
                $alignementText = 'Brakmarien';
                break;
            case 3:
                $alignementText = 'Mercenaire';
                break;
            default:
                $alignementText = 'Inconnu';
        }
        echo '<p><u>Alignement :</u> ' . $alignementText . '</p>';
        $honor = $persoDetails['honor'];
        $formattedHonor = number_format($honor, 0, '.', ' ');
        echo '<p><u>Honneur :</u> ' . $formattedHonor . '</p>';
        $deshonor = $persoDetails['deshonor'];
        $formattedDeshonor = number_format($deshonor, 0, '.', ' ');
        echo '<p><u>Déshonneur :</u> ' . $formattedDeshonor . '</p>';
        $savepos = $persoDetails['savepos'];
        $parts = explode(',', $savepos);
        $map = $parts[0];
        $cell = $parts[1];
        echo '<p><u>Position :</u> map: ' . $map . ', cellule: ' . $cell . '</p>';
        echo '<p><u>Prison :</u> ' . $persoDetails['prison'] . '</p>';
        ?>
    </div><hr/>
	<div style="text-align: right;"><button id="clotureButton" class="btn btn-primary btn-red">Supprimer le perso</button></div>
	<div style="text-align: left;"><a href="?page=adm_perso">Retour àu personnage</a></div>
    


    <!-- Formulaire pour clôturer le ticket -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const clotureButton = document.getElementById("clotureButton");
            const persosId = <?php echo $persosId; ?>; // Récupère l'ID du ticket depuis PHP

            clotureButton.addEventListener("click", function () {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "?page=adm_perso_cloture", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Redirige vers la page actuelle avec l'ID du ticket
                        window.location.href = "?page=adm_perso";
                    }
                };
                xhr.send("perso_id=" + persosId);
            });
        });
    </script>
</div>