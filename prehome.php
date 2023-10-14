<?php
$configFile = 'configuration/configuration.php';
require_once($configFile);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saharash</title>
    <link rel="shortcut icon" href="img/favi.ico">
</head>
    <style>
        p {
            color: #fff;
            line-height: 1.6;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            text-align: center;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }
        .content-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .prehome-container {
            background-image: url("css/themes/prehome/iop/bg-text.png");
            color: #fff !important;
            padding: 179px;
            max-width: 811px;
            margin-right: -1074px;
            margin-top: 292px;
        }

        h1 {
            color: #E5BF7B;
        }

        .start-button {
            background-color: #ff6100;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            margin-top: -162px;
            margin-left: 1074px;
            transition: background-color 0.3s ease;
        }

        .start-button:hover {
            background-color: #f37427; /* Couleur au survol */
            color: #fff;
        }

        /* Animation CSS pour afficher le texte progressivement */
        .animate-text {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 1s ease forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
<body>
<video class="VideoPane-video" src="css/themes/prehome/iop/prehome.mp4" data-src="video/DOFUS - Cinématique d'introduction.mp4" loop="loop" muted="muted" autoplay="autoplay" playsinline="playsinline"></video>
<div class="content-wrapper">
    <div class="prehome-container">
        <h1><?php echo TITLE ?></h1>
        <p class="animate-text">Plongez dans un univers médiéval fantastique où dragons, héros et divinités se disputent les précieux Dofus.</p>
        <p class="animate-text">Affrontez seul ou à plusieurs des monstres de plus en plus robustes dans une mécanique au tour par tour.</p>
        <p class="animate-text">De nombreux donjons aux décors, histoires et boss originaux vous attendent pour mettre à rude épreuve votre force, votre ténacité et votre stratégie.</p>
        <p class="animate-text">Découvrez un monde en perpétuelle évolution, forgez votre légende et rejoignez la quête ultime pour la domination des Dofus. Relevez le défi dès maintenant en cliquant sur Jouer gratuitement !</p>
    </div>
    <a href="<?php echo URL_SITE ?>" class="start-button">Jouer gratuitement !</a>
</div>
<script>
    // Masquer les contrôles vidéo
    const video = document.getElementById('background-video');
    video.controls = false;
</script>
<script type="text/javascript">
    // Désactiver le clic droit sur la page entière
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });
    // Désactiver la touche F12
    document.addEventListener('keydown', function (e) {
        if (e.keyCode == 123) {
            e.preventDefault();
        }
    });

</script>
</body>
</html>
