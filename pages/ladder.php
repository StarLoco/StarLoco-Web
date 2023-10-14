<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_100"]; ?></li>
    </ol>
	<?php
/*    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);*/
	
    // Vérifier si la connexion est autorisée
    $query = $web->prepare("SELECT ladder FROM website_general;");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $ladderAllowed = $result['ladder'];
    $query->closeCursor();
	
	 // Afficher un message si le ladder est désactivé
    if ($ladderAllowed == 'non') {
        echo $translations["ALERTES_006"];
    } else {
        // Si le ladder est activé, afficher le contenu
        ?>
	<center><img src="img/ladder/head_dofus1.jpg" style="width: 700px; height: auto;"></center><br/>
    <?php require_once('./class/PlayerJobs.class.php'); ?>
    <ul id="myTab4" class="nav nav-tabs" role="tablist">
        <li role="presentation" class="<?php if(!isset($_POST['ok'])) echo 'active'; ?>"><a href="#pvm" id="pvm-tab" role="tab" data-toggle="tab" aria-controls="pvm" aria-expanded="<?php if(!isset($_POST['ok'])) echo 'true'; else echo 'false'; ?>"><?php echo $translations["MOTS_101"]; ?></a></li>
        <li role="presentation" class=""><a href="#pvp" role="tab" id="pvp-tab" data-toggle="tab" aria-controls="pvp" aria-expanded="false"><?php echo $translations["MOTS_102"]; ?></a></li>
        <li role="presentation" class=""><a href="#guilds" role="tab" id="guilds-tab" data-toggle="tab" aria-controls="guilds" aria-expanded="false"><?php echo $translations["MOTS_103"]; ?></a></li>
        <li role="presentation" class=""><a href="#jobs" role="tab" id="job-tab" data-toggle="tab" aria-controls="jobs" aria-expanded="false"><?php echo $translations["MOTS_104"]; ?></a></li>
        <li role="presentation" class=""><a href="#votes" role="tab" id="links-tab5" data-toggle="tab" aria-controls="votes" aria-expanded="false"><?php echo $translations["MOTS_105"]; ?></a></li>
    </ul>
    <!--Debut du PVM-->
    <div id="myTabContent4" class="tab-content padding-20">
        <div role="tabpanel" class="tab-pane fade <?php if(!isset($_POST['ok'])) echo 'active in'; else echo ''; ?>" id="pvm" aria-labelledby="pvm-tab">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    /*ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);*/

                    $query = $login->prepare("SELECT * FROM world_players WHERE groupe = 0 ORDER BY xp DESC LIMIT 0, 50");
                    $query->execute();
                    $count = $query->rowCount();
                    $query->setFetchMode(PDO::FETCH_OBJ);
                    $i = 1;

                    if ($count) {
                    ?>
                    <section class="section section-white no-border no-padding-top">
                        <div class="box no-border-radius padding-20">
                            <table class="table table-striped no-margin">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $translations["MOTS_106"]; ?></th>
                                    <th class="hidden-sm"><?php echo $translations["MOTS_107"]; ?></th>
                                    <th><?php echo $translations["MOTS_108"]; ?></th>
                                    <th><?php echo $translations["MOTS_109"]; ?></th>
                                    <th><?php echo $translations["MOTS_110"]; ?></th>
                                    <th><?php echo $translations["MOTS_035"]; ?></th>
                                </tr>
                                </thead>
                                <?php
                                while($row = $query -> fetch()) { ?>
                                    <tr>
                                        <td>
                                            <?php if ($i == 1): ?>
                                                <img src="img/ladder/trophy_1.png" alt="Image 1">
                                            <?php elseif ($i == 2): ?>
                                                <img src="img/ladder/trophy_2.png" alt="Image 2">
                                            <?php elseif ($i == 3): ?>
                                                <img src="img/ladder/trophy_3.png" alt="Image 3">
                                            <?php else: ?>
                                                <?php echo $i; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $row -> name; ?></td>
                                        <td class="hidden-sm"><img src ="<?php echo URL_SITE . 'img/dofus/class/' . ($row -> class * 10 + $row -> sexe) . '.png'; ?>" /></td>
                                        <td><?php echo $row -> level; ?></td>
                                        <td><?php echo str_replace(',', ' ', number_format($row->xp)); ?></td>
                                        <td class="hidden-sm"><img style="border-radius: 15px; -moz-border-radius: 15px; -webkit-border-radius: 15px;" src="<?php echo URL_SITE . 'img/dofus/align/' . $row -> alignement . '.jpg'; ?>" /></td>
                                        <?php
                                        $server_id = $row->server;
                                        $query = $login->prepare("SELECT name FROM world_servers WHERE id = :server_id");
                                        $query->bindValue(':server_id', $server_id, PDO::PARAM_INT);
                                        $query->execute();
                                        $result = $query->fetch(PDO::FETCH_ASSOC);
                                        if ($result) {
                                            $server_name = $result['name'];
                                        } else {
                                            $server_name = $row->server;
                                        }
                                        echo "<td>" . htmlspecialchars($server_name) . "</td>";
                                        $query->closeCursor();
                                        ?>
                                    </tr>
                                    <?php $i++;
                                }
                                $query -> closeCursor();?>
                                <?php
                                // ...

                                if ($count) {
                                    // Affichage du tableau des joueurs
                                    // ...
                                } else {
                                    // Aucun joueur trouvé, afficher un message
                                    echo $translations["INFOS_002"];
                                }
                                ?>
                                </tbody>
                                <!-- Mettez ici le reste du contenu de la table -->
                            </table>
                        </div>
                </div>
            </div>
        </div>
        </section>
        <?php
        } // Fin de la condition if($count)
        ?>
        <!--fin du PVM-->

        <!--debut du PVP-->
        <div role="tabpanel" class="tab-pane fade" id="pvp" aria-labelledby="pvp-tab">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $query = $login -> prepare("SELECT * FROM world_players WHERE groupe = 0 ORDER BY honor DESC LIMIT 0, 50");
                    $query -> execute();
                    $count = $query -> rowCount();
                    $query -> setFetchMode(PDO:: FETCH_OBJ);
                    $i = 1;

                    if($count) { ?>
                        <section class="section section-white no-border no-padding-top">
                            <div class="box no-border-radius padding-20">
                                <table class="table table-striped no-margin">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $translations["MOTS_106"]; ?></th>
                                        <th class="hidden-sm"><?php echo $translations["MOTS_107"]; ?></th>
                                        <th><?php echo $translations["MOTS_108"]; ?></th>
                                        <th><?php echo $translations["MOTS_110"]; ?></th>
                                        <th><?php echo $translations["MOTS_111"]; ?></th>
                                        <th><?php echo $translations["MOTS_035"]; ?></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    while($row = $query -> fetch()) { ?>
                                        <tr>
                                            <td>
                                                <?php if ($i == 1): ?>
                                                    <img src="img/ladder/trophy_1.png" alt="Image 1">
                                                <?php elseif ($i == 2): ?>
                                                    <img src="img/ladder/trophy_2.png" alt="Image 2">
                                                <?php elseif ($i == 3): ?>
                                                    <img src="img/ladder/trophy_3.png" alt="Image 3">
                                                <?php else: ?>
                                                    <?php echo $i; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $row -> name; ?></td>
                                            <td class="hidden-sm"><img src ="<?php echo URL_SITE . 'img/dofus/class/' . ($row -> class * 10 + $row -> sexe) . '.png'; ?>" /></td>
                                            <td><?php echo $row -> level; ?></td>
                                            <td class="hidden-sm"><img style="border-radius: 15px; -moz-border-radius: 15px; -webkit-border-radius: 15px;" src="<?php echo URL_SITE . 'img/dofus/align/' . $row -> alignement . '.jpg'; ?>" /></td>
                                            <td><?php echo str_replace(',', ' ', number_format($row->honor)); ?></td>
                                            <?php
                                            $server_id = $row->server;
                                            $query = $login->prepare("SELECT name FROM world_servers WHERE id = :server_id");
                                            $query->bindValue(':server_id', $server_id, PDO::PARAM_INT);
                                            $query->execute();
                                            $result = $query->fetch(PDO::FETCH_ASSOC);
                                            if ($result) {
                                                $server_name = $result['name'];
                                            } else {
                                                $server_name = $row->server;
                                            }
                                            echo "<td>" . htmlspecialchars($server_name) . "</td>";
                                            $query->closeCursor();
                                            ?>
                                        </tr>
                                        <?php $i++;
                                    }
                                    $query -> closeCursor();?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <?php
                    } else { ?>
                        <?php echo $translations["INFOS_002"]; ?>
                        <?php
                    } ?>
                </div>
            </div>
        </div>
        <!--fin du PVP-->

        <!--debut des Guildes-->
        <div role="tabpanel" class="tab-pane fade" id="guilds" aria-labelledby="guilds-tab">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $query = $login->prepare("SELECT id, name, lvl, xp, date, maxCollectors FROM `world_guilds` ORDER BY xp DESC LIMIT 0, 50;");
                    $query->execute();
                    $count = $query->rowCount();
                    $query->setFetchMode(PDO::FETCH_OBJ);
                    $i = 1;

                    if ($count) :
                        ?>
                        <section class="section section-white no-border no-padding-top">
                            <div class="box no-border-radius padding-20">
                                <table class="table table-striped no-margin">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $translations["MOTS_106"]; ?></th>
                                        <th><?php echo $translations["MOTS_108"]; ?></th>
                                        <th><?php echo $translations["MOTS_112"]; ?></th>
                                        <th><?php echo $translations["MOTS_229"]; ?></th>
                                        <th><?php echo $translations["MOTS_109"]; ?></th>
                                        <th><?php echo $translations["MOTS_230"]; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php while ($row = $query->fetch()): ?>
                                        <tr>
                                            <td>
                                                <?php if ($i == 1): ?>
                                                    <img src="img/ladder/trophy_1.png" alt="Image 1">
                                                <?php elseif ($i == 2): ?>
                                                    <img src="img/ladder/trophy_2.png" alt="Image 2">
                                                <?php elseif ($i == 3): ?>
                                                    <img src="img/ladder/trophy_3.png" alt="Image 3">
                                                <?php else: ?>
                                                    <?php echo $i; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $row->name; ?></td>
                                            <td><?php echo $row->lvl; ?></td>
                                            <td><?php
                                                // Effectuer une requête pour compter le nombre de membres dans la guilde
                                                $countQuery = $jiva->prepare("SELECT COUNT(*) AS member_count FROM `guild_members` WHERE guild = :guild");
                                                $countQuery->bindParam(':guild', $row->id, PDO::PARAM_INT);
                                                $countQuery->execute();

                                                // Récupérer le nombre de membres
                                                $memberCount = $countQuery->fetch(PDO::FETCH_ASSOC)['member_count'];

                                                echo $memberCount;
                                                ?>
                                            </td>
                                            <td><?php echo $row->maxCollectors; ?></td>
                                            <td><?php echo str_replace(',', ' ', number_format($row->xp)); ?></td>
                                            <td><?php
                                                $timestamp = $row->date;
                                                if (strlen($timestamp) > 10) {
                                                    $timestamp /= 1000;
                                                }
                                                setlocale(LC_TIME, 'fr_FR.UTF-8');
                                                $dateLisible = strftime('%e %b %y %H:%M:%S', $timestamp);
                                                echo $dateLisible;
                                                ?>
                                            </td>
                                        </tr>
                                        <?php $i++; endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    <?php else: ?>
                        <?php echo $translations["INFOS_002"]; ?>
                    <?php endif; ?>
                    <?php $query->closeCursor(); ?>
                </div>
            </div>
        </div>

        <!-- fin de guilde-->
        <!-- début des metiers-->
        <div role="tabpanel" class="tab-pane fades" id="jobs" aria-labelledby="jobs-tab">
            <div class="row">
                <div class="col-md-12">
                    <form class="form-inline" role="form" method="post" action="#" style="margin-bottom: 10px;">
                        <select class="form-control" style="width: 90%!important; height: 35px; padding: 6px 12px!important;" name="job">
                            <?php
                            // Code pour récupérer les métiers depuis la base de données et générer les options du select
                          /*  ini_set('display_errors', 1);
                            ini_set('display_startup_errors', 1);
                            error_reporting(E_ALL);*/

                            $query = $jiva->prepare("SELECT id, name, skills FROM jobs_data WHERE tools != '';");
                            $query->execute();
                            $count = $query->rowCount();
                            $query->setFetchMode(PDO::FETCH_OBJ);
                            $jobsName = array();
                            while ($row = $query->fetch()) {
                                $jobsName[$row->id] = $row->name;
                                echo '<option value="' . $row->id . '" ' . (isset($_POST['job']) && $row->id == $_POST['job']  ? 'selected' : '') . '>' . $row->name . '</option>';
                            }
                            ?>
                        </select>
                        <button type="submit" name="ok" class="btn btn-info">Ok</button>
                    </form>

                    <?php
                    // Code pour afficher les résultats si un métier a été sélectionné
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);

                    if (isset($_POST['ok']) && isset($_POST['job'])) {
                        $query = $login->prepare("SELECT * FROM world_players WHERE jobs LIKE '%" . $_POST['job'] . "%' LIMIT 0, 50;");
                        $query->execute();
                        $count = $query->rowCount();
                        //var_dump($count);
                        $query->setFetchMode(PDO::FETCH_OBJ);

                        if ($count) {
                            // Afficher les résultats
                            ?>
                            <section class="section section-white no-border no-padding-top">
                                <div class="box no-border-radius padding-20">
                                    <table class="table table-striped no-margin">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <?php echo $translations["MOTS_106"]; ?>
                                            <?php echo $translations["MOTS_106"]; ?>
                                            <?php echo $translations["MOTS_108"]; ?>
                                            <?php echo $translations["MOTS_113"]; ?>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        ini_set('display_errors', 1);
                                        ini_set('display_startup_errors', 1);
                                        error_reporting(E_ALL);

                                        $players = array();
                                        $i = 0;
                                        while ($row = $query->fetch()) {
                                            $split1 = explode(';', $row->jobs);
                                            $currentJob = $_POST['job'];
                                            $currentJobXp = 0;
                                            $otherJobs = array();
                                            $nbr = 0;
                                            foreach ($split1 as $element) {
                                                $split2 = explode(',', $element);

                                                if ($split2[0] == $_POST['job']) {
                                                    $currentJobXp = $split2[1];
                                                    continue;
                                                }

                                                $otherJobs[$nbr] = $split2[0];
                                                $nbr++;
                                            }

                                            $query1 = $jiva->prepare('SELECT lvl, metier FROM experience WHERE metier > ' . $currentJobXp . ';');
                                            $query1->execute();
                                            $row1 = $query1->fetch();
                                            $query1->closeCursor();
                                            $currentJobLvl = $row1['lvl'] - 1;

                                            if ($currentJobLvl != 100) {
                                                $query2 = $jiva->prepare('SELECT lvl, metier FROM experience WHERE lvl = ' . $currentJobLvl . ';');
                                                $query2->execute();
                                                $row2 = $query2->fetch();

                                                $query3 = $jiva->prepare('SELECT lvl, metier FROM experience WHERE lvl = ' . ($currentJobLvl + 1) . ';');
                                                $query3->execute();
                                                $row3 = $query3->fetch();


                                                $xpActuel = $currentJobXp;
                                                $xpMax1 = $row2['metier'];
                                                $xpMax2 = $row3['metier'];

                                                if ($xpMax2 - $xpMax1 != 0) {
                                                    $pourcent = ($xpActuel - $xpMax1) / ($xpMax2 - $xpMax1) * 100;
                                                    if ($pourcent < 10)
                                                        $pourcent = '0' . substr($pourcent * 100, 0, 1) . '%';
                                                    else
                                                        $pourcent = substr($pourcent * 100, 0, 2) . '%';
                                                } else {
                                                    $pourcent = '00';
                                                }
                                                $query2->closeCursor();
                                                $query3->closeCursor();
                                            } else {
                                                $pourcent = '100';
                                            }

                                            $players[$i]['lvl'] = $currentJobLvl;
                                            $players[$i]['xp'] = 1;
                                            $players[$i]['player'] = new PlayerJobs($row->name, $currentJob, $currentJobLvl, $pourcent, $otherJobs);
                                            $i++;
                                        }
                                        $query->closeCursor();

                                        $i = 1;

                                        foreach ($players as $player) {
                                            $player = $player['player'];
                                            if ($player->currentJobLvl == -1)
                                                continue;
                                            ?>

                                            <tr>
                                                <td>
                                                    <?php if ($i == 1): ?>
                                                        <img src="img/ladder/trophy_1.png" alt="Image 1">
                                                    <?php elseif ($i == 2): ?>
                                                        <img src="img/ladder/trophy_2.png" alt="Image 2">
                                                    <?php elseif ($i == 3): ?>
                                                        <img src="img/ladder/trophy_3.png" alt="Image 3">
                                                    <?php else: ?>
                                                        <?php echo $i; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $player->name; ?></td>
                                                <td><img src="<?php echo URL_SITE . 'img/dofus/job/' . $player->currentJob . '.png'; ?>"/></td>
                                                <td><?php echo $player->currentJobLvl; ?></td>
                                                <td><?php echo $player->currentJobXp; ?></td>
                                                <td>
                                                    <?php
                                                    foreach ($player->otherJobs as $id) {
                                                        echo '<img style="margin-right: 5px;" src="' . URL_SITE . 'img/dofus/job/' . $id . '.png" data-toggle="tooltip" title="" data-original-title="' . utf8_encode($jobsName[$id]) . '"/>';
                                                    }
                                                    ?>
                                                </td>

                                            </tr>
                                            <?php $i++;
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </section>
                            <?php
                        } else {
                            // Aucun enregistrement trouvé
                            ?>
                            <?php echo $translations["INFOS_003"]; ?>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <!-- Fin des metiers-->

        <!-- debut des votes-->
        <div role="tabpanel" class="tab-pane fade" id="votes" aria-labelledby="votes-tab">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $query = $login -> prepare("SELECT pseudo, votes, totalVotes FROM world_accounts ORDER BY totalVotes DESC LIMIT 0, 50;");

                    $query -> execute();
                    $count = $query -> rowCount();
                    $query -> setFetchMode(PDO:: FETCH_OBJ);
                    $i = 1;

                    if($count) { ?>
                        <section class="section section-white no-border no-padding-top">
                            <div class="box no-border-radius padding-20">
                                <table class="table table-striped no-margin">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo $translations["MOTS_114"]; ?></th>
                                        <th><?php echo $translations["MOTS_115"]; ?></th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php
                                    while($row = $query -> fetch()) { ?>
                                        <tr>
                                            <td>
                                                <?php if ($i == 1): ?>
                                                    <img src="img/ladder/trophy_1.png" alt="Image 1">
                                                <?php elseif ($i == 2): ?>
                                                    <img src="img/ladder/trophy_2.png" alt="Image 2">
                                                <?php elseif ($i == 3): ?>
                                                    <img src="img/ladder/trophy_3.png" alt="Image 3">
                                                <?php else: ?>
                                                    <?php echo $i; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $row -> pseudo; ?></td>
                                            <td><?php echo $row -> totalVotes; ?></td>
                                        </tr>
                                        <?php $i++;
                                    }
                                    $query -> closeCursor();?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <?php
                    } else { ?>
                        <?php echo $translations["INFOS_002"]; ?>
                        <?php
                    } ?>
                </div>
            </div>
        </div>
        <!-- FIN des votes-->
        <!-- FIN DE LA PAGE-->
    </div>
	<?php } ?>
</div>

