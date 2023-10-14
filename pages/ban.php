<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_069"]; ?></li>
    </ol>
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $configFile = 'configuration/configuration.php';
    require_once($configFile);

    $ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

    $raison = "Aspiration du site";

    $query = $login->prepare("INSERT INTO administration_ban_ip (ip, raison) VALUES (?, ?)");
    $query->execute([$ip, $raison]);

    echo "<p>" . $translations["MOTS_067"] . "</p>";
    echo "<p>" . $translations["MOTS_068"] . " " . $ip . "</p>";

    ?>
    <script>
        setTimeout(function() {
            window.location.href = "<?php echo URL_SITE; ?>";
        }, 4000); // Redirection vers la page d'accueil après 4 secondes
    </script>
</div>
