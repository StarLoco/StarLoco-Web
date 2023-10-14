<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_086"]; ?></li>
    </ol>
<h2 class="page-header text-center no-margin-top"><i class="ion-clipboard"></i> <?php echo $translations["MOTS_087"]; ?></h2>
<?php
require_once('configuration/configuration.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$token = $_GET['token']; // Récupérez le token depuis l'URL

$query = $login->prepare("SELECT guid FROM world_accounts WHERE email_token = ? LIMIT 1");
$query->execute([$token]);
$user = $query->fetch();

if ($user) {
    // Mettez à jour le statut de vérification de l'adresse e-mail
    $updateStmt = $login->prepare("UPDATE world_accounts SET email_verified = 1, email_token = NULL WHERE guid = ?");
    $updateStmt->execute([$user['guid']]);


    echo $translations["SUCCESS_003"];
    echo "<meta http-equiv='refresh' content='3; url=" . URL_SITE . "'> ";
} else {
    echo $translations["ALERTES_003"];
}
?>
</div>


