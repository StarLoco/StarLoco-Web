<!DOCTYPE html>
<html lang="<?php echo $language; ?>"
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">

    <title><?php echo TITLE; ?></title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favi.ico">

    <!-- Core CSS -->
    <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <link href="css/themes/blue.css" rel="stylesheet" id="themes" />

    <!-- Plugins fontawesome -->
    <link rel="stylesheet" data-purpose="Layout StyleSheet" title="Web Awesome" href="/css/app-wa-02670e9412103b5852dcbe140d278c49.css?vsn=d">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-solid.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-regular.css">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/sharp-light.css">
    <link href="plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" />
    <!-- Plugins  -->
    <link href="plugins/ionicons/css/ionicons.min.css" rel="stylesheet" />
    <link href="plugins/animate/animate.min.css" rel="stylesheet" >
    <link href="plugins/bxslider/bxslider.css" rel="stylesheet"  />
    <link href="plugins/notification/css/ns-default.css" rel="stylesheet" />
    <link href="plugins/notification/css/ns-style-other.css" rel="stylesheet" />
    <link href="plugins/notification/css/ns-style-other.css" rel="stylesheet" />
</head>
<!------------------------------------- lang --------------------------------------------------------------------------------------------------------------------->
<?php
$defaultLanguage = "fr"; // Langue par défaut
$language = isset($_COOKIE['language']) ? $_COOKIE['language'] : $defaultLanguage;

// Charger les traductions depuis le fichier JSON en fonction de la langue choisie
$translations = json_decode(file_get_contents("translation/{$language}_msg_list.json"), true);

/*if (isset($_GET['lang'])) {
    $selectedLanguage = $_GET['lang'];
    $expiration = time() + (365 * 24 * 60 * 60); // 1 an à partir de maintenant
    setcookie("language", $selectedLanguage, $expiration, "/");

}*/
?>
<!------------------------------------/ lang --------------------------------------------------------------------------------------------------------------------->
<!------------------------------------ progress bar -------------------------------------------------------------------------------------------------------------->
<style>
    .progress-container {
        width: 100%;
        height: 1px;
        background-color: #000;
    }

    .progress-bar {
        height: 140%;
        width: 100%;
        background-color: #4caf50;
        transition: width 0.3s;
        display: none; /* Cachez la barre de progression par défaut */
    }
</style>
<div class="progress-container">
    <div class="progress-bar" id="myProgressBar"></div>
</div>
<!------------------------------------/ progress bar -------------------------------------------------------------------------------------------------------------->

<!---------------------------------- Chargement de la maintenance et ouverture du site ---------------------------------------------------------------------------->
<?php
$configFile = 'configuration/configuration.php';
require_once($configFile);

session_start();
$isUserLoggedIn = isset($_SESSION['user']) && ($_SESSION['data']->guid === ADMIN_GUID);

function checkStatusAndInclude($column, $value, $includeFile) {
    global $web, $isUserLoggedIn;

    $query = $web->prepare("SELECT $column FROM website_general");
    $query->execute();
    $status = $query->fetchColumn();
    $query->closeCursor();

    if ($status === $value && !$isUserLoggedIn) {
        include($includeFile);
        exit();
    }
}

checkStatusAndInclude("maintenance", "oui", "maintenance.php");
checkStatusAndInclude("ouverture", "oui", "ouverture.php");
?>
<!-------------------------------------------- chargement du PREHOME ---------------------------------------------------------------------------------------------->
<?php
function checkStatusAndInclude2($column, $value, $includeFile) {
    global $web, $isUserLoggedIn;

    $query = $web->prepare("SELECT $column FROM website_general");
    $query->execute();
    $status = $query->fetchColumn();
    $query->closeCursor();

    if ($status === $value && !$isUserLoggedIn) {
        // Vérifie si l'utilisateur a déjà vu la page "prehome"
        if (!isset($_SESSION['prehome_seen']) || $_SESSION['prehome_seen'] !== true) {
            include($includeFile);
            $_SESSION['prehome_seen'] = true;
            exit();
        }
    }
}

