<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    
    $configFile = 'configuration/configuration.php';
    require_once($configFile);
    
    // Récupérer la valeur de minuterie_ouverture depuis la base de données
    $query = $web->prepare('SELECT minuterie_ouverture, ouverture FROM website_general');
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $minuterieOuverture = $result['minuterie_ouverture'];
    $ouverture = $result['ouverture'];

    // Vérifier que la valeur n'est pas vide ou nulle
    if ($minuterieOuverture !== null && $minuterieOuverture !== '') {
        // Convertir la date en timestamp et la formater en format ISO 8601
        $formattedMinuterieOuverture = date('c', strtotime($minuterieOuverture));
    } else {
        // Valeur par défaut si minuterie_ouverture est nul ou vide
        $formattedMinuterieOuverture = null;
    }

    // Vérifier si la requête est une requête POST et si le jeu n'est pas déjà ouvert
    if ($_SERVER["REQUEST_METHOD"] === "POST" && $ouverture !== 'non') {
        // Mettre à jour le champ "ouverture" dans la base de données
        $query = $web->prepare('UPDATE website_general SET ouverture = "non"');
        $query->execute();
        // Réponse de la mise à jour de la base de données (optionnel)
        echo "Champ 'ouverture' mis à jour avec succès.";
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ouverture prochainement</title>
    <link rel="icon" type="image/x-icon" href="./img/favi.ico" />
    <style>
        /* Styles CSS pour le fond d'écran et le fondu noir */
        body {
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-image: url("./img/ouverture/carte.png");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            position: relative;
            width: 100%;
            height: 100vh;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.5);
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .overlay h1 {
            color: white;
            font-size: 30px;
            margin-bottom: 20px;
        }

        .overlay p.centered-text {
            color: white;
            font-size: 18px;
            text-align: center;
			 z-index: 9999;
        }

        /* Styles pour le fondu noir */
        .fade-out {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: black;
            opacity: 0;
            z-index: 9998;
            pointer-events: none;
            transition: opacity 1s ease;
        }

        .fade-out.fade-out-active {
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="overlay">
            <h1>Bienvenue sur <?php echo TITLE ?></h1>
            <p class="centered-text" id="countdown">
                <span id="countdown-prefix">Le jeu commencera dans </span>
                <span id="countdown-timer">0 mois, 21 jours, 3 minutes et 21 secondes</span>
            </p>
        </div>
    </div>
    <!-- Div pour le fondu noir -->
    <div class="fade-out" id="fade-out"></div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var targetDate = <?php echo json_encode($formattedMinuterieOuverture); ?>;
            var fadeOutElement = document.getElementById("fade-out");
            var countdownElement = document.getElementById("countdown-timer");
            var countdownPrefix = document.getElementById("countdown-prefix");

            if (targetDate !== null) {
                targetDate = new Date(targetDate).getTime();

                var countdownInterval = setInterval(function () {
                    var now = new Date().getTime();
                    var timeDifference = targetDate - now;

                    if (timeDifference <= 0) {
                        clearInterval(countdownInterval);
                        countdownElement.textContent = "Que l'aventure commence ! ";
                        countdownPrefix.textContent = ""; // Efface le texte "Le jeu commencera dans" lorsque le jeu a commencé

                        // Activer le fondu noir progressivement
                        fadeOutElement.classList.add("fade-out-active");

                        // Mettre à jour le champ "ouverture" dans la base de données en utilisant AJAX
                        var xhr = new XMLHttpRequest();
                        xhr.open("POST", "", true);
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.onreadystatechange = function () {
                            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                                // Réponse de la mise à jour de la base de données (optionnel)
                                console.log(xhr.responseText);
                            }
                        };
                        xhr.send();

                        // Ajoutez l'URL de redirection ici (par exemple, rediriger vers la page d'accueil du jeu) après 1 seconde
                        setTimeout(function () {
                            var redirectURL = "<?php echo URL_SITE ?>";
                            window.location.href = redirectURL;
                        }, 1000);
                    } else {
                        var months = Math.floor(timeDifference / (1000 * 60 * 60 * 24 * 30));
                        var days = Math.floor((timeDifference % (1000 * 60 * 60 * 24 * 30)) / (1000 * 60 * 60 * 24));
                        var minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);

                        var countdownText = months + " mois, " + days + " jours, " + minutes + " minutes et " + seconds + " secondes";
                        countdownElement.textContent = countdownText;

                        // Vérifier si le compte à rebours atteint 60 secondes, puis activer le fondu noir progressivement
                        if (timeDifference <= 60000) {
                            var opacityValue = 1 - timeDifference / 60000;
                            fadeOutElement.style.opacity = opacityValue;
                        }
                    }
                }, 1000);
            } else {
                var countdownElement = document.getElementById("countdown-timer");
                countdownElement.textContent = "La date d'ouverture n'est pas définie.";
                var countdownPrefix = document.getElementById("countdown-prefix");
                countdownPrefix.textContent = ""; // Efface le texte "Le jeu commencera dans" si la date d'ouverture n'est pas définie
            }
        });
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
