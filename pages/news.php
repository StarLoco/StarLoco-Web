			<div class="leftside">
					<ol class="breadcrumb">
						<li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
						<li class="active"><?php echo $translations["MOTS_125"]; ?></li>
					</ol>	
				<div class="row">
					<div class="col-md-12 col-xs-12">
						<section class="no-border no-padding-top">
							<div class="page-header margin-top-10"><h4><?php echo $translations["MOTS_126"]; ?></h4></div>
							
								<?php
								require_once("./include/rsslib.php");
								echo RSS_Display(URL_RSS_NEWS_IPB, 15, false, true);
								?>
							
							
						</section>
					</div>
				</div>			
			</div>
			<!-- ./leftside -->			