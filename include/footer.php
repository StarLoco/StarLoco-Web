		</div><!-- ./wrapper -->
	</div>

	<!-- footer -->
	<footer style="padding:0px!important;">
		<div class="container">
			<div class="widget row">
				<!-- about -->
				<div class="col-md-4 col-xs-12 no-padding-sm-lg pull-left">
					<h4 class="title"><?php echo $translations["MOTS_015"]; ?></h4>
					<div class="text">
						<span><?php echo $translations["MOTS_016"]; ?><br> <?php echo $translations["MOTS_017"]; ?><br> <?php echo $translations["MOTS_018"]; ?></span>
					</div>
				</div>
				<!-- latest tweet -->
			</div>
            <!-- /.footer widget -->
		</div>
	
		<div class="footer-bottom" style="margin-top:0px!important;">
			<div class="container">
				<ul class="pull-left hidden-xs">
					<li>2014 - 2023 &copy; <?php echo TITLE; ?>. <?php echo $translations["MOTS_019"]; ?>&nbsp;&nbsp;&nbsp;</li>
				</ul>
				<ul class="pull-left hidden-xs">
					<li><a href="?page=cgu"><?php echo $translations["MOTS_020"]; ?></a></li>
				</ul>
				
				<ul class="pull-right hidden-xs">
					<li style= "color: rgba(255, 255, 255, 100);">Code by Locos & Majordom</li>
				</ul>
			</div>
		</div>
	</footer>	
	<!-- ./footer -->

	<!-- Javascript -->
    <script src="https://kit.fontawesome.com/1710200817.js" crossorigin="anonymous"></script>
    <script src="plugins/jquery/jquery-1.11.1.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.min.js"></script>
	<script src="plugins/bxslider/jquery.bxslider.min.js"></script>
	<script src="plugins/jcarousel/jquery.jcarousel.min.js"></script>
	<script src="plugins/holder/holder.js"></script>
	<script src="plugins/core.js"></script>
	
	<script src="plugins/notification/js/modernizr.custom.js"></script>
	<script src="plugins/notification/js/classie.js"></script>
	<script src="plugins/notification/js/notificationFx.js"></script>
		
	<script src="plugins/pace/pace.min.js"></script>

	<script src="plugins/ckeditor5/ckeditor.js"></script>

<!----------------------------------/ panel lange ---------------------------------------------------------------------------------------------------------------->
        <script>
            function changeLanguage(language) {
                // Enregistrer la préférence de langue dans le cookie
                var expiration = new Date();
                expiration.setTime(expiration.getTime() + (365 * 24 * 60 * 60 * 1000)); // 1 an
                document.cookie = "language=" + language + "; expires=" + expiration.toUTCString() + "; path=/";

                // Recharger la page actuelle
                location.reload();
            }
        </script>
<!----------------------------------/ panel lange ---------------------------------------------------------------------------------------------------------------->
<!----------------------------------/ panel editor ---------------------------------------------------------------------------------------------------------------->
	<script>
		ClassicEditor
			.create( document.querySelector( '#editor' ), {
				// toolbar: [ 'heading', '|', 'bold', 'italic', 'link' ]
			} )
			.then( editor => {
				window.editor = editor;
			} )
			.catch( err => {
				console.error( err.stack );
			} );
	</script>
<!----------------------------------/ panel editor ---------------------------------------------------------------------------------------------------------------->
<!------------------------------------ Notif bar ------------------------------------------------------------------------------------------------------------------>
        <!-- Bxslider -->
        <script>
            (function($) {
                "use strict";
                /*	Bx Slider
                /*----------------------------------------------------*/
                $('.bxslider').bxSlider({
                    nextSelector: '.bx-controls-direction',
                    prevSelector: '.bx-controls-direction',
                    nextText: '',
                    prevText: '',
                    mode: 'vertical',
                    pagerCustom: '#bx-tabs',
                    auto: true,
                    onSlideBefore: function (currentSlideNumber, totalSlideQty, currentObject) {
                        $('.caption h2').removeClass('animated fadeInLeft');
                        $('.caption h1').removeClass('animated fadeInRight');
                        $('.caption p').removeClass('animated fadeInLeft');
                        $('.caption h2').eq(currentObject + 1).addClass('animated fadeInLeft');
                        $('.caption h1').eq(currentObject + 1).addClass('animated fadeInRight');
                        $('.caption p').eq(currentObject + 1).addClass('animated fadeInLeft');
                    }
                });

                // create the notification
                var messages = [
                    //'Une nouvelle MAJ est disponible, relancer votre updater !'
                   
                ];

                var currentIndex = 0;

                function showNextMessage() {
                    if (currentIndex < messages.length) {
                        var notification = new NotificationFx({
                            message: '<div class="ns-content"><p>' + messages[currentIndex] + '</p></div>',
                            layout: 'other',
                            ttl: 6000,
                            effect: 'thumbslider',
                            type: 'success',
                            onClose: function() {
                                currentIndex++;
                                showNextMessage();
                            }
                        });

                        notification.show();
                    }
                }

                $(document).ready(function() {
                    showNextMessage();
                });

                /* Load Content
                /*----------------------------------------------------*/
                $(".loaded-content section").slice(0, 3).show();
                $('#load-more').click(function (e) {
                    e.preventDefault();
                    var btn = $(this)
                    btn.button('loading')
                    setTimeout(function () {
                        btn.button('reset')
                        $(".loaded-content section:hidden").slice(0, 3).fadeIn();
                    }, 5000)//time in ms
                });
            })(jQuery);
        </script>
<!------------------------------------/ Notif bar ------------------------------------------------------------------------------------------------------------------>
<!------------------------------------ progress bar ------------------------------------------------------------------------------------------------------------->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const progressBar = document.getElementById('myProgressBar');
                progressBar.style.opacity = '0'; // Masquer la barre de progression initialement

                window.addEventListener('beforeunload', function() {
                    progressBar.style.opacity = '1'; // Afficher la barre de progression avant de quitter la page
                });
            });

                window.addEventListener('load', function() {
                    const progressBar = document.getElementById('myProgressBar');

                    progressBar.style.width = '100%'; // Charge complète
                    progressBar.style.opacity = '0'; // Masquer la barre de progression initialement
                    progressBar.style.display = 'block'; // Afficher la barre de progression lorsque la page est en cours de chargement

                    setTimeout(() => {
                        progressBar.style.opacity = '0'; // Réduisez l'opacité pour la transition
                        setTimeout(() => {
                            progressBar.style.width = '0'; // Réinitialisez la largeur pour la prochaine page
                            progressBar.style.display = 'none'; // Masquer la barre de progression
                        }, 300); // Durée de la transition en millisecondes
                    }, 500); // Durée d'affichage de la barre de progression en millisecondes
                });
        </script>
<!------------------------------------/ progress bar ------------------------------------------------------------------------------------------------------------->
       <!-- <script type="text/javascript">
            // Désactiver la touche F12
            document.addEventListener('keydown', function (e) {
                if (e.keyCode == 123) {
                    e.preventDefault();
                }
            });

            // Désactiver le clic droit sur la page entière
            document.addEventListener('contextmenu', function (e) {
                e.preventDefault();
            });
        </script>-->
        </body>
</html>
