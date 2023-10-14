			<?php if(!isset($_SESSION['user'])) {
				echo "<script>window.location.replace(\"?page=signin\")</script>";
				return;
			} ?>
			<div class="leftside">
				<ol class="breadcrumb">
					<li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
					<li class="active"><?php echo $translations["MOTS_014"]; ?></li>
				</ol>
                <?php
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

                // Vérifier si la connexion est autorisée
                $query = $web->prepare("SELECT shop FROM website_general;");
                $query->execute();
                $result = $query->fetch(PDO::FETCH_ASSOC);
                $shopAllowed = $result['shop'];
                $query->closeCursor();

                // Afficher un message si le rejoin est désactivé
                if ($shopAllowed == 'non') {
                    echo $translations["ALERTES_036"] . "<br />";
                } else {

                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

				$server = -1; $category = -1; $name = "";
				
				if(isset($_POST['ok1'])) {
					if(isset($_POST['server'])) {
						if(!empty($_POST['server'])) {
							if(is_numeric($_POST['server'])) {
								$server = $_POST['server'];
							}
						}
					}
				} else if(isset($_GET['server'])){
					if(isset($_GET['server'])) {
						if(!empty($_GET['server'])) {
							if(is_numeric($_GET['server'])) {
								$server = $_GET['server'];
							}
						}
					}
				}
				
				if(isset($_POST['ok2'])) {
					if(isset($_POST['category'])) {
						if(!empty($_POST['category'])) {
							if(is_numeric($_POST['category'])) {
								$category = $_POST['category'];
							}
						}
					}
				} else if(isset($_GET['category'])){
					if(isset($_GET['category'])) {
						if(!empty($_GET['category'])) {
							if(is_numeric($_GET['category'])) {
								$category = $_GET['category'];
							}
						}
					}
				} ?>
				
				<!-- Selecter of server -->
                    <div class="page-header margin-top-10"><h4><?php echo $translations["MOTS_189"]; ?></h4></div>
				<form class="form-inline" role="form" method="post" style="margin-bottom: 10px;">
					<select class="form-control" style="width: 90%!important; height: 35px; padding: 6px 12px!important;" name="server">
						<?php 
						$query = $login -> prepare("SELECT id, name FROM world_servers;");
						$query -> execute();
						$query -> setFetchMode(PDO:: FETCH_OBJ);

						while($row = $query -> fetch()) {
							if($row -> id == $server) $name = $row -> name;
							echo '<option value="' . $row -> id . '" ' . (isset($_POST['server']) && $row -> id == $_POST['server']  ? 'selected' : '') . '>' . $row -> name . '</option>';
						}
						$query -> closeCursor();
						?>
					</select>
					<button type="submit" name="ok1" class="btn btn-info"><?php echo $translations["MOTS_190"]; ?></button>
				</form>
				<!-- End selecter of server -->
				
				<!-- Selecter of category -->
				<?php
                ini_set('display_errors', 1);
                ini_set('display_startup_errors', 1);
                error_reporting(E_ALL);

				if($server != -1) { ?>
					<form class="form-inline" role="form" method="post" action="?page=shop&server=<?php echo $server; ?>" style="margin-bottom: 10px;">
						<select class="form-control" style="width: 90%!important; height: 35px; padding: 6px 12px!important;" name="category">
							<?php 
							$query = $connection -> prepare("SELECT id, name FROM `website_shop_categories` WHERE `active` = 1;");
							$query -> execute();
							$query -> setFetchMode(PDO:: FETCH_OBJ);

							while($row = $query -> fetch()) {
								$id = $row -> id;
								$query1 = $connection -> prepare("SELECT * FROM `website_shop_objects` WHERE `server` = ? AND `category` = ?;");
								$query1 -> bindParam(1, $server);
								$query1 -> bindParam(2, $id);
								$query1 -> execute();
							
								if($query1 -> fetch()) echo '<option value="' . $id . '" ' . (isset($_POST['category']) && $id == $_POST['category']  ? 'selected' : '') . '>' . $row -> name . '</option>';
								$query1 -> closeCursor();
							}
							$query -> closeCursor();
							?>
						</select>
						<button type="submit" name="ok2" class="btn btn-info">Ok</button>
					</form>
					
					<div class="alert alert-info no-border-radius" role="alert">
                        <?php echo $translations["MOTS_191"]; ?> <strong><?php echo $name; ?></strong> !
					</div>
				<?php
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);
                    
					if($category != -1) {
						$query = $web -> prepare("SELECT * FROM `website_shop_objects` WHERE `category` = " . $category . " AND server LIKE '%" . $server . "%' AND `active` = 1 ORDER BY price DESC;");
						$query -> execute();
						$count = $query -> rowCount();
						$query -> setFetchMode(PDO:: FETCH_OBJ);

                        if (isset($_GET['add_to_cart']) && is_numeric($_GET['add_to_cart'])) {
                            $itemToAdd = $_GET['add_to_cart'];

                            // Requête pour obtenir les détails de l'objet à ajouter au panier
                            $itemQuery = $web->prepare("SELECT * FROM `website_shop_objects_templates` WHERE `id` = :item_id;");
                            $itemQuery->bindParam(':item_id', $itemToAdd, PDO::PARAM_INT);
                            $itemQuery->execute();
                            $item = $itemQuery->fetch(PDO::FETCH_OBJ);
                            $itemQuery->closeCursor();

                            // Ajouter l'objet au panier
                            if (isset($_SESSION['cart'])) {
                                $found = false; // Variable pour indiquer si l'objet existe déjà dans le panier

                                foreach ($_SESSION['cart'] as &$item) {
                                    if ($item['template'] == $itemToAdd) {
                                        $item['quantity']++; // Augmenter la quantité de l'objet existant
                                        $found = true;
                                        break;
                                    }
                                }

                                if (!$found) {
                                    $_SESSION['cart'][] = array(
                                        'template' => $itemToAdd,
                                        'name' => $item->name,
                                        'price' => $item->price,
                                        'jp' => $item->jp,
                                        'quantity' => 1 // Nouvel objet, donc la quantité est 1
                                    );
                                }
                            } else {
                                $_SESSION['cart'] = array(
                                    array(
                                        'template' => $itemToAdd,
                                        'name' => $item->name,
                                        'price' => $item->price,
                                        'jp' => $item->jp,
                                        'quantity' => 1 // Premier objet, donc la quantité est 1
                                    )
                                );
                            }

                        }

						if($count) { ?>
						<section class="section section-white no-border no-padding-top">	
							<div class="box no-border-radius padding-20">
							<table class="table no-margin">
								<thead>
									<tr>
										<th>#</th>
										<th><?php echo $translations["MOTS_106"]; ?></th>
										<th><?php echo $translations["MOTS_192"]; ?></th>
										<th><?php echo $translations["MOTS_131"]; ?></th>
										<th><?php echo $translations["MOTS_193"]; ?></th>
										<th><?php echo $translations["MOTS_194"]; ?></th>
									</tr>
								</thead>
								<tbody><?php
								while($row = $query -> fetch()) {
                                    $query1 = $connection->prepare("SELECT * FROM `website_shop_objects_templates` AS t
                               INNER JOIN `website_shop_objects` AS o ON t.id = o.template
                               WHERE t.id = :template_id;");
                                    $query1->bindParam(':template_id', $row->template, PDO::PARAM_INT);
                                    $query1->execute();
                                    $query1->setFetchMode(PDO::FETCH_OBJ);
                                    $object = $query1->fetch();
                                    $query1->closeCursor();
                                    ?>

									<tr data-toggle="tooltip" title="" data-original-title="<?php if($object -> effects == "") echo "Aucun"; else  echo convertStatsToString($object -> effects);?>" data-html="true">
                                    <td>
                                        <?php
                                        $imagePath = "img/shop/boutique/items_SVG/" . $row->img;
                                        if (file_exists($imagePath)) {
                                            echo '<img src="' . $imagePath . '" alt="Product Image" style="width: 30px; height: auto;">';
                                        } else {
                                            echo '<img src="img/shop/boutique/items_SVG/gfx_not_found.svg" alt="Non dispo" style="width: 30px; height: auto;">';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $row -> name; ?></td>
										<td><?php echo $object -> level; ?></td>
                                    <td>
                                        <?php
                                        $price = $row->price;
                                        $reduction = $object->reduc;

                                        // Calcul du prix avec réduction
                                        $priceWithReduction = $price - ($price * ($reduction / 100));

                                        if ($priceWithReduction < $price) {
                                            echo $priceWithReduction . ' ' . NOM_POINT . ' <br>(' . $reduction . ''. $translations["MOTS_225"].')';
                                        } else {
                                            echo $price . ' ' . NOM_POINT;
                                        }
                                        ?>
                                    </td>
                                    <td>
											<span class="btn btn-<?php if($row -> jp) echo 'success'; else echo 'danger'; ?> btn-outline btn-circle btn-xs"><i class="ion-<?php if($row -> jp) echo 'checkmark'; else echo 'close'; ?>"></i></span>
										</td>
										<td>
                                            <a href="?page=shop&server=<?php echo $server; ?>&add_to_cart=<?php echo $object->id; ?>"title="Ajouter au panier"><img src="./img/shop/btn_shop.png"/></a>
<!--                                            <a href="--><?php //echo URL_SITE; ?><!--?page=buy&template=--><?php //echo $object -> id; ?><!--&server=--><?php //echo $server; ?><!--"><span class="btn btn-info btn-outline btn-circle btn-xs" data-toggle="tooltip" title="" data-original-title="Acheter"><i class="fa fa-credit-card"></i></span></a>-->
										</td>                          
									</tr><?php
								} ?>
								</tbody>
							</table>
							</div>
						</section>
						<?php
						
						} else { ?>
                            <?php echo $translations["ALERTES_044"]; ?>
						<?php 
						}
					}
				}
                ?>
                <?php } ?>
				<!-- End selecter of category -->

            </div>
			<!-- ./leftside -->			