checkStatusAndInclude2("prehome", "oui", "prehome.php");
?>
<!--------------------------------------------/ chargement du PREHOME --------------------------------------------------------------------------------------------->
<!--------------------------------------------- chargement du BAN IP ----------------------------------------------------------------------------------------------->
<?php
$configFile = 'configuration/configuration.php';
require_once($configFile);

$ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

$query = $login->prepare("SELECT COUNT(*) FROM administration_ban_ip WHERE ip = ?;");
$query->execute([$ip]);
$row = $query->fetch();
$query->closeCursor();

if ($row[0] > 0) {
    // Rediriger ou refuser l'accès
    header('Location: ' . URL_SITE . 'ban.php');
    exit;
}
?>
<header>
<!---------------------------------------------------- top -------------------------------------------------------------------------------------------------------->
    <div id="top">
        <div class="container">
            <ul>
                <!-- NE PAS SUPPRIMER SINON PLUS RIEN NE SERA AFFICHER tous les textes sont dans le dossier /translation/fr en ou es -->
                <li><a href="javascript:void(0);" onclick="changeLanguage('fr');"><img src="img/flags/fr.png" alt="Français"></a></li>
                <li><a href="javascript:void(0);" onclick="changeLanguage('en');"><img src="img/flags/en.png" alt="English"></a></li>
                <li><a href="javascript:void(0);" onclick="changeLanguage('es');"><img src="img/flags/es.png" alt="Espagnol"></a></li>
                <!--/ NE PAS SUPPRIMER SINON PLUS RIEN NE SERA AFFICHER-->
                <?php
                if (isset($_SESSION['user']) && in_array($_SESSION['data']->guid, ADMIN_GUID)) {
                    echo '<li><a href="?page=adm_administration"><i class="fa-duotone fa-gear fa-spin"></i>' . $translations["MOTS_001"] . ' ' . $_SESSION['data']->admin . '</a></li>';
                }
                ?>
                <?php if(isset($_SESSION['user'])) { echo '<li><a href="?page=support"><i class="fa fa-ticket"></i> ' . $translations["MOTS_002"] . '</a></li>'; } ?>
                <?php if(isset($_SESSION['user'])) { echo '<li><a href="?page=bugtracker"><i class="fa fa-bug"></i> ' . $translations["MOTS_003"] . '</a></li>'; } ?>
            </ul>
            <?php
            if (isset($_SESSION['user'])) { ?>
            <div class="btn-group pull-right hidden-xs">
                <a href="?page=profile" class="btn"><?php  $query = $login->prepare("SELECT avatar, pseudo FROM world_accounts WHERE guid = :user_id;");
                    $query-> bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
                    $query->execute();

                    $result = $query->fetch(PDO::FETCH_ASSOC);

                    if ($result) {
                        $avatar = $result['avatar'];
                        $pseudo = $result['pseudo'];
                        // Afficher l'avatar, par exemple :
                        echo '<img src="img/avatar/' . $avatar . '" alt="Avatar width="29" height="29">';
                    } else {
                        // Gérer le cas où l'avatar n'a pas été trouvé (par exemple, afficher un avatar par défaut)
                        echo '<img src="img/avatar/avatar.jpg" alt="Avatar par défaut" width="29" height="29">';
                    }
//                    ?>&nbsp;&nbsp;<?php echo $pseudo;?></a><!--&nbsp;&nbsp;--><?php //echo $translations["MOTS_004"]; ?><!--</a>-->
<!--                <a href="?page=signin&ok=2" class="btn"><i class="fa-sharp fa-solid fa-right-from-bracket"></i>--><?php //echo $translations["MOTS_005"]; ?><!--</a>-->
            </div><?php
            } else { ?>
                <div class="btn-group pull-right hidden-xs">
                    <a href="?page=register" data-toggle="modal" class="btn"><i class="fa-solid fa-user-plus"></i> <?php echo $translations["MOTS_007"]; ?></a>
                    <a href="?page=signin" data-toggle="modal" class="btn"><i class="fa fa-lock"></i> <?php echo $translations["MOTS_008"]; ?></a>
                </div>
                <?php
            } ?>
        </div>
    </div>
    <!-- ./top -->
    <!-- header -->
    <div class="header">
        <div class="container">
            <span class="bar hide"></span>
            <a href="?page=index" class="logo pull-left"><img src="img/favi.ico">
                </i> <?php echo TITLE; ?></a>
            <ul class="list-inline pull-right hidden-xs">
                <?php
                if (VIEW_TAG_DISCORD === 'yes') {
                    echo '<li><a href="https://discord.gg/' . TAG_DISCORD . '" class="btn btn-social-icon btn-circle" data-toggle="tooltip" data-placement="bottom" title="Discord" target="_blank"><i class="fa-brands fa-discord"></i></a></li>';
                }

                if (VIEW_TAG_TWITTER === 'yes') {
                    echo '<li><a href="https://twitter.com/' . TAG_TWITTER . '" class="btn btn-social-icon btn-circle" data-toggle="tooltip" data-placement="bottom" title="Twitter" target="_blank"><i class="fa-brands fa-x-twitter"></i></a></li>';
                }

                if (VIEW_TAG_FACEBOOK === 'yes') {
                    echo '<li><a href="https://www.facebook.com/' . TAG_FACEBOOK . '" class="btn btn-social-icon btn-circle" data-toggle="tooltip" data-placement="bottom" title="Facebook" target="_blank"><i class="fa fa-facebook"></i></a></li>';
                }

                if (VIEW_TAG_GOOGLE === 'yes') {
                    echo '<li><a href="' . URL_GOOGLE . '" class="btn btn-social-icon btn-circle" data-toggle="tooltip" data-placement="bottom" title="Google" target="_blank"><i class="fa-brands fa-google"></i></a></li>';
                }
                ?>
                <li>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</li>
                <li><a href="?page=ban"><img src="img/bot/clic.GIF" /></a></li>
            </ul>
        </div>
    </div>
    <!-- ./header -->

    <!-- navigation -->
    <nav>
        <div class="container">
            <ul>
                <li><a href="?page=index" class="active"><i class="fa fa-home" ></i> <?php echo $translations["MOTS_006"]; ?></a></li>
                <!--<li><a href="?page=index">Accueil</a></li>-->
                <li><a href="?page=join"><?php echo $translations["MOTS_009"]; ?></a></li>
                <li class="dropdown">
                    <a href="#"><?php echo $translations["MOTS_010"]; ?><i class="ion-chevron-down"></i></a>
                    <ul class="dropdown-menu default">
                        <li><a href="?page=ladder"><?php echo $translations["MOTS_011"]; ?></a></li>
                        <li><a href="<?php echo URL_BARBOK; ?>" target="_blank">Barbok</a></li>
                    </ul>
                </li>
                <?php if(isset($_SESSION['user'])) {
                    echo '<li><a href="?page=viewdrop">'. $translations["MOTS_012"] .'</a></li>';
                } ?>
               <?php if(isset($_SESSION['user'])) {
                    echo '<li><a href="?page=loterie">'. $translations["MOTS_013"] .'</a></li>';
                } ?>
                    </ul>
            <!-- search -->
            <div class="pull-right">
                <ul>
                    <?php
                    if (isset($_SESSION['user'])) {
//                        echo '<li><a href="'.URL_BOUTIQUE.'"><img src="img/shop/caddie.svg" alt="Boutique" width="30" height="30"> Boutique</a></li>';
                        echo '<li><a href="'.URL_BOUTIQUE.'"><img src="img/shop/caddie.svg" alt="Boutique" width="30" height="30"> '. $translations["MOTS_014"] .'</a></li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- ./navigation -->
</header>

<div class="container">
    <!-- wrapper-->
    <div id="wrapper">
        <!-- Le reste du contenu de votre page -->
