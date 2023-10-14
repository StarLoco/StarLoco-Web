<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_212"]; ?></li>
    </ol>
    <?php
       /* ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);*/

        if(isset($_GET['ok'])) {
            if(isset($_SESSION['id'])) {
                $query = $login->prepare('SELECT votes, heurevote, points, totalVotes FROM world_accounts WHERE guid = ' . $_SESSION['id'] . ';');
                $query->execute();
                $row = $query->fetch();
                $query->closeCursor();

                date_default_timezone_set("Europe/Paris");

                $curVotes = $row['votes'];
                $totalVotes = $row['totalVotes'] + 1;

                $curDate = time();
                $lastDate = $row['heurevote'];
                $diffDate = $curDate - $lastDate;

                if(!CLOUDFLARE_ENABLE) $ip = $_SERVER['REMOTE_ADDR']; // no cloudflare
                else $ip = $_SERVER["HTTP_CF_CONNECTING_IP"]; // with cloudflare

                $query = $connection->prepare("SELECT * FROM `website_users_votes` WHERE ip LIKE '" . $ip . "';");
                $query->execute();
                $row = $query->fetch();
                $query->closeCursor();

                $ipDate = $row['date'];

                if (!($diffDate > 60 * 60 * 3)) {
                    $remainingTime = 60 * 60 * 3 - $diffDate;
                    $remainingHours = floor($remainingTime / 3600);
                    $remainingMinutes = floor(($remainingTime % 3600) / 60);
                    $remainingSeconds = $remainingTime % 60;


                    echo '<div class="alert alert-danger no-border-radius" role="alert">
              <center>Il te faut attendre encore ' . $remainingHours . ' heure(s), ' . $remainingMinutes . ' minute(s), ' . $remainingSeconds . ' seconde(s) avant de pouvoir voter !</center></div>';
                } else if (!($curDate - $ipDate > 60 * 60 * 3)) {
                    $remainingTime = 60 * 60 * 3 - ($curDate - $ipDate);
                    $remainingHours = floor($remainingTime / 3600);
                    $remainingMinutes = floor(($remainingTime % 3600) / 60);
                    $remainingSeconds = $remainingTime % 60;

                    echo '<div class="alert alert-danger no-border-radius" role="alert">
              <center>Il te faut attendre encore ' . $remainingHours . ' heure(s), ' . $remainingMinutes . ' minute(s), ' . $remainingSeconds . ' seconde(s) avant de pouvoir voter !</center></div>';
                } else {
                    // Récupérez la valeur de $curPoints depuis la base de données ou autre source
                    $query = $login->prepare('SELECT points FROM world_accounts WHERE guid = ' . $_SESSION['id'] . ';');
                    $query->execute();
                    $row = $query->fetch();
                    $query->closeCursor();

                    // Assurez-vous que la colonne 'points' existe dans votre table et a le bon nom
                    $curPoints = $row['points'];

                    $query = $login->prepare("UPDATE world_accounts SET votes = " . ($curVotes + 1) . ", heurevote = '" . $curDate . "', totalVotes = " . $totalVotes . ", points = " . ($curPoints + PTS_PER_VOTE) . " WHERE guid = " . $_SESSION['id'] . ";");
                    $query->execute();
                    $query->closeCursor();
                    $query = $connection->prepare("DELETE FROM `website_users_votes` WHERE ip = '" . $ip . "';");
                    $query->execute();
                    $query->closeCursor();
                    $query = $connection->prepare("INSERT INTO `website_users_votes` (ip, date) VALUES ('" . $ip . "', '" . $curDate . "');");
                    $query->execute();
                    $query->closeCursor();

                    echo $translations["MOTS_213"];
                    if (QUEL_VOTE === 'rpg') {
                        echo "<meta http-equiv='refresh' content='1; url=" . URL_RPG . "'>";
                    } else {
                        echo "<meta http-equiv='refresh' content='1; url=" . URL_SERVEURPRIVE . "'>";
                    }
                }
            }
        } else { ?>
            <?php
            if(isset($_SESSION['id'])) {
                ?>
                <!-- Bouton de vote -->
                <?php echo $translations["MOTS_214"] . "<br/>"; ?>
                <?php
            }
            ?>
        <?php
        if(isset($_SESSION['id'])) {
            // Récupérez la valeur de $curPoints depuis la base de données ou autre source
            $query = $login->prepare('SELECT points FROM world_accounts WHERE guid = ' . $_SESSION['id'] . ';');
            $query->execute();
            $row = $query->fetch();
            $query->closeCursor();

            // Assurez-vous que la colonne 'points' existe dans votre table et a le bon nom
            $curPoints = $row['points'];
            ?>
            <hr />
            <center>
                <img src="img/devtool/cadeau.png"> <?php echo $translations["MOTS_215"]; ?> <?php echo $curPoints; ?> <?php  echo NOM_POINT; ?>.<br/>
                <?php echo $translations["MOTS_216"]; ?> <?php echo PTS_PER_VOTE . ' ' . NOM_POINT; ?> !<br><hr />
                <img src="img/devtool/infos.png"> <?php echo $translations["MOTS_217"]; ?><br><hr />
                <img src="img/devtool/ip.png"> <?php echo $translations["MOTS_218"]; ?>
            </center>
        <?php
        } else { ?>
                <center><img src="img/illu_block_jeux_en_ligne.png" height="200px" width="auto"></center>
            <div class="alert alert-info no-border-radius" role="alert">
                <?php echo $translations["MOTS_219"]; ?> <?php echo PTS_PER_VOTE ." " . NOM_POINT ." " .$translations["MOTS_220"]; ?>
                <?php echo $translations["MOTS_221"]; ?>
                <?php echo $translations["MOTS_222"]; ?>
                </div>
            <?php
            }
        }
        ?>
    </div>
    <!-- ./leftside -->
