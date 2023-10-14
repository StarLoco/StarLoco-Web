<div class="leftside">
				<ol class="breadcrumb">
					<li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
					<li class="active"><?php echo $translations["MOTS_135"]; ?></li>
				</ol>	
				<?php
				if(!isset($_SESSION['user'])) {
					echo "<script>window.location.replace(\"?page=signin\")</script>";
					return;
				} else {
					$query = $login -> prepare("SELECT * FROM world_accounts WHERE account = '" . $_SESSION['user'] . "';");
					$query -> execute();
					$query -> setFetchMode(PDO:: FETCH_OBJ);
					$row = $query -> fetch();	
					$query -> closeCursor();
				} ?>
					<?php
					ini_set('display_errors', 1);
					ini_set('display_startup_errors', 1);
					error_reporting(E_ALL);
					
					// Vérifier si la connexion est autorisée
					$query = $web->prepare("SELECT profile FROM website_general;");
					$query->execute();
					$result = $query->fetch(PDO::FETCH_ASSOC);
					$profileAllowed = $result['profile'];
					$query->closeCursor();
					
					 // Afficher un message si le profil est désactivé
					if ($profileAllowed == 'non') {
						echo $translations["ALERTES_017"];
					} else {
						// Si le profil est activé, afficher le contenu
						?>
				<div class="title">
					<h2 class="headline margin-bottom-10"><?php echo $translations["MOTS_136"]; ?> <?php echo $row -> pseudo; ?> !</h2>
					<h2 class="page-header text-center no-margin-top"></h2>
				</div>
				<div class="section section-default padding-25" >			
					<div style="display:inline-flex;">
                        <?php
/*
                        $accountId = $_SESSION['id'];

                        $query = $web->prepare("SELECT sexe, class FROM world_players WHERE account = :account ORDER BY xp DESC LIMIT 1");
                        $query->bindParam(':account', $accountId);
                        $query->execute();

                        $result = $query->fetch(PDO::FETCH_ASSOC);

                        if ($result) {
                            $sexe = $result['sexe'];
                            $class = $result['class'];

                            // Utilisez les valeurs récupérées pour afficher l'avatar correspondant
                            $classString = convertClassIdToString($class, $sexe);
                            $imagePath = 'img/dofus/class/headsMed/' . ($class * 10 + $sexe) . '.png';

                            // Affichez l'avatar
                            echo '<img class="img-thumbnail" alt="140x140" src="' . $imagePath . '" style="width: 140px; height: 140px;">';
                        } else {
                            // Aucun enregistrement correspondant n'a été trouvé
                            $defaultImagePath = 'img/avatar/1.jpg';
                            echo '<img class="img-thumbnail" alt="140x140" src="' . $defaultImagePath . '" style="width: 140px; height: 140px;">';
                        }
                        */?>
                        <?php
                        $accountId = $_SESSION['id'];
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            // Si le formulaire est soumis, redirigez l'utilisateur vers la page de modification de profil
                            echo "<script>window.location.replace(\"?page=profile_edit\")</script>";
                        }

                        $query = $login->prepare("SELECT avatar FROM world_accounts WHERE guid = :accountId"); // Assurez-vous que la table et les colonnes sont correctes, et que vous utilisez la bonne colonne pour l'ID
                        $query->bindParam(':accountId', $accountId, PDO::PARAM_INT); // Assurez-vous que l'ID est de type INT si c'est le cas dans votre base de données
                        $query->execute();

                        $result = $query->fetch(PDO::FETCH_ASSOC);

                        if ($result) {
                            $avatar = $result['avatar'];
                            // Afficher l'avatar, par exemple :
                            echo '<img src="img/avatar/' . $avatar . '" alt="Avatar width="140" height="140">';
                        } else {
                            // Gérer le cas où l'avatar n'a pas été trouvé (par exemple, afficher un avatar par défaut)
                            echo '<img src="img/avatar/avatar.jpg" alt="Avatar par défaut" width="140" height="1400">';
                        }
                        ?>

                        <div style="text-align: center; margin-top: 160px; margin-left: -135px;">
                            <form method="post" action="?page=profile"> <!-- Ajoutez l'attribut action avec la valeur de la page de destination -->
                                <button type="submit" class="btn btn-primary">Changer l'avatar</button>
                            </form>
                        </div>



                        <div style="margin-left: 25px; ">
							<strong><?php echo $translations["MOTS_137"]; ?></strong><br><br>
                            <?php echo $translations["MOTS_138"]; ?> <?php echo '<b>' . $row->pseudo . '</b>'; ?><br>
                            <?php echo $translations["MOTS_140"]; ?> <?php
                            // Supposons que $row est un objet avec une propriété "dateRegister"
                            if (!empty($row->dateRegister)) {
                                echo '<strong>' . $row->dateRegister . '</strong>';
                            } else {
                                echo $translations["MOTS_141"];
                            }
                            ?>
                            <br>
                            <?php echo $translations["MOTS_142"]; ?> <?php
                            // Supposons que $row est un objet avec une propriété "lastConnectionDate"
                            $date = (empty($row->lastConnectionDate) ? $translations["MOTS_143"] : parseDate($row->lastConnectionDate));
                            echo ' <strong>' . $date . '</strong>';
                            ?>
                            <br>
                            <?php
                            echo $translations["MOTS_144"];

                            if (!empty($row->lastConnectDaySite)) {
                                $timestamp = strtotime($row->lastConnectDaySite);
                                $formattedDate = date('\l\e d/m/Y H:i', $timestamp);
                                echo '<strong>' . $formattedDate . '</strong>';
                            } else {
                                echo $translations["MOTS_145"];
                            }
                            ?>
                            <br>
                            <?php echo $translations["MOTS_146"]; ?> <?php
                            // Supposons que $row est un objet avec une propriété "email"
                            if (!empty($row->email)) {
                                echo '<strong>' . $row->email . '</strong>';
                            } else {
                                echo $translations["MOTS_147"];
                            }
                            ?>
                            <br>
                            <?php echo $translations["MOTS_148"]; ?> <?php
                            echo '<strong>' . $row->lastIP . '</strong>';
                            ?>
                            <br>
                            <?php echo $translations["MOTS_149"]; ?> <?php
                            // Supposons que $row est un objet avec les propriétés "banned" et "bannedTime"
                            if ($row->banned == 0) {
                                echo $translations["MOTS_150"];
                            } else {
                                if ($row->bannedTime === 86313600000) {
                                    echo $translations["MOTS_151"];
                                } else {
                                    echo $translations["MOTS_152"];

                                    $milliseconds = $row->bannedTime;
                                    $seconds = floor($milliseconds / 1000);
                                    $minutes = floor($seconds / 60);
                                    $hours = floor($minutes / 60);
                                    $days = floor($hours / 24);

                                    $hours %= 24;
                                    $minutes %= 60;
                                    $seconds %= 60;

                                    echo " - <strong>" . $days . " jour(s), " . $hours . " heure(s), " . $minutes . " minute(s), " . $seconds . " seconde(s)</strong>";
                                }
                            }
                            ?><br>
                            <?php echo $translations["MOTS_153"]; ?> <?php echo '<strong>' . $row->totalVotes . '</strong>'; ?><br>
                            <?php
                            $timestampVote = isset($row->heurevote) ? $row->heurevote : 0;
                            $timestampNow = time(); // Obtient le timestamp actuel

                            if (empty($timestampVote)) {
                                echo $translations["MOTS_154"];
                            } else {
                                $diffSeconds = (3 * 60 * 60) - ($timestampNow - $timestampVote); // Calcul de la différence de temps en secondes
                                $diffHours = floor($diffSeconds / 3600); // Conversion en heures
                                $diffMinutes = floor(($diffSeconds % 3600) / 60); // Conversion en minutes
                                $diffSeconds = $diffSeconds % 60; // Conversion en secondes

                                $date = date('H:i:s', $timestampVote);
                                $message = ''. $translations["MOTS_155"] .' <strong>' . $date . '</strong>';

                                if ($diffHours > 0 || $diffMinutes > 0 || $diffSeconds > 0) {
                                    $message .= ''. $translations["MOTS_156"] .' ';

                                    if ($diffHours > 0) {
                                        $message .= '<strong>' . $diffHours . ' '. $translations["MOTS_157"] .' </strong>';
                                    }

                                    if ($diffMinutes > 0) {
                                        $message .= '<strong>' . $diffMinutes . ' '. $translations["MOTS_158"] .' </strong>';
                                    }

                                    $message .= '<strong>' . $diffSeconds . ' '. $translations["MOTS_159"] .'</strong>';
                                } else {
                                    $message .= ' '. $translations["SUCCESS_007"] .' ';
                                }

                                echo $message;
                            }
                            ?>
                            <br>
                            <?php
                            $formattedPoints = number_format($row->points, 0, ',', ' ');
                            echo NOM_POINT . ' : <strong>' . $formattedPoints . '</strong><br>';
                            ?>

                        </div>
					</div>
				</div>	
				<?php 
				$option = $row -> showOrHide;				
				if(isset($_POST['ok1'])) {
					$option = ($option ? 0 : 1);
					$query = $login -> prepare("UPDATE world_accounts SET `showOrHide` = " . $option . " WHERE account = '" . $_SESSION['user'] . "';");
					$query -> execute();
					$query -> closeCursor();
				}
				?>
				<!--<form class="form-inline" method="post" action="#" role="form">
					<div class="alert alert-warning no-border-radius" style="width: 58%; margin-right: 5px;">
						Tu es actuellement <?php /*echo ($option ? "<b>visible</b>" : "<b>invisible</b>"); */?> dans l'armurerie !
					</div>
					<button type="submit" class="btn btn-info btn-icon-right" name="ok1" style="height: 46px; width: 40%; font-size: 15px;"><?php /*echo ($option ? "<i class='ion-eye-disabled'></i> Être invisible" : "<i class='ion-eye'></i> Être visible"); */?></button>
				</form>-->
				<?php
				$option = $row -> showOrHidePos;				
				if(isset($_POST['ok2'])) {
					$option = ($option ? 0 : 1);
					$query = $login -> prepare("UPDATE world_accounts SET `showOrHidePos` = " . $option . " WHERE account = '" . $_SESSION['user'] . "';");
					$query -> execute();
					$query -> closeCursor();
				}
				?>
				<form class="form-inline" method="post" action="#" role="form">
					<div class="alert alert-warning no-border-radius" style="width: 58%; margin-right: 5px;">
                        <?php echo $translations["MOTS_160"]; ?> <?php echo ($option ? $translations["MOTS_162"] : $translations["MOTS_163"]); ?> <?php echo $translations["MOTS_161"]; ?>
					</div>
					<button type="submit" class="btn btn-info btn-icon-right" name="ok2" style="height: 46px; width: 40%; font-size: 15px;"><?php echo ($option ? "<i class='ion-eye-disabled'></i> Être invisible" : "<i class='ion-eye'></i> Être visible"); ?></button>
				</form>
				
				<div class="default-tab">
					<ul id="myTab" class="nav nav-tabs" role="tablist">
						<li role="presentation" class="<?php if(!isset($_POST['change-pass'])) echo 'active'; ?>"><a href="#players" style="color: #363636;!important" id="players-tab" role="tab" data-toggle="tab" aria-controls="players" aria-expanded="<?php echo !isset($_POST['change-pass']); ?>"><i class="ion-person-stalker"></i> <?php echo $translations["MOTS_164"]; ?></a></li>
						<li role="presentation" class="<?php if(isset($_POST['change-pass'])) echo 'active'; ?>"><a href="#settings" role="tab" style="color: #363636;!important" id="settings-tab" data-toggle="tab" aria-controls="settings" aria-expanded="<?php echo isset($_POST['change-pass']); ?>"><i class="ion-settings"></i> <?php echo $translations["MOTS_165"]; ?></a></li>
						<li role="presentation" class=""><a href="#reload" role="tab" style="color: #363636;!important" id="reload-tab" data-toggle="tab" aria-controls="reload" aria-expanded="false"><i class="ion-card"></i> <?php echo $translations["MOTS_167"]; ?> <?php  echo NOM_POINT; ?></a></li>
					</ul>
	
					<div id="myTabContent" class="tab-content">
						<div role="tabpanel" class="tab-pane fade <?php if(!isset($_POST['change-pass']) && !isset($_POST['key'])) echo 'active in'; ?>" id="players" aria-labelledby="players-tab">
							<div class="row">
							<div class="col-md-12">
								<section class="section margin-top-20 margin-bottom-20 no-border">
								<?php				
								$query = $login -> prepare("SELECT name, class, xp, level, sexe, account, alignement, honor, server FROM world_players WHERE account = " . $_SESSION['id'] . ";");
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
                                            <th><?php echo $translations["MOTS_168"]; ?></th>
											<th><?php echo $translations["MOTS_106"]; ?></th>
											<th class="hidden-sm"><?php echo $translations["MOTS_107"]; ?></th>
											<th><?php echo $translations["MOTS_108"]; ?></th>
											<th><?php echo $translations["MOTS_109"]; ?></th>
											<th><?php echo $translations["MOTS_110"]; ?></th>
											<th><?php echo $translations["MOTS_111"]; ?></th>
										</tr>
									</thead>
									
									<tbody>
									<?php
									while($row1 = $query -> fetch()) { ?>
										<tr>
											<td><?php echo $i; ?></td>
                                            <?php
                                                // Supposons que $row1 contient les données de votre première requête qui récupère le serveur spécifique
                                                $server = $row1->server;
                                                $serverName = '';

                                                if ($server == 636) {
                                                    $serverName = 'Osiris';
                                                } elseif ($server == 637) {
                                                    $serverName = '???';
                                                } elseif ($server == 638) {
                                                    $serverName = '???';
                                                }

                                                // Afficher le nom du serveur correspondant
                                                echo '<td>' . $serverName . '</td>';
                                                ?>
											<td><?php echo $row1 -> name; ?></td>
                                            <td class="hidden-sm"><img src ="<?php echo URL_SITE . 'img/dofus/class/' . ($row1 -> class * 10 + $row1 -> sexe) . '.png'; ?>" /></td>
											<td><?php echo $row1 -> level; ?></td>
											<td><?php echo str_replace(',', ' ', number_format($row1->xp)); ?></td>
											<td class="hidden-sm"><img style="border-radius: 15px; -moz-border-radius: 15px; -webkit-border-radius: 15px;" src="<?php echo URL_SITE . 'img/dofus/align/' . $row1 -> alignement . '.jpg'; ?>" /></td>
											<td><?php echo str_replace(',', ' ', number_format($row1->honor)); ?></td>
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
                                    <?php echo $translations["ALERTES_018"]; ?>
								<?php
								} ?>
								</section>								
							</div>
							</div>
						</div>
	
						<div role="tabpanel" class="tab-pane fade <?php if(isset($_POST['change-pass']) && !isset($_POST['key'])) echo 'active in'; ?>" id="settings" aria-labelledby="settings-tab">
							<div class="row">
								<div class="col-md-12">
									<section class="section margin-top-20 margin-bottom-20 no-border">
										<h4 class="page-header no-margin-top"> <?php echo $translations["MOTS_169"]; ?></h4>
										<?php
										if(isset($_POST['change-pass'])) {
											$answer = $_POST['answer'];
											$newPass = $_POST['password'];
											$newPassConf = $_POST['password-repeat'];
											
											$error = -1;
											
											if(empty($answer) || empty($newPass) || empty($newPassConf)) {
												$error = 1;
											} else {
												$count = false;
												$query = $login -> prepare("SELECT * FROM `world_accounts` WHERE `reponse` = ?;");
												$query -> bindParam(1, $answer);
												$query -> execute();
												$count = $query -> rowCount();
												$query -> closeCursor();
												
												if(!$count) {
													$error = 3;
												} else {
													if(strcmp($newPass, $newPassConf) !== 0) {
														$error = 4;
													} else {
														$newPass = hash('SHA512', md5($newPass));
														$query = $login -> prepare("UPDATE `world_accounts` SET `pass` = ? WHERE `account` = ?;");
														$query -> bindParam(1, $newPass);	
														$query -> bindParam(2, $_SESSION['user']);	
														$query -> execute();
														$query -> closeCursor();
													}
												}
											}
											
											switch($error) {
												case 1:
                                                    echo $translations["ALERTES_019"] . "<br />";
													break;
												case 2:
                                                    echo $translations["ALERTES_020"] . "<br />";
													break;
												case 3:
                                                    echo $translations["ALERTES_021"] . "<br />";
													break;
													
												case 4:
                                                    echo $translations["ALERTES_022"] . "<br />";
													break;
												default:
                                                    echo $translations["SUCCESS_008"] . "<br />";
													break;

											}
										}
										?>									
										<form role="form" method="post" action="#">
											<div class="form-group">
                                                <label for="answer"><?php echo $translations["MOTS_170"] . " " . $row->question; ?> ?</label>
                                                <input type="text" class="form-control margin-top-5" id="answer" name="answer" placeholder="La réponse est..">
											</div>
											<div class="form-group">
												<label for="password"><?php echo $translations["MOTS_171"]; ?></label>
												<input type="password" class="form-control margin-top-5" id="password" name="password" placeholder="">
											</div>
											<div class="form-group">	
												<label for="password-repeat"><?php echo $translations["MOTS_172"]; ?></label>
												<input type="password" class="form-control margin-top-5" id="password-repeat" name="password-repeat" placeholder="">	
											</div>
											<button type="submit" name="change-pass"class="btn btn-success"><?php echo $translations["MOTS_173"]; ?></button>
										</form>
									</section>
                                    <!-- FIN CHANGEMENT MDP-->
                                    <hr />
                                    <!-- DEBUT CHANGEMENT PSEUDO    -->
                                    <section class="section margin-top-20 margin-bottom-20 no-border">
                                        <h4 class="page-header no-margin-top"><?php echo $translations["MOTS_174"]; ?></h4>
                                        <?php
                                        if(isset($_POST['change-pseudo'])) {
                                            $answer = $_POST['answer'];
                                            $pseudo = $_POST['pseudo'];
                                            $newPseudoConf = $_POST['pseudo-repeat'];

                                            $error = -1;

                                            if(empty($answer) || empty($pseudo) || empty($newPseudoConf)) {
                                                $error = 1;
                                            } else {
                                                $count = false;
                                                $query = $login->prepare("SELECT * FROM `world_accounts` WHERE `reponse` = ?;");
                                                $query->bindParam(1, $answer);
                                                $query->execute();
                                                $count = $query->rowCount();
                                                $query->closeCursor();

                                                if(!$count) {
                                                    $error = 3;
                                                } else {
                                                    if(strcmp($pseudo, $newPseudoConf) !== 0) {
                                                        $error = 4;
                                                    } else {
                                                        $query = $login->prepare("UPDATE `world_accounts` SET `pseudo` = ? WHERE `account` = ?;");
                                                        $query->bindParam(1, $newPseudoConf);
                                                        $query->bindParam(2, $_SESSION['user']);
                                                        $query->execute();
                                                        $query->closeCursor();
                                                    }
                                                }
                                            }

                                            switch($error) {
                                                case 1:
                                                    echo $translations["ALERTES_023"] ."<br />";
                                                    break;
                                                case 2:
                                                    echo $translations["ALERTES_024"] ."<br />";
                                                    break;
                                                case 3:
                                                    echo $translations["ALERTES_025"] ."<br />";
                                                    break;
                                                case 4:
                                                    echo $translations["ALERTES_026"] ."<br />";
                                                    break;
                                                default:
                                                    echo $translations["SUCCESS_009"] ."<br />";
                                                    break;
                                            }
                                        }
                                        ?>
                                        <form role="form" method="post" action="#">
                                            <div class="form-group">
                                                <label for="answer"><?php echo $translations["MOTS_175"]  . $row->question; ?> ?</label>
                                                <input type="text" class="form-control margin-top-5" id="answer" name="answer" placeholder="La réponse est..">
                                            </div>
                                            <div class="form-group">
                                                <label for="pseudo"><?php echo $translations["MOTS_176"]; ?></label>
                                                <input type="text" class="form-control margin-top-5" id="pseudo" name="pseudo" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="pseudo-repeat"><?php echo $translations["MOTS_177"]; ?></label>
                                                <input type="text" class="form-control margin-top-5" id="pseudo-repeat" name="pseudo-repeat" placeholder="">
                                            </div>
                                            <button type="submit" name="change-pseudo" class="btn btn-success"><?php echo $translations["MOTS_178"]; ?></button>
                                        </form>
                                    </section>

                                    <!-- FIN CHANGEMENT PSEUDO-->
								</div>	
							</div>
						</div>
									
						<div role="tabpanel" class="tab-pane fade <?php if(isset($_POST['key'])) echo 'active in'; ?>"" id="reload" aria-labelledby="reload-tab">
							<div class="row">
								<div class="col-md-12">
									<section class="section margin-top-10 margin-bottom-20 no-border">	
										<?php
										if(isset($_POST['key'])) {
											$public_key  = isset($_POST['key']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_POST['key']) : '';
											$code = isset($_POST['code']) ? preg_replace('/[^a-zA-Z0-9]+/', '', $_POST['code']) : '';
											$rate = isset($_POST['rate']) ? preg_replace('/[^a-zA-Z0-9\-]+/', '', $_POST['rate']) : '';
											
											if($public_key == "0d3b0c706cd8c0e53bbe181d9835e110") {
												if(empty($code)) {
													echo 'Vous devez définir un code';
												} else if(empty($rate)) {
													echo 'Vous devez choisir un palier';
												} else {
										
													$dedipass = file_get_contents('http://api.dedipass.com/v1/pay/?key='.$public_key.'&rate='.$rate.'&code='.$code);
													$dedipass = json_decode($dedipass);
													$code = $dedipass->code; // Le code
													$rate = $dedipass->rate; // Le palier
													if($dedipass -> status == 'success') {
														$explode = explode("-", $rate);
														$points = $dedipass->virtual_currency; 
														$actualPoints = $row -> points;
														$newPoints = $actualPoints + $points;
														
														$query = $login -> prepare("UPDATE `world_accounts` SET `points` = ? WHERE `account` = ?;");
														$query -> bindParam(1, $newPoints);	
														$query -> bindParam(2, $_SESSION['user']);	
														$query -> execute();
														$query -> closeCursor();
														
														$time = date('Y-m-d H:i:s');
														$query = $login->prepare("INSERT INTO `website_shop_points_purchases` (guid, account, points, code, date) VALUES (?, ?, ?, ?, ?);");
														$query->bindParam(1, $row->guid);
                                                        $query->bindParam(2, $row->account);
														$query->bindParam(3, $points);
														$query->bindParam(4, $code);
														$query->bindParam(5, $time); // Liez la variable $time au quatrième paramètre


														$query->execute();
														$query->closeCursor();

														echo "<div class='alert alert-success no-border-radius no-margin' style='text-align: center!important;' role='success'><strong>Oh good !</strong> Vous avez été créditer de <b>" . $points . "</b> Bullions soit un total de <b>" . $newPoints . "</b> Bullions !</div><br />";
													} else {
														echo "<div class='alert alert-danger no-border-radius no-margin' style='text-align: center!important;' role='success'><strong>Oh non !</strong> Le code <b>" . $code . "</b> est invalide ou déjà utiliser !</div><br />";
													}
												}
											}
										}
										?>
                                        <?php echo $translations["WARNING_002"]; "<br />" ?>
										<div data-dedipass="0d3b0c706cd8c0e53bbe181d9835e110"></div>
									</section>
								</div>	
							</div>	
						</div>
					</div>
				</div>
				<?php } ?>	
			</div>
			<!-- ./leftside -->			
			<script src="//api.dedipass.com/v1/pay.js"></script>