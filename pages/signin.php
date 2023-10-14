<ol class="breadcrumb">
    <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
    <li class="active"><?php echo $translations["MOTS_008"]; ?></li>
</ol>

<br /> <br /> <br />
<div class="row">
    <div class="col-md-12">
        <!-- section -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-xs-12">
                <section class="section margin-top-20 margin-bottom-20 no-border">
                    <h2 class="page-header text-center no-margin-top"><i class="fa fa-lock"></i> <?php echo $translations["MOTS_195"]. TITLE; ?></h2>
                    <?php
                    // Vérifier si la connexion est autorisée
                    $query = $web->prepare("SELECT login FROM website_general;");
                    $query->execute();
                    $result = $query->fetch(PDO::FETCH_ASSOC);
                    $webAllowed = $result['login'];
                    $query->closeCursor();

                    //systeme flood
                    if (isset($_SESSION['last_failed_login_time'])) {
                        $currentTime = time();
                        $lastFailedLoginTime = $_SESSION['last_failed_login_time'];
                        $userIP = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];

                        // Vérifier si le temps écoulé depuis la dernière tentative est inférieur à 5 secondes
                        if ($currentTime - $lastFailedLoginTime < 5) {
                            // Incrémenter le compteur d'anti-flood
                            if (!isset($_SESSION['anti_flood_count'])) {
                                $_SESSION['anti_flood_count'] = 1;
                            } else {
                                $_SESSION['anti_flood_count']++;
                                if ($_SESSION['anti_flood_count'] >= 5) {
                                    $ip = $userIP; // Utiliser l'adresse IP brute
                                    $reason = "Anti-flood Login";
                                    $query = $login->prepare("INSERT INTO administration_ban_ip (ip, raison) VALUES (?, ?)");
                                    $query->execute([$ip, $reason]);
                                    // Mettez ici le code pour bannir l'IP si nécessaire
                                }
                            }
                        } else {
                            // Réinitialiser le compteur d'anti-flood car le délai est respecté
                            $_SESSION['anti_flood_count'] = 1;
                        }
                    }

                    // Enregistrez le temps de la tentative infructueuse actuelle
                    $_SESSION['last_failed_login_time'] = time();

                    // Vérifier si la connexion est autorisée ou non
                    if ($webAllowed == 'non') {

                        echo $translations["ALERTES_037"] . "<br />";
                        // Ajouter ici toute autre action à effectuer lorsque la connexion est bloquée
                        exit(); // Arrêter l'exécution du reste du code
                    }

                    if (isset($_GET['ok'])) {
                        $ok = $_GET['ok'];

                        // Vérifier à nouveau si la connexion est autorisée
                        $query = $web->prepare("SELECT login FROM website_general;");
                        $query->execute();
                        $result = $query->fetch(PDO::FETCH_ASSOC);
                        $webAllowed = $result['login'];
                        $query->closeCursor();

                        if ($webAllowed == 'non') {
                            echo $translations["ALERTES_038"] . "<br />";
                            exit(); // Arrêter l'exécution du reste du code
                        }

                        if ($ok == 1) {
                            echo $translations["SUCCESS_011"] . "<br />";
                            echo "<meta http-equiv='refresh' content='1; url=" . URL_SITE . "'> ";
                            
                            $currentDateTime = date('Y-m-d H:i:s');
                            $userAccount = $_SESSION['user'];  // Récupérer les informations de l'utilisateur de manière appropriée

                            $query = $login->prepare("UPDATE `world_accounts` SET `lastConnectDaySite` = :currentDateTime, `lastIPConnectionSite` = :ip WHERE `account` = :userAccount");
                            $query->bindParam(':currentDateTime', $currentDateTime);
                            $query->bindParam(':ip', $ip);
                            $query->bindParam(':userAccount', $userAccount);
                            $query->execute();

                        } else if ($ok == 2) {
                            $_SESSION = array();
                            session_destroy();
                            echo $translations["INFOS_005"] . "<br />";
                            echo "<meta http-equiv='refresh' content='1; url=" . URL_SITE . "'> ";
                        } else if ($ok == 3) {
                            echo $translations["ALERTES_039"] . "<br />";
                            echo "<meta http-equiv='refresh' content='8; url=" . URL_SITE . "'> ";
                        }else {
                            echo $translations["ALERTES_040"] . "<br />";
                            echo "<br />";
                        }
                    }
                    ?>
                    <form autocomplete="off" method="POST" action="#">
                            <div class="input-group margin-bottom-sm">
                                <span class="input-group-addon"><i class="fa fa-user fa-fw"></i></span>
                                <input class="form-control" type="text" name="username" placeholder="<?php echo $translations["MOTS_179"]; ?>">
                            </div>
                            <span class="help-block"></span>
                            <div class="input-group">
                                <span class="input-group-addon"><i class="fa fa-key fa-fw"></i></span>
                                <input class="form-control" type="password" name="password" placeholder="<?php echo $translations["MOTS_181"]; ?>">
                            </div>
                            <div class="margin-top-20">

                            <div class="checkbox pull-left no-padding no-margin-bottom margin-top-5">
                                <input type="checkbox" id="checkbox1">
                                <label for="checkbox1"><?php echo $translations["MOTS_196"]; ?></label>
                            </div>
                            <button type="submit" name="login" class="btn btn-success pull-right"><?php echo $translations["MOTS_008"]; ?></button>
                        </div>
                    </form>
                    <a href="?page=lost_password" class="text-dark margin-top-20 padding-top-20 help-block border-top-light btn-icon-right"><i class="fa fa-unlock"></i> <?php echo $translations["MOTS_116"]; ?></a>
                </section>
            </div>
        </div>
    </div>
</div>

<br /> <br /> <br /> <br />
<!-- ./leftside -->
