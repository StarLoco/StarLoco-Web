<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_012"]; ?></li>
    </ol>
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Vérifier si la connexion est autorisée
    $query = $web->prepare("SELECT viewdrop FROM website_general;");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $viewdropAllowed = $result['viewdrop'];
    $query->closeCursor();

    // Afficher un message si l'encyclopedie est désactivé
    if ($viewdropAllowed == 'non') {
        echo $translations["ALERTES_043"] . "<br/>";
    } else {
        // Si l'encyclopedie est activé, afficher le contenu
        ?>
        <center><img src="img/bestiaire/monsters.jpg" style="width: 300px; height: auto;"></center>
        <ol class="breadcrumb">
            <form method="post" class="form-inline">
                <input type="text" class="form-control" name="monster" style="border: 1px solid gray;"
                       placeholder="Recherche du monstre.."/>
                <button type="submit" class="btn btn-primary" name="search1"><i class="fa fa-search"></i></button>
            </form>
            <br/>
            <form method="post" class="form-inline">
                <input type="text" class="form-control" name="object" style="border: 1px solid gray;"
                       placeholder="Recherche de l'objet.."/>
                <button type="submit" name="search2" class="btn btn-primary"><i class="fa fa-search"></i></button>
            </form>
        </ol>

        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        if (!isset($_SESSION['user'])) {
            echo "<script>window.location.replace(\"?page=signin\")</script>";
            return;
        }

        if (isset($_POST['search1']) && isset($_POST['monster'])) {
            $searchedMonster = $_POST['monster']; // Récupérer le nom saisi par l'utilisateur
//            $query1 = $jiva->prepare("SELECT * FROM `monsters` WHERE LOWER(name) LIKE ?;");
            $query1 = $jiva->prepare("SELECT * FROM `monsters` WHERE name = ?;");
            $query1->execute(array(strtolower($_POST['monster'])));
//            $query1->execute(array("%" . strtolower($_POST['monster']) . "%"));
            $query1->setFetchMode(PDO::FETCH_OBJ);
            ?>
            <ol class="breadcrumb">
                <div class="box no-border-radius padding-20" style="border: 1px solid gray;">
                    <?php
                    if ($query1->rowCount() === 0) {
                        ini_set('display_errors', 1);
                        ini_set('display_startup_errors', 1);
                        error_reporting(E_ALL);
                        // Si aucun monstre n'est trouvé, insérer les données dans la table website_ticket_encyclopedie
                        $type = 'Monstre'; // Valeur du nouveau champ "type"
                        $searchedMonster = $_POST['monster'];
                        $currentDate = date("Y-m-d H:i:s");
                        $account = $_SESSION['user'];

                        // Requête d'insertion dans la table website_ticket_encyclopedie
                        // Préparez la requête SELECT pour vérifier l'existence du ticket
                        $selectQuery = $web->prepare("SELECT COUNT(*) as count FROM website_ticket_encyclopedie WHERE account = ? AND type = ? AND recherche = ?;");
                        $selectQuery->execute(array($account, $type, $searchedMonster));
                        $result = $selectQuery->fetch();
                        // Vérifiez si un ticket correspondant existe déjà
                        if ($result['count'] == 0) {
                            // Aucun ticket correspondant n'a été trouvé, vous pouvez insérer le nouveau ticket

                            // Requête d'insertion préparée
                            $insertQuery = $web->prepare("INSERT INTO website_ticket_encyclopedie (account, type, recherche, date) VALUES (?, ?, ?, ?);");

                            // Exécution de la requête d'insertion avec les valeurs appropriées
                            $insertQuery->execute(array($account, $type, $searchedMonster, $currentDate));

                            echo '<div class="alert alert-danger no-border-radius no-margin" role="alert">';
                            echo '<center><strong>Oups !</strong> Aucun monstre trouvé avec le nom "' . htmlspecialchars($searchedMonster) . '", un ticket vient d\'être ouvert pour résoudre ce problème.</center>';
                            echo '</div>';
                        } else {
                            // Un ticket correspondant a été trouvé, message d'erreur
                            echo '<div class="alert alert-danger no-border-radius no-margin" role="alert">';
                            echo '<center><strong>Oh non !</strong> Un ticket a déjà été ouvert sous le nom : "' . htmlspecialchars($searchedMonster) . '", il sera résolu très rapidement.</center>';
                            echo '</div>';
                        }
                        ?>
                        <?php
                    } else {
                        ?>
                        <table class="table table-striped no-margin">
                            <thead>
                            <tr>
                                <th><?php echo $translations["MOTS_206"]; ?></th>
                                <th><?php echo $translations["MOTS_207"]; ?></th>
                                <th><?php echo $translations["MOTS_208"]; ?></th>
                                <th><?php echo $translations["MOTS_209"]; ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            while ($row1 = $query1->fetch()) {
                                $count = 0;
                                $query2 = $jiva->prepare("SELECT * FROM `drops` WHERE `monsterId` = ?;");
                                $query2->execute(array($row1->id));

                                while ($query2->fetch())
                                    $count++;

                                $query2->closeCursor();

                                if ($count > 0) {
                                    ?>
                                    <tr>
                                        <td><img src="img/bestiaire/monsters_svg/<?php echo $row1->id; ?>.svg"
                                                 title="<?php echo $row1->name; ?>" height="70" width="auto"></td>
                                        <?php
                                        $query2 = $jiva->prepare("SELECT * FROM `drops` WHERE `monsterId` = ?;");
                                        $query2->execute(array($row1->id));
                                        $query2->setFetchMode(PDO::FETCH_OBJ);
                                        $name = "";
                                        $seuil = "";
                                        $taux = "";
                                        while ($row2 = $query2->fetch()) {
                                            $query3 = $jiva->prepare("SELECT * FROM `item_template` WHERE id = ?;");
                                            $query3->execute(array($row2->objectId));
                                            $query3->setFetchMode(PDO::FETCH_OBJ);
                                            while ($row3 = $query3->fetch()) {
                                                $name .= $row3->name . "<br />";
                                                $seuil .= $row2->ceil;
                                                $seuil .= "<img src='img/bestiaire/Rune-Prospe_0.png' height='20' width='20'><br />";

                                                if ($row2->percentGrade5 >= 1) {
                                                    if ($row2->percentGrade1 == $row2->percentGrade5)
                                                        $taux .= str_replace(".0", "", str_replace(".00", "", str_replace(".000", "", str_replace("00%", "%", $row2->percentGrade1 . "%"))));
                                                    else
                                                        $taux .= str_replace(".0", "", str_replace(".000", "", str_replace(".000", "", str_replace("00%", "%", $row2->percentGrade1 . "%")))) . " à " . str_replace(".0", "", str_replace(".000", "", str_replace(".000", "", str_replace("00%", "%", $row2->percentGrade5 . "%"))));
                                                } else $taux .= "<font color='red'><</font> 1%";
                                                $taux .= "<br />";
                                            }
                                        }
                                        $query2->closeCursor();
                                        $query3->closeCursor();
                                        ?>
                                        <td><?php echo $name; ?></td>
                                        <td><?php echo $seuil; ?></td>
                                        <td><?php echo $taux; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            $query1->closeCursor();
                            /*$query2->closeCursor();
                            $query3->closeCursor();*/
                            ?>
                            </tbody>
                        </table>
                        <?php
                    }
                    ?>
                </div>
            </ol>
            <?php
        } else if (isset($_POST['search2']) && isset($_POST['object'])) {
            $searchedObject = $_POST['object']; // Récupérer le nom saisi par l'utilisateur
            $query1 = $jiva->prepare("SELECT * FROM `item_template` WHERE name = ?;");
            $query1->execute(array(strtolower($searchedObject)));
            $query1->setFetchMode(PDO::FETCH_OBJ);
            ?>
            <?php
            if ($query1->rowCount() === 0) {
                /*ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);*/

                $type2 = 'Objet'; // Valeur du nouveau champ "type"
                $searchedObject = $_POST['object'];
                $currentDate = date("Y-m-d H:i:s");
                $account = $_SESSION['user'];

                // Préparez la requête SELECT pour vérifier l'existence de l'objet
                $selectQuery = $web->prepare("SELECT COUNT(*) as count FROM website_ticket_encyclopedie WHERE account = ? AND type = ? AND recherche = ?;");

                // Exécutez la requête SELECT avec les valeurs appropriées
                $selectQuery->execute(array($account, $type2, $searchedObject));
                $result = $selectQuery->fetch();

                if ($result['count'] == 0) {
                    $insertQuery4 = $web->prepare("INSERT INTO website_ticket_encyclopedie (account, type, recherche, date) VALUES (?, ?, ?, ?);");
                    $insertQuery4->execute(array($account, $type2, $searchedObject, $currentDate));

                    echo '<div class="alert alert-danger no-border-radius no-margin" role="alert">';
                    echo '<center><strong>Oups !</strong> Aucun objet trouvé avec le nom "' . htmlspecialchars($searchedObject) . '", un ticket vient d\'être ouvert pour résoudre ce problème.</center>';
                    echo '</div>';
                } else {
                    // Un ticket correspondant a été trouvé, vous pouvez afficher un message d'erreur ou prendre d'autres mesures nécessaires.
                    echo '<div class="alert alert-danger no-border-radius no-margin" role="alert">';
                    echo '<center><strong>Oh non !</strong> Un ticket a déjà été ouvert sous le nom: "' . htmlspecialchars($searchedObject) . '", il sera résolu très rapidement.</center>';
                    echo '</div>';
                }
            }
            ?>
            <ol class="breadcrumb">
            <div class="box no-border-radius padding-20" style="border: 1px solid gray;">
            <center><table class="table table-striped no-margin">
                <thead>
                <tr>
                    <th><?php echo $translations["MOTS_210"]; ?></th>
                    <th><?php echo $translations["MOTS_211"]; ?></th>
                    <th><?php echo $translations["MOTS_208"]; ?></th>
                    <th><?php echo $translations["MOTS_209"]; ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                while ($row1 = $query1->fetch()) {
                    $query2 = $jiva->prepare("SELECT * FROM `drops` WHERE `objectId` = ?;");
                    $query2->execute(array($row1->id));
                    $query2->setFetchMode(PDO::FETCH_OBJ);

                    // Tableaux pour stocker les informations des monstres associés
                    $names = array();
                    $seuils = array();
                    $tauxs = array();

                    while ($row2 = $query2->fetch()) {
                        $query3 = $jiva->prepare("SELECT * FROM `monsters` WHERE `id` = ?;");
                        $query3->execute(array($row2->monsterId));
                        $query3->setFetchMode(PDO::FETCH_OBJ);

                        while ($row3 = $query3->fetch()) {
                            // Récupérer les informations du monstre associé
                            $names[] = $row3->name;
                            $seuils[] = $row2->ceil . " <img src='img/bestiaire/Rune-Prospe_0.png' height='20' width='20'>";
                            $taux = ($row2->percentGrade5 >= 1) ?
                                str_replace(".0", "", str_replace(".00", "", str_replace(".000", "", str_replace("00%", "%", $row2->percentGrade1 . "%")))) . " à " . str_replace(".0", "", str_replace(".000", "", str_replace(".000", "", str_replace("00%", "%", $row2->percentGrade5 . "%")))) :
                                "<font color='red'><</font> 1%";
                            $tauxs[] = $taux;
                        }
                    }
                    // Fermer le curseur $query2 après la boucle
                    $query2->closeCursor();
                    $query3->closeCursor();


                    // Afficher les informations de l'objet et les monstres associés
                    if (!empty($names)) {
                        ?>
                        <tr>
                            <td><img src="img/shop/boutique/items_PNG/<?php echo $row1->id; ?>.png" title="<?php echo $row1->name; ?>" height="70" width="auto"></td>
                            <td><?php echo implode("<br>", $names); ?></td>
                            <td><?php echo implode("<br>", $seuils); ?></td>
                            <td><?php echo implode("<br>", $tauxs); ?></td>
                        </tr>
                        <?php
                    }
                }
                $query1->closeCursor();
                ?>
                </tbody>
                </table></center>
            <?php
        }
        ?>
        </ol>
        <?php
    }
    ?>
</div>
<!-- ./leftside -->
