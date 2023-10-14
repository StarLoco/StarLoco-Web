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
         right: 710px; /* Ajuste la valeur selon le décalage horizontal souhaité */
    }
    /* Changement de couleur au survol */
    .btn-red:hover {
        background-color: #660000; /* Nouvelle couleur au survol */
    }
</style>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li><a href="?page=adm_account">Account</a></li>
        <li class="active">Account en cours</li>
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

        $accounttId = $_GET['guid'];
        $query = $login->prepare("SELECT * FROM world_accounts WHERE guid = ?");
        $query->execute([$accounttId]);
        $accountDetails = $query->fetch(PDO::FETCH_ASSOC);

        echo '<p><u>Guid :</u> ' . $accountDetails['guid'] . '</p>';
        echo '<p><u>Account :</u> ' . $accountDetails['account'] . '</p>';
        echo '<p><u>Pseudo :</u> ' . $accountDetails['pseudo'] . '</p>';
        echo '<p><u>IP jeu :</u> ' . $accountDetails['lastIP'] . '</p>';
        echo '<p><u>Email :</u> ' . $accountDetails['email'] . '</p>';
        echo '<p><u>Token email :</u> ' . $accountDetails['email_token'] . '</p>';
        $emailVerified = $accountDetails['email_verified'];
        if ($emailVerified == 1) {
            echo '<p><u>Email vérifié :</u> <b><span style="color: green;">oui</span></b></p>';
        } else {
            echo '<p><u>Email vérifié :</u> <b><span style="color: red;">non</span></b></p>';
        }
        echo '<p><u>Question :</u> ' . $accountDetails['question'] . '</p>';
        echo '<p><u>Reponse :</u> ' . $accountDetails['reponse'] . '</p>';
        echo '<p><u> '. NOM_POINT .' :</u> ' . $accountDetails['points'] . '</p>';
        echo '<p><u>Creation compte site :</u> ' . date('d/m/Y H:i:s', strtotime($accountDetails['dateRegister'])) . '</p>';
        echo '<p><u>Connexion au site :</u> ' . date('d/m/Y H:i:s', strtotime($accountDetails['lastConnectDaySite'])) . '</p>';
        echo '<p><u>IP Connexion au site :</u> ' . $accountDetails['lastIPConnectionSite'] . '</p>';
        echo '<p><u>Connection jeu :</u> ' . $accountDetails['lastConnectionDate'] . '</p>';
        echo '<p><u>Total votes :</u> ' . $accountDetails['totalVotes'] . '</p>';
        echo '<p><u>Heure vote :</u> ' . $accountDetails['heurevote'] . '</p>';
        echo '<hr/>';
		 $logged = $accountDetails['logged'];
        if ($logged == 1) {
            echo '<p><u>Connecté jeu :</u> <b><span style="color: green;">oui</span></b></p>';
        } else {
            echo '<p><u>Connecté jeu :</u> <b><span style="color: red;">non</span></b></p>';
        }
        $banned = $accountDetails['banned'];

        if ($banned == 'oui') {
            echo '<p><u>Bannis :</u> <b><span style="color: red;">oui</span></b></p>';
            if ($accountDetails['bannedTime'] === '86313600000') {
                echo '<p><u>Temps bannis :</u> <b><span style="color: red;">à vie</span></b></p>';
            } else {
                echo '<p><u>Temps bannis :</u> <b><span style="color: red;">temporaire</span></b></p>';
                echo '<p><u>Bannissement prévu jusqu\'à :</u> ' . date("Y-m-d H:i:s", $accountDetails['bannedTime'] / 1000) . '</p>';
            }
        } else {
            echo '<p><u>Bannis :</u> <b><span style="color: forestgreen;">non</span></b></p>';
        }
        echo '<p><u>Mute Temps:</u> ' . $accountDetails['muteTime'] . '</p>';
        echo '<p><u>Mute Raison :</u> ' . $accountDetails['muteRaison'] . '</p>';
        echo '<p><u>Mute Pseudo :</u> ' . $accountDetails['mutePseudo'] . '</p>';
        ?>
    </div><hr/>
	<div style="text-align: right;"><button id="clotureButton" class="btn btn-primary btn-red">Supprimer le compte</button></div>
	<div style="text-align: left;"><a href="?page=adm_account">Retour àu comptes</a></div>
    


    <!-- Formulaire pour clôturer le ticket -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const clotureButton = document.getElementById("clotureButton");
            const accountId = <?php echo $accounttId; ?>; // Récupère l'ID du ticket depuis PHP

            clotureButton.addEventListener("click", function () {
                const xhr = new XMLHttpRequest();
                xhr.open("POST", "?page=adm_account_cloture", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Redirige vers la page actuelle avec l'ID du ticket
                        window.location.href = "?page=adm_account";
                    }
                };
                xhr.send("account_id=" + accountId);
            });
        });
    </script>
</div>