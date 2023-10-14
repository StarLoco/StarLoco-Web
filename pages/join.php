		<div class="leftside">
					<ol class="breadcrumb">
						<li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
						<li class="active"><?php echo $translations["MOTS_090"]; ?></li>
					</ol>
						 <?php
    // Vérifier si la connexion est autorisée
    $query = $web->prepare("SELECT rejoin FROM website_general;");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $rejoinAllowed = $result['rejoin'];
    $query->closeCursor();
	
	 // Afficher un message si le rejoin est désactivé
    if ($rejoinAllowed == 'non') {
        echo $translations["ALERTES_004"];
    } else {
        // Si le rejoin est activé, afficher le contenu
        ?>
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<section class="no-border no-padding-top">
							<div class="page-header margin-top-10"><h4><?php echo $translations["MOTS_091"]; ?></h4></div>
							<div class="section section-default padding-25">
								<p class="no-margin">
                                    <?php echo $translations["MOTS_092"]; ?>
                                    <?php echo $translations["MOTS_093"]; ?>
                                    <?php echo $translations["MOTS_094"]; ?>

                                    <a href="?page=register" data-toggle="modal" class="btn btn-danger btn-block btn-md btn-bold margin-bottom-15"><i class="fa fa-plus"></i> <?php echo $translations["MOTS_095"]; ?></a><br /><br />


                                    <?php echo $translations["MOTS_096"]; ?>
                                    <?php echo $translations["MOTS_097"]; ?>
									
									<a href="<?php echo URL_LAUNCHER_1_29; ?>" class="btn btn-danger btn-block btn-md btn-bold margin-bottom-15"><i class="ion-social-windows"></i>  <i class="ion-ios7-cloud-download"></i> <?php echo $translations["MOTS_098"]; ?></a><br /><br />

                                    <?php echo $translations["MOTS_099"]; ?><br />
                                    <?php echo $translations["WARNING_001"]; ?>
                                    <?php echo $translations["ALERTES_005"]; ?>
									<br />
                                    <?php echo $translations["SUCCESS_004"]; ?>
								</p>	
							</div>
						</section>
					</div>
				</div>
			<?php } ?>				
			</div>

			<!-- ./leftside -->	
			