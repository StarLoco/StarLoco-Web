<div id="fb-root"></div>
<!-- sidebar -->
<div class="sidebar">
    <a href="<?php echo URL_SITE . '?page=vote'; ?>" class="btn btn-warning btn-block btn-md btn-bold margin-bottom-15"><i
                class="fa fa-gift"></i> <?php echo $translations["MOTS_021"]; ?> <?php echo NOM_POINT; ?>.</a>

    <!-- section mon compte-->
    <style>
        /* CSS pour la boîte contenant l'avatar et les informations */
        .facebook-like-box2 {
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            display: flex; /* Utiliser flexbox pour la mise en page */
            align-items: center; /* Aligner les éléments verticalement au centre */
            margin-top: -11px;
        }

        /* CSS pour l'image de l'avatar */
        .avatar img {
            border-radius: 50%; /* Pour créer une forme de cercle */
            width: 80px;
            height: 80px;
            border: 3px solid #fff; /* Ajout d'une bordure blanche autour de l'avatar */
            margin-left: -16px;
            margin-top: -48px;
        }

        /* CSS pour la section des détails de l'utilisateur */
        .user-details {
            flex-grow: 1; /* Permet au contenu de prendre tout l'espace disponible */
            text-align: left;
            padding-left: 20px; /* Espacement à gauche pour les détails de l'utilisateur */
        }

        /* CSS pour le nom d'utilisateur */
        .user-details .username {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            margin-left: -10px;
        }

        /* CSS pour les points */
        .user-details .points {
            font-size: 18px;
            color: #777;
            margin-left: -99px;
            margin-top: 12px;
        }
        .white-text {
            color: white !important;
        }

    </style>
    <?php
    if (isset($_SESSION['user'])) { ?>
        <div class="section section-default">
            <div class="title dark-grey no-margin padding-10-15">
                <i class="fa-light fa-wreath-laurel"></i> <?php echo $translations["MOTS_004"];?>
            </div>
            <?php
            if (isset($_SESSION['user'])) {
                $query = $login->prepare("SELECT avatar, pseudo, points FROM world_accounts WHERE guid = :user_id");
                $query-> bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
                $query->execute();

                $result = $query->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    $avatar = $result['avatar'];
                    $pseudo = $result['pseudo'];
                    $points = $result['points'];
                }
            }
            ?>
            <div class="padding-15">
                <ul class="box no-padding">
                    <li class="no-padding-top no-padding-bottom"><br/>
                        <div class="facebook-like-box2">
                                <div class="avatar">
                                    <img src="img/avatar/<?php echo $avatar; ?>" alt="Avatar" width="80" height="80">
                                </div>
                                <div class="user-details">
                                    <div class="username">
                                        <?php echo $pseudo; ?>
                                    </div>
                                    <div style="text-align: center; margin-left: -10px;">
                                        <a href="?page=profile" class="btn btn-primary white-text">Gestion de compte</a>
                                    </div>
                                    <table>
                                    <div class="points" title="Bullions">
                                        <td><?php echo $points; ?> <img src="img/devtool/jeton.png" style="width: auto;height: 20px;" title ="Bullions"></td>
                                    </div>
                                    </table>
                                </div>
                        </div>
                        <div style="text-align: center; margin-top: 5px;">
                            <a href="?page=signin&ok=2" class="btn btn-red white-text" style="width: 100%;"><?php echo $translations["MOTS_005"]; ?></a>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
            <?php
    } ?>
    <!-- ./section mon compte-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section panier-->
    <?php
    if (isset($_GET['page']) && ($_GET['page'] == 'shop' || $_GET['page'] == 'buy')) {
        //affiche ques sur ces pages
        ?>
        <div class="section section-default">
            <div class="title dark-grey no-margin padding-10-15">
                <i class="fa fa-cart-arrow-down"></i> <?php echo $translations["MOTS_022"]; ?>
            </div>
            <div class="padding-15">
                <ul class="box no-padding">
                    <li class="no-padding-top no-padding-bottom"><br/>
                        <div class="facebook-like-box">
                            <?php
                            if (isset($_GET['page']) && ($_GET['page'] == 'shop' || $_GET['page'] == 'buy' || $_GET['page'] == 'panier')) {
                                // Vérifier si l'utilisateur a demandé d'ajouter un élément au panier
                                if (isset($_GET['add_to_cart']) && is_numeric($_GET['add_to_cart'])) {
                                    $itemToAdd = $_GET['add_to_cart'];

                                    // Requête pour obtenir les détails de l'objet à ajouter au panier
                                    $itemQuery = $web->prepare("SELECT * FROM `website_shop_objects_templates` WHERE `id` = :item_id;");
                                    $itemQuery->bindParam(':item_id', $itemToAdd, PDO::PARAM_INT);
                                    $itemQuery->execute();
                                    $item = $itemQuery->fetch(PDO::FETCH_OBJ);
                                    $itemQuery->closeCursor();

                                    // Ajout de l'objet au panier
                                    if (isset($_SESSION['cart'])) {
                                        $_SESSION['cart'][] = array(
                                            'template' => $item->id,
                                            'name' => $item->name,
                                            'server' => $server,
                                        );
                                    } else {
                                        $_SESSION['cart'] = array(
                                            array(
                                                'template' => $item->id,
                                                'name' => $item->name,
                                                'server' => $server,
                                            )
                                        );
                                    }
                                }

                                // Affichage du panier
                                $totalPrice = 0; // Initialiser le total

                                if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                                    echo '<div class="panier" style="font-size:14px">';

                                    foreach ($_SESSION['cart'] as $item) {
                                        // Requête pour obtenir le prix et la réduction à partir de website_shop_objects en utilisant le nom de l'objet
                                        $priceQuery = $connection->prepare("SELECT price, reduc FROM website_shop_objects WHERE name = :item_name;");
                                        $priceQuery->bindParam(':item_name', $item['name'], PDO::PARAM_STR);
                                        $priceQuery->execute();
                                        $result = $priceQuery->fetch(PDO::FETCH_ASSOC);
                                        $price = $result['price'];
                                        $reduction = $result['reduc'];
                                        $priceQuery->closeCursor();

                                        echo '<img src="img/shop/blue.png"/> - <b>' . $item['name'] . ' <a href="?page=shop&server=' . $server . '&remove_template=' . $item['template'] . '"><img src="img/devtool/close.png" alt="Supprimer" /></a>';
                                        echo '<br>';

                                        // Calculer le prix avec réduction pour cet élément
                                        $priceWithReduction = $price - ($price * ($reduction / 100));

                                        // Ajouter le prix réduit de l'élément au total
                                        $totalPrice += $priceWithReduction;
                                    }

                                    // Afficher le total
                                    echo '<hr/>';
                                    echo '<b>' . $translations["MOTS_023"] . ' ' . $totalPrice . ' ' . NOM_POINT . '</b>';


                                    echo '</div>';
                                } else {
                                    echo '<div class="panier" align="center" style="font-size:14px"><b><font color="#3B71B6">' . $translations["MOTS_024"] . '</font></b></div>';
                                    echo '<br/>';
                                }

                                // Vider le panier
                                if (isset($_GET['clear_cart']) && $_GET['clear_cart'] == "true") {
                                    unset($_SESSION['cart']);
                                    echo '<script>window.location.href = "?page=shop";</script>';
                                    exit();
                                }

                                // Vérifier si l'utilisateur a demandé de supprimer un élément du panier
                                if (isset($_GET['remove_template']) && is_numeric($_GET['remove_template'])) {
                                    $templateToRemove = $_GET['remove_template'];

                                    if (isset($_SESSION['cart'])) {
                                        foreach ($_SESSION['cart'] as $key => $item) {
                                            if ($item['template'] == $templateToRemove) {
                                                unset($_SESSION['cart'][$key]);
                                                break;
                                            }
                                        }
                                        // Réorganiser les clés du tableau pour éviter les trous dans l'index
                                        $_SESSION['cart'] = array_values($_SESSION['cart']);
                                    }
                                    // Rediriger vers la même page pour rafraîchir la page après avoir supprimé un élément du panier
                                    echo '<script>window.location.href = "?page=shop";</script>';
                                }
                            }
                            ?>
                        </div>
                    </li>
                </ul>
            </div>
            <a href="?page=shop&clear_cart=true"><i class="fa fa-trash-o"></i> <?php echo $translations["MOTS_025"]; ?></a><a
                    href="?page=panier" style="float: right;"><i class="fa fa-shopping-basket"></i> <?php echo $translations["MOTS_026"]; ?></a>
            <br>
        </div>
        <?php
    }
    ?>
    <!-- ./section panier -->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section etat server-->
    <?php
    //                if (!isset($_GET['page']) || $_GET['page'] === 'index' || $_GET['page'] === 'join' || $_GET['page'] === 'viewdrop' || $_GET['page'] === 'panier') {
    if (!isset($_GET['page']) || $_GET['page'] !== 'ladder' && $_GET['page'] !== 'loterie' && $_GET['page'] !== 'shop' && $_GET['page'] !== 'vote') {
        // Le bloc etat des serveurs sera cacher sur ces pages
        ?>
        <div class="section section-default">
            <div class="title dark-grey no-margin padding-10-15">
                <i class="fa-duotone fa-server"></i> <?php echo $translations["MOTS_027"]; ?>
            </div>
            <div class="tab-content padding-15">
                <!-- SERVER 0 CONNECTION-->
                <?php
                if (defined('AFFICHER_SERVEUR_0') && AFFICHER_SERVEUR_0 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_1 est défini à 'yes'
                    ?>
                    <ul id="Connexion" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_0; ?>"
                                                                  height="55"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_0; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?>
                                    <?php
                                    ini_set('display_errors', 1);
                                    ini_set('display_startup_errors', 1);
                                    error_reporting(E_ALL);

                                    $configFile = 'configuration/configuration.php';
                                    require_once($configFile);

                                    $query = $login->prepare('SELECT COUNT(*) FROM world_accounts WHERE logged = 1;');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                </div>
                                <?php
                                ini_set('display_errors', 1);
                                ini_set('display_startup_errors', 1);
                                error_reporting(E_ALL);
                                $configFile = 'configuration/configuration.php';
                                require_once($configFile);

                                if (checkState(IP_SERVEUR_0, PORT_SERVEUR_0)) $state = "success"; else $state = "danger"; ?>

                                <span class="label label-<?php echo $state; ?> pull-right">
										<?php
                                        if ($state == "success")
                                            echo '<i class="glyphicon glyphicon-ok"></i>';
                                        else
                                            echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
									</span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 1-->
                <?php
                if (defined('AFFICHER_SERVEUR_1') && AFFICHER_SERVEUR_1 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_1 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_1; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_1; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_1 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_1 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_1, PORT_SERVEUR_1)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 2-->
                <?php
                if (defined('AFFICHER_SERVEUR_2') && AFFICHER_SERVEUR_2 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_2 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_2; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_2; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_2 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_2 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_2, PORT_SERVEUR_2)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 3-->
                <?php
                if (defined('AFFICHER_SERVEUR_3') && AFFICHER_SERVEUR_3 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_3 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="<?php echo IMAGE_SERVEUR_3; ?>" height="70"/>
                            </div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_3; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_3 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_3 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_3, PORT_SERVEUR_3)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 4-->
                <?php
                if (defined('AFFICHER_SERVEUR_4') && AFFICHER_SERVEUR_4 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_4 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_4; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_4; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_4 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_4 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_4, PORT_SERVEUR_4)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 5-->
                <?php
                if (defined('AFFICHER_SERVEUR_5') && AFFICHER_SERVEUR_5 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_5 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_5; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_5; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_5 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_5 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_5, PORT_SERVEUR_5)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 6-->
                <?php
                if (defined('AFFICHER_SERVEUR_6') && AFFICHER_SERVEUR_6 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_6 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_6; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_6; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_6 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_6 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_6, PORT_SERVEUR_6)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 7-->
                <?php
                if (defined('AFFICHER_SERVEUR_7') && AFFICHER_SERVEUR_7 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_7 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_7; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_7; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_7 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_7 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_7, PORT_SERVEUR_7)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 8-->
                <?php
                if (defined('AFFICHER_SERVEUR_8') && AFFICHER_SERVEUR_8 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_8 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_8; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_8; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_8 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_8 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_8, PORT_SERVEUR_8)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 9-->
                <?php
                if (defined('AFFICHER_SERVEUR_9') && AFFICHER_SERVEUR_9 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_9 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_9; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_9; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_9 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_9 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_9, PORT_SERVEUR_9)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
                <!-- SERVER 10-->
                <?php
                if (defined('AFFICHER_SERVEUR_10') && AFFICHER_SERVEUR_10 === 'yes') {
                    // Le contenu HTML à afficher si AFFICHER_SERVEUR_10 est défini à 'yes'
                    ?>
                    <ul id="Test" class="tab-pane active clearfix box">
                        <!-- row -->
                        <li class="row">
                            <div class="col-md-2 no-padding"><img src="img/servers/<?php echo IMAGE_SERVEUR_10; ?>"
                                                                  height="70"/></div>
                            <div class="details col-md-10 no-padding-right">
                                <div class="pull-left">
                                    <h5><a style="color: #2a5d9f">&ensp;<?php echo $translations["MOTS_035"]; ?> <?php echo SERVEUR_10; ?></a></h5>
                                    &ensp;&ensp;<?php echo $translations["MOTS_028"]; ?> <?php
                                    $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE logged = 1 AND server = ' . ID_SERVEUR_10 . ';');
                                    $query->execute();
                                    $row = $query->fetch();
                                    $query->closeCursor();
                                    echo $row['COUNT(*)'];
                                    ?>
                                    <div class="info">
                                        <?php
                                        $query = $login->prepare('SELECT * FROM world_servers WHERE `id` = ' . ID_SERVEUR_10 . ';');
                                        $query->execute();
                                        $query->setFetchMode(PDO::FETCH_OBJ);
                                        $server = $query->fetch();
                                        $uptime = convertTimestampToUptime($server->uptime);
                                        $query->closeCursor();
                                        echo "&ensp;&ensp;&ensp;Uptime : " . $uptime;
                                        ?>
                                    </div>
                                </div>
                                <?php if (checkState(IP_SERVEUR_10, PORT_SERVEUR_10)) $state = "success"; else $state = "danger"; ?>
                                <span class="label label-<?php echo $state; ?> pull-right">
                    <?php
                    if ($state == "success")
                        echo '<i class="glyphicon glyphicon-ok"></i>';
                    else
                        echo '<i class="glyphicon glyphicon-remove"></i>'; ?>
                </span>
                            </div>
                        </li>
                    </ul>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>
    <!-- ./section etat serveur-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section statistique-->
    <?php
    if (!isset($_GET['page']) || $_GET['page'] !== 'ladder' && $_GET['page'] !== 'loterie' && $_GET['page'] !== 'shop' && $_GET['page'] !== 'vote' && $_GET['page'] !== 'adm_administration') {
        // Le bloc de statistiques ne sera pas aficher sur ces pages
        ?>
        <div class="section section-default">
            <div class="title dark-grey no-margin padding-10-15">
                <i class="ion-podium"></i> <?php echo $translations["MOTS_029"] . " Osiris";?>
            </div>
            <div class="padding-15">
                <ul class="box no-padding">
                    <li class="no-padding-top no-padding-bottom"><br/>
                        <div class="facebook-like-box">
                            <?php
                            $query = $login->prepare('SELECT COUNT(DISTINCT lastIPConnectionSite) AS total FROM world_accounts;');
                            $query->execute();
                            $rows = $query->fetchAll();
                            $query->closeCursor();
                            $inscris = count($rows);

                            $query = $login->prepare('SELECT COUNT(*) FROM world_accounts;');
                            $query->execute();
                            $row = $query->fetch();
                            $query->closeCursor();
                            $inscris2 = $row['COUNT(*)'];

                            $query = $login->prepare('SELECT COUNT(*) FROM `world_guilds`;');
                            $query->execute();
                            $row = $query->fetch();
                            $query->closeCursor();
                            $guildes = $row['COUNT(*)'];

                            $query = $login->prepare('SELECT COUNT(*) FROM `world_objects`;');
                            $query->execute();
                            $row = $query->fetch();
                            $query->closeCursor();
                            $objets = $row['COUNT(*)'];

                            $query = $login->prepare('SELECT COUNT(*) FROM world_players;');
                            $query->execute();
                            $row = $query->fetch();
                            $query->closeCursor();
                            $personnages = $row['COUNT(*)']; ?>

                            <h4><?php echo $translations["MOTS_030"]; ?>&nbsp; : <?php echo $inscris; ?></h4>
                            <br/><h4><?php echo $translations["MOTS_031"]; ?>&nbsp; : <?php echo $inscris2; ?></h4>
                            <br/><h4><?php echo $translations["MOTS_032"]; ?>&nbsp; : <?php echo $personnages; ?></h4>
                            <br/><h4><?php echo $translations["MOTS_033"]; ?>&nbsp; : <?php echo $guildes; ?></h4>

                        </div>
                        <br/></li>
                </ul>
            </div>
        </div>
        <?php
    }
    ?>
    <!-- ./section statistique-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section top3 joueur PVM-->
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'index' || $_GET['page'] === 'ladder') {
        //affiche que sur la page index et ladder
        ?>
        <div class="section carousel-tab section-info">
            <div class="title no-margin">
                <i class="ion-ribbon-a"></i> <?php echo $translations["MOTS_036"]; ?>
            </div>
            <div class="jcarousel" data-jcarousel="true" data-jcarouselautoscroll="true">
                <ul class="box" style="left: 0px; top: 0px;">
                    <?php

                    $query = $login->prepare('SELECT * FROM world_players WHERE groupe = 0 ORDER BY xp DESC LIMIT 0, 3;');
                    $query->execute();
                    $query->setFetchMode(PDO:: FETCH_OBJ);

                    $i = 1;
                    $configFile = 'configuration/configuration.php';
                    require_once($configFile);

                    while ($row = $query->fetch()) {
                        $name = $row->name;
                        $class = $row->class;
                        $level = $row->level;
                        $sexe = $row->sexe;

                        $map = $jiva->prepare('SELECT id, mappos FROM maps WHERE id = ' . $row->map . ';');
                        $map->execute();
                        $map->setFetchMode(PDO:: FETCH_OBJ);
                        $map_row = $map->fetch();
                        $map->closeCursor();

                        $mappos = explode(",", $map_row->mappos)[2];

                        $sub = $web->prepare('SELECT id, name FROM subarea_data WHERE id = ' . $mappos . ';');
                        $sub->execute();
                        $sub->setFetchMode(PDO:: FETCH_OBJ);
                        $sub_row = $sub->fetch();
                        $sub->closeCursor();


                        $color = "";
                        switch ($i) {
                            case 1:
                                $color = "F5C553";
                                echo '<li class="no-padding"><h4 class="padding-15"><a href="#"><img src="img/ladder/trophy_1.png"></i>
                                     ' . $i . '<sup>er</sup> ' . $translations["MOTS_037"] . ' ' . $name . '</a></h4><div class="padding-15"><p>
                                     ' . $translations["MOTS_038"] . ' ' . convertClassIdToString($class, $sexe) . ', ' . $translations["MOTS_039"] . ' ' . $level . ' ' . $translations["MOTS_040"] . '';

                                if (property_exists($row, 'showOrHidePos') && $row->showOrHidePos) {
                                    echo '.';
                                } else {
                                    echo ' ' . $translations["MOTS_041"] . ' ' . $sub_row->name . '.</p></div></li>';
                                }
                                break;
                            case 2:
                                $color = "D1D1E3";
                                echo '<li class="no-padding"><h4 class="padding-15"><a href="#"><a href="#"><img src="img/ladder/trophy_2.png"></i>
                                        ' . $i . '<sup>er</sup> ' . $translations["MOTS_037"] . ' ' . $name . '</a></h4><div class="padding-15"><p>
                                       ' . $translations["MOTS_038"] . ' ' . convertClassIdToString($class, $sexe) . ', ' . $translations["MOTS_039"] . ' ' . $level . ' ' . $translations["MOTS_042"] . '';

                                if (property_exists($row, 'showOrHidePos') && $row->showOrHidePos) {
                                    echo '';
                                } else {
                                    echo ' ' . $translations["MOTS_043"] . ' ' . $sub_row->name . '.</p></div></li>';
                                }
                                break;
                            case 3:
                                $color = "E48644";
                                echo '<li class="no-padding"><h4 class="padding-15"><a href="#"><a href="#"><img src="img/ladder/trophy_3.png"></i>
                                        ' . $i . '<sup>er</sup> ' . $translations["MOTS_037"] . ' ' . $name . '</a></h4><div class="padding-15"><p>
                                        ' . $translations["MOTS_038"] . ' ' . convertClassIdToString($class, $sexe) . ', ' . $translations["MOTS_039"] . ' ' . $level . ' ' . $translations["MOTS_044"] . '';

                                if (property_exists($row, 'showOrHidePos') && $row->showOrHidePos) {
                                    echo '';
                                } else {
                                    echo ' ' . $translations["MOTS_043"] . ' ' . $sub_row->name . '.</p></div></li>';
                                }
                                break;

                        }

                        $i++;
                    }
                    $query->closeCursor();
                    ?>
                </ul>
            </div>
            <div class="jcarousel-pagination" data-jcarouselpagination="true"><a href="#1" class="active">1</a><a
                        href="#2" class="">2</a></div>
        </div>
        <?php
    }
    ?>
    <!-- ./section top3 joueur PVM-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section top3 PVP-->
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'ladder') {
        //affiche que sur la page ladder
        ?>
        <div class="section carousel-tab section-info">
            <div class="title no-margin">
                <i class="fa-duotone fa-helmet-battle"></i> <?php echo $translations["MOTS_238"]; ?>
            </div>
            <div class="jcarousel" data-jcarousel="true" data-jcarouselautoscroll="true">
                <ul class="box" style="left: 0px; top: 0px;">
                    <?php
                    $query = $login->prepare("SELECT name FROM world_players ORDER BY honor DESC;");
                    $query->execute();
                    $count = $query->rowCount();
                    $query->setFetchMode(PDO:: FETCH_OBJ);
                    $i = 1;

                    $configFile = 'configuration/configuration.php';
                    require_once($configFile);

                    while ($row = $query->fetch()) {

                        $color = "";
                        switch ($i) {
                            case 1:
                                $color = "F5C553";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_1.png"></i>
                                     ' . $i . '<sup>er</sup> ' . $translations["MOTS_037"] . ' ' . $row->name . '</a></h4><div class="padding-15"><p>
                                     ' . $translations["MOTS_235"] . ' ';
                                break;
                            case 2:
                                $color = "D1D1E3";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_2.png"></i>
                                        ' . $i . '<sup>eme</sup> ' . $translations["MOTS_037"] . ' ' . $row->name . '</a></h4><div class="padding-15"><p>
                                      ' . $translations["MOTS_236"] . ' ';
                                break;
                            case 3:
                                $color = "E48644";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_3.png"></i>
                                        ' . $i . '<sup>eme</sup> ' . $translations["MOTS_037"] . ' ' . $row->name . '</a></h4><div class="padding-15"><p>
                                        ' . $translations["MOTS_237"] . ' ';
                                break;
                        }
                        $i++;
                    }
                    $query->closeCursor();
                    ?>
                </ul>
            </div>
            <div class="jcarousel-pagination" data-jcarouselpagination="true"><a href="#1" class="active">1</a><a
                        href="#2" class="">2</a></div>
        </div>
        <?php
    }
    ?>
    <!-- ./section top3 PVP-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section top3 guildes-->
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'ladder') {
        //affiche que sur la page ladder
        ?>
        <div class="section carousel-tab section-info">
            <div class="title no-margin">
                <i class="fa-solid fa-user-shield"></i> <?php echo $translations["MOTS_233"]; ?>
            </div>
            <div class="jcarousel" data-jcarousel="true" data-jcarouselautoscroll="true">
                <ul class="box" style="left: 0px; top: 0px;">
                    <?php
                    $query = $login->prepare("SELECT name, xp FROM world_guilds ORDER BY xp DESC;");
                    $query->execute();
                    $count = $query->rowCount();
                    $query->setFetchMode(PDO:: FETCH_OBJ);
                    $i = 1;

                    $configFile = 'configuration/configuration.php';
                    require_once($configFile);

                    while ($row = $query->fetch()) {

                        $color = "";
                        switch ($i) {
                            case 1:
                                $color = "F5C553";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_1.png"></i>
                                     ' . $i . '<sup>ere</sup> ' . $translations["MOTS_234"] . ' ' . $row->name . '</a></h4><div class="padding-15"><p>
                                     ' . $translations["MOTS_235"] . ' ';
                                break;
                            case 2:
                                $color = "D1D1E3";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_2.png"></i>
                                        ' . $i . '<sup>eme</sup> ' . $translations["MOTS_234"] . ' ' . $row->name . '</a></h4><div class="padding-15"><p>
                                      ' . $translations["MOTS_236"] . ' ';
                                break;
                            case 3:
                                $color = "E48644";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_3.png"></i>
                                        ' . $i . '<sup>eme</sup> ' . $translations["MOTS_234"] . ' ' . $row->name . '</a></h4><div class="padding-15"><p>
                                        ' . $translations["MOTS_237"] . ' ';
                                break;
                        }
                        $i++;
                    }
                    $query->closeCursor();
                    ?>
                </ul>
            </div>
            <div class="jcarousel-pagination" data-jcarouselpagination="true"><a href="#1" class="active">1</a><a
                        href="#2" class="">2</a></div>
        </div>
        <?php
    }
    ?>
    <!-- ./section top3 guildes-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section top3 votes-->
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'ladder' || $_GET['page'] === 'vote') {
        //affiche que sur la page ladder
        ?>
        <div class="section carousel-tab section-info">
            <div class="title no-margin">
                <i class="fa-solid fa-trophy"></i> <?php echo $translations["MOTS_045"]; ?>
            </div>
            <div class="jcarousel" data-jcarousel="true" data-jcarouselautoscroll="true">
                <ul class="box" style="left: 0px; top: 0px;">
                    <?php
                    $query = $login->prepare("SELECT pseudo, votes, totalVotes FROM world_accounts ORDER BY totalVotes DESC;");
                    $query->execute();
                    $count = $query->rowCount();
                    $query->setFetchMode(PDO:: FETCH_OBJ);
                    $i = 1;

                    $configFile = 'configuration/configuration.php';
                    require_once($configFile);

                    while ($row = $query->fetch()) {

                        $color = "";
                        switch ($i) {
                            case 1:
                                $color = "F5C553";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_1.png"></i>
                                     ' . $i . '<sup>er</sup> ' . $translations["MOTS_037"] . ' ' . $row->pseudo . '</a></h4><div class="padding-15"><p>
                                     ' . $translations["MOTS_046"] . ' ';
                                break;
                            case 2:
                                $color = "D1D1E3";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_2.png"></i>
                                        ' . $i . '<sup>eme</sup> ' . $translations["MOTS_037"] . ' ' . $row->pseudo . '</a></h4><div class="padding-15"><p>
                                      ' . $translations["MOTS_231"] . ' ';
                                break;
                            case 3:
                                $color = "E48644";
                                echo '<li class="no-padding"><h4 class="padding-15"><img src="img/ladder/trophy_3.png"></i>
                                        ' . $i . '<sup>eme</sup> ' . $translations["MOTS_037"] . ' ' . $row->pseudo . '</a></h4><div class="padding-15"><p>
                                        ' . $translations["MOTS_232"] . ' ';
                                break;
                        }
                        $i++;
                    }
                    $query->closeCursor();
                    ?>
                </ul>
            </div>
            <div class="jcarousel-pagination" data-jcarouselpagination="true"><a href="#1" class="active">1</a><a
                        href="#2" class="">2</a></div>
        </div>
        <?php
    }
    ?>
    <!-- ./section top3 votes-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- Section Top SERVEURPRIVE -->
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'ladder' || $_GET['page'] === 'adm_administration' || $_GET['page'] === 'vote') {
        // Affiche uniquement sur la page ladder
        ?>
        <div class="section section-default">
            <div class="title dark-grey no-margin padding-10-15">
                <i class="ion-podium"></i> <?php echo $translations["MOTS_223"]; ?>
            </div>
            <div class="padding-15">
                <ul class="box no-padding">
                    <li class="no-padding-top no-padding-bottom">
                        <br/>
                        <div class="facebook-like-box">
                            <?php
                            $server_token = TOKEN_SERVEURPRIVE; // Remplacez "TOKEN" par le token de votre serveur.
                            $json = file_get_contents("https://serveur-prive.net/api/v1/servers/$server_token/statistics");
                            $json_data = json_decode($json);

                            if ($json_data->success) {
                                ?>
                                <p>Position: <?php echo $json_data->data->position; ?></p>
                                <p>Votes: <?php echo $json_data->data->votes_count; ?></p>
                                <p>Clicks: <?php echo $json_data->data->clicks_count; ?></p>
                                <p>Commentaires: <?php echo $json_data->data->comments_count; ?></p>
                                <p>Note: <?php echo $json_data->data->rating; ?></p>
                                <?php
                                // Vous pouvez utiliser les variables suivantes :
                                $datas = $json_data->data; // Cette variable contiendra les données JSON renvoyées par l'API.
                                $datas->position; // Correspond à la position du serveur dans le classement du mois
                                $datas->votes_count; // Correspond au nombre de votes du serveur
                                $datas->clicks_count; // Correspond au nombre de clics du serveur
                                $datas->comments_count; // Correspond au nombre de commentaires du serveur
                                $datas->rating; // Correspond à la note du serveur sur 5
                            } else {
                                echo 'Le Token spécifié est incorrect';
                            }
                            ?>
                        </div>
                        <br/>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }
    ?>
    <!-- ./ Section Top Votes SERVEURPRIVE -->

    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- .section loterie-->
    <style>
        .probabilite-indicateur {
            position: absolute;
            top: 0;
            right: 0;
            padding: 5px 10px;
            border-radius: 0 8px 0 8px;
            font-size: 12px;
            font-weight: bold;
        }

        .loterie-list {
            display: grid; /* Utilisez un affichage de grille */
            grid-template-columns: repeat(2, 1fr); /* Trois éléments par ligne */
            gap: 20px; /* Espacement entre les éléments */
            list-style: none; /* Enlever les puces */
            padding: 0; /* Supprimer les marges par défaut */
            margin: 10px;
        }

        .loterie-item {
            text-align: center; /* Centre le contenu */
            position: relative; /* Permet de positionner les éléments enfants */
            border: 1px solid #ccc; /* Ajouter une bordure autour de chaque élément */
            border-radius: 5px; /* Bord arrondi de chaque élément */
            padding: 10px; /* Espacement interne des éléments */
            background-color: #fff; /* Ajouter une couleur de fond blanche */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .loterie-image {
            display: block;
            margin: 0 auto; /* Centre l'image */
        }

        .loterie-name {
            font-size: 10px; /* Ajustez la taille du texte ici */
            background-color: rgba(0, 0, 0, 0.8); /* Couleur du bandeau */
            color: #fff; /* Couleur du texte du bandeau */
            border-radius: 30px; /* Bord arrondi du bandeau */
            transform: translateY(calc(100% + 3px));
        }

        .loterie-empty-message {
            text-align: center;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-left: 70px;
        }

        .loterie-empty-message p {
            margin: 10px 0;
            font-size: 14px;
            color: #888;
        }

        .probabilite-indicateur {
            position: absolute;
            top: 0;
            right: 0;
            padding: 5px 10px;
            border-radius: 0 8px 0 8px;
            font-size: 12.6px;
            font-weight: bold;
            background-color: #000000ab; /* Ajoutez une couleur de fond noire */
            color: white; /* Couleur du texte */
            display: none; /* Par défaut, cachez l'indicateur */
        }

        .loterie-item:hover .probabilite-indicateur {
            display: block; /* Affiche l'indicateur au survol de l'élément */
        }
    </style>
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'loterie') {
        // Le bloc de statistiques sera affiché uniquement sur la page d'accueil
        ?>
        <div class="section section-default">
            <div class="title dark-grey no-margin padding-10-15">
                <i class="fa-solid fa-hat-wizard"></i> <?php echo $translations["MOTS_047"]; ?>
            </div>
            <ul class="loterie-list">
                <?php
                $currentDateTime = date("Y-m-d H:i:s"); // Date et heure actuelles au format YYYY-MM-DD HH:MM:SS

                $query = $web->prepare("SELECT img, name, probabilite FROM website_loterie WHERE expire >= :currentDateTime");
                $query->bindParam(':currentDateTime', $currentDateTime, PDO::PARAM_STR);
                $query->execute();
                $result = $query->fetchAll(PDO::FETCH_ASSOC);


                // Afficher les lots à gagner
                if ($query->rowCount() > 0) {
                    foreach ($result as $row) {
                        $img = $row['img'];
                        $name = $row['name'];
                        $probabilite = $row['probabilite'];
                        $probabiliteText = ($probabilite <= 25) ? $translations["MOTS_049"] : (($probabilite <= 50) ? $translations["MOTS_50"] : $translations["MOTS_051"]);
                        $probabiliteClass = "probabilite-" . strtolower($probabiliteText);
                        ?>
                        <li class="loterie-item">
                            <img src="img/shop/boutique/items_PNG/<?php echo $img; ?>" class="loterie-image">
                            <div class="loterie-name"><?php echo $name; ?></div>
                            <div class="probabilite-indicateur"><?php echo $translations["MOTS_048"]; ?> <?php echo $probabiliteText; ?></div>
                        </li>
                        <?php
                    }
                } else {
                    ?>
                    <div class="loterie-empty-message">
                        <p><?php echo $translations["MOTS_052"]; ?></p>
                        <p><?php echo $translations["MOTS_053"]; ?></p>
                    </div>
                    <?php
                }
                ?>
            </ul>
            <br/>
        </div>
        <?php
    }
    ?>
    <!-- ./section loterie-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section BOUTIQUE REDUCTION -->
    <style>
        .affaire-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 28px;
            list-style: none;
            padding: 0;
            margin: 19px;
        }

        .affaire-item {
            background-color: #f5f5f5;
            border-radius: 10px;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.31);
            width: calc(33.33% - 20px);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
        }

        .affaire-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 25px;
        }

        .affaire-name {
            font-size: 10px;
            margin: 10px 0 5px 0;
            color: #000;
        }

        .affaire-reduc {
            font-size: 11px;
            color: #888;
            margin: 0;
        }

        .promo-banner {
            position: absolute;
            top: -10px;
            left: 50%; /* Positionnement à 50% de la largeur parente */
            transform: translateX(-50%); /* Centrage horizontal */
            background-color: #ffcc00;
            color: #000;
            padding: 2px 5px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: bold;

        }

        .boutique-reduc-empty-message {
            text-align: center;
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-right: 50px;
            margin-left: 50px;
            color: #888;
        }
        }
    </style>
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'shop') {
        // Le bloc de BOUTIQUE REDUCTION sera affiché uniquement sur cette page
        ?>
        <div class="section section-default">
            <div class="title dark-grey no-margin padding-10-15">
                <i class="fa-solid fa-gift"></i> <?php echo $translations["MOTS_054"]; ?>
            </div>
            <?php
            $query = $web->prepare("SELECT img, name, reduc FROM website_shop_objects WHERE reduc > 0 ORDER BY reduc DESC");
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            if (empty($result)) {
                echo ' <div class="boutique-reduc-empty-message">'. $translations["MOTS_055"] .'</div><br>';
            } else {
                echo '<ul class="affaire-list">';
                foreach ($result as $row) {
                    $img = $row['img'];
                    $name = $row['name'];
                    $reduc = $row['reduc'];

                    echo '<li class="affaire-item">';
                    if ($reduc === 100) {
                        echo '<div class="promo-banner">' . $translations["MOTS_224"] .'</div>';
                    } else {
                        echo '<div class="promo-banner">' . $translations["MOTS_056"] . ' ' . $reduc . '%</div>';
                    }
                    echo '<img src="img/shop/boutique/items_SVG/' . $img . '" class="affaire-image">';
                    echo '<p class="affaire-name">' . $name . '</p>';
                    echo '</li>';
                }
                echo '</ul><br>';
            }
            ?>
        </div>
        <?php
    }
    ?>
    <!-- ./section BOUTIQUE REDUCTION -->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- section desohneur-->
    <?php
    if (!isset($_GET['page']) || $_GET['page'] === 'index') {
        ?>
        <?php
        $query = $login->prepare('SELECT COUNT(*) FROM world_players WHERE groupe = 0 AND deshonor > 0;');
        $query->execute();
        $row = $query->fetch();
        $query->closeCursor();
        $i = $row['COUNT(*)'];
        if ($i > 0) {
            ?>
            <div class="section carousel-tab section-warning">
                <div class="title text-dark no-margin">
                    <i class="ion-nuclear"></i><?php echo '<span style="color: red">' . $i . '</span>'; ?> <?php echo $translations["MOTS_057"]; ?>
                    !
                </div>
                <div class="jcarousel" data-jcarousel="true" data-jcarouselautoscroll="true">
                    <ul class="box" style="left: 0px; top: 0px;">
                        <?php
                        $query = $login->prepare('SELECT * FROM world_players WHERE groupe = 0 AND deshonor > 0 ORDER BY deshonor DESC LIMIT 0, 5;');
                        $query->execute();
                        $query->setFetchMode(PDO:: FETCH_OBJ);

                        $i = 1;
                        while ($row = $query->fetch()) {
                            if ($i > 5) break;

                            $name = $row->name;
                            $class = $row->class;
                            $sexe = $row->sexe;
                            $level = $row->level;
                            $logged = $row->logged;


                            $map = $jiva->prepare('SELECT id, mappos FROM maps WHERE id = ' . $row->map . ';');
                            $map->execute();
                            $map->setFetchMode(PDO:: FETCH_OBJ);
                            $map_row = $map->fetch();
                            $map->closeCursor();

                            $mappos = explode(",", $map_row->mappos);

                            $sub = $jiva->prepare('SELECT id, name FROM subarea_data WHERE id = ' . $mappos[2] . ';');
                            $sub->execute();
                            $sub->setFetchMode(PDO:: FETCH_OBJ);
                            $sub_row = $sub->fetch();
                            $sub->closeCursor();

                            $subName = $sub_row->name;

                            ?>
                            <li class="no-padding clearfix">
                            <h4 class="padding-5"><a href="#"><?php
                                    $classString = convertClassIdToString($class, $sexe);
                                    $imagePath = 'img/dofus/class/headsMed/' . ($class * 10 + $sexe) . '.png';
                                    ?>

                                    <h4 class="padding-10">
                                        <a href="#">
                                            <center>
                                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $classString; ?>"
                                                     style="width: 100px; height: auto;"><br>
                                                <?php echo $name . ' - Niveau ' . $level; ?>
                                            </center>
                                        </a>
                                    </h4>
                                </a></h4>

                            <div class="padding-15">
                                <p><?php echo ($sexe == 0) ? $translations["MOTS_060"] : $translations["MOTS_061"]; ?>
                                <b><?php echo convertClassIdToString($class, $sexe); ?></b> <?php echo $translations["MOTS_058"]; ?>
                                <p><?php echo $translations["MOTS_059"] . " " . ($sexe == 0 ? $translations["MOTS_062"] : $translations["MOTS_063"]) . " " . $translations["MOTS_064"]; ?></p>
                                <p>
                                    <?php
                                    switch ($logged) {
                                        case 0:
                                            echo $translations["MOTS_065"];
                                            break;
                                        case 1:
                                            echo $translations["MOTS_066"] . $subName . " ( <b>" . $mappos[0] . " ; " . $mappos[1] . "</b>) !";
                                            break;
                                    }
                                    ?>

                                </p>
                            </div>
                            </li><?php

                            $i++;
                        }
                        $query->closeCursor();
                        ?>
                    </ul>
                </div>
                <div class="jcarousel-pagination" data-jcarouselpagination="true"><a href="#1" class="active">1</a><a
                            href="#2" class="">2</a></div>
            </div>
            <?php
        }
    } ?>
    <!-- ./section desohnneur-->
    <!-- ----------------------------------------------------------------------------------------------------------------------------- -->
    <!-- bouton Cochon pour remonter la page -->
    <script type="text/javascript" src="plugins/jquery/jquery-1.11.1.min.js"></script>
    <script>
        $('<div></div>')
            .attr('id', 'scrolltotop')
            .hide()
            .css({
                'z-index': '1000',
                'position': 'fixed',
                'bottom': '25px',
                'right': '35px',
                'cursor': 'pointer',
                'width': '65px',
                'height': '70px',
                'background-image': 'url(img/top/cochon_top.png)',  // URL de la première image
                'background-position': 'center',
                'background-size': 'cover'
            })
            .appendTo('body')
            .hover(
                function () {
                    $(this).css('background-image', 'url(img/top/cochon_back.png)'); // URL de la deuxième image
                },
                function () {
                    $(this).css('background-image', 'url(img/top/cochon_top.png)'); // Revenir à la première image lorsque le curseur quitte l'image
                }
            )
            .click(function () {
                $('html,body').animate({scrollTop: 0}, 'slow');
            });

        $(window).scroll(function () {
            if ($(window).scrollTop() < 300) {
                $('#scrolltotop').fadeOut();
            } else {
                $('#scrolltotop').fadeIn();
            }
        });
    </script>
    <!-- /section bouton Cochon -->
</div>
<!-- ./sidebar -->
