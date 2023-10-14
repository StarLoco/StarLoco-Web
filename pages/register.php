<style>
    .password-toggle-icon {
        position: relative;
    }

    .toggle-icon {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
    }
</style>
<ol class="breadcrumb">
    <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
    <li class="active"><?php echo $translations["MOTS_007"]; ?></li>
</ol>

<br/> <br/> <br/>
<div class="row">
    <div class="col-md-12">
        <!-- section -->
        <div class="row">
            <div class="col-md-6 col-md-offset-3 col-xs-12">
                <section class="section margin-top-20 margin-bottom-20 no-border">
                    <h2 class="page-header text-center no-margin-top"><i class="ion-clipboard"></i> <?php echo $translations["MOTS_007"]; ?> a <?php echo TITLE; ?></h2>
                    <?php
                    ini_set('display_errors', 1);
                    ini_set('display_startup_errors', 1);
                    error_reporting(E_ALL);

                    function containsInvalidCharacters($input)
                    {
                        $invalidCharacters = array('[', ']', '*', '/', '=', '+', '?', '!', '§', '.', ',', ';', ':', 'ù', '%', 'µ', '$', '£', '¤', '}', '°', ')', 'à', '@', '^', 'ç', '_', '`', 'è', '|', '(', '{', '\'', '"', '#', 'é', '~', '&', '<', '>', '²'); // Ajoutez d'autres caractères invalides ici
                        foreach ($invalidCharacters as $invalidChar) {
                            if (strpos($input, $invalidChar) !== false) {
                                return true; // Si le caractère invalide est trouvé dans le pseudo
                            }
                        }
                        return false; // Si le pseudo est valide
                    }

                    // Récupérer la valeur du champ "inscription" de la table "website_open"
                    $query = $web->query("SELECT inscription FROM website_general;");
                    $inscriptionStatus = $query->fetchColumn();
                    $query->closeCursor();

                    // Vérifier si les inscriptions sont ouvertes ou fermées
                    $inscriptionOpen = ($inscriptionStatus == "oui"); // true si les inscriptions sont ouvertes, false sinon

                    // Vérifier si l'utilisateur est connecté ou si les inscriptions sont ouvertes
                    if (isset($_SESSION['user']) || !$inscriptionOpen) {
                    echo $translations["ALERTES_027"]; "<br />";

                        // Initialiser les variables avec des valeurs par défaut
                        $username = '';
                        $email = '';
                        $question = '';
                        $pseudo = '';
                    } else {
                        if (isset($_POST['register'])) {
                            $username = isset($_POST['username']) ? $_POST['username'] : '';
                            $email = isset($_POST['email']) ? $_POST['email'] : '';
                            $question = isset($_POST['question']) ? $_POST['question'] : '';
                            $pseudo = isset($_POST['pseudo']) ? $_POST['pseudo'] : '';

                            $ok = true;
                            $error = 0;
                            $invalidFields = array();

                            if (!isset($_POST['password']) || !isset($_POST['answer']) || !isset($_POST['security-password'])) {
                                $ok = false;
                            } else {
                                $password = $_POST['password'];
                                $repeat_password = $_POST['repeat-password'];
                                $answer = $_POST['answer'];
                                $security = $_POST['security-password'];

                                if (empty($password) || empty($repeat_password) || empty($answer) || empty($security)) {
                                    $ok = false;
                                } else {
                                    if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
                                        $ok = false;
                                        $error = 10; // password complexity not met
                                        $invalidFields[] = 'password';
                                    }

                                    if ($password !== $repeat_password) {
                                        $ok = false;
                                        $error = 11; // passwords do not match
                                        $invalidFields[] = 'repeat-password';
                                    }
                                    if (checkString($answer)) {
                                        $ok = false;
                                        $error = 1; // invalid field
                                        $invalidFields[] = 'answer';
                                    }
                                    if (checkString($security)) {
                                        $ok = false;
                                        $error = 1; // invalid field
                                        $invalidFields[] = 'security-password';
                                    }
                                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                        $ok = false;
                                        $error = 3; // invalid mail
                                        $invalidFields[] = 'email';
                                    } else {
                                        // Vérification de l'e-mail pour les domaines jetables
                                        $emailDomain = explode('@', $email)[1]; // Obtenir le domaine de l'e-mail

                                        $query = $web->prepare("SELECT COUNT(*) FROM website_register_domaine WHERE domaine = ?");
                                        $query->execute([$emailDomain]);
                                        $domainCount = $query->fetchColumn();

                                        if ($domainCount > 0) {
                                            $ok = false;
                                            $error = 9; // blocked email domain
                                            $invalidFields[] = 'email';
                                            $blockedEmailMsg = $translations["ALERTES_028"];
                                        }
                                    }
                                    if (strtoupper($security) != $_SESSION['captcha']) {
                                        $ok = false;
                                        $error = 4; // invalid captcha
                                        $invalidFields[] = 'security-password';
                                    }
                                    if (!isset($_POST['checkbox'])) {
                                        $ok = false;
                                        $error = 5; // invalid checkbox
                                        $invalidFields[] = 'checkbox';
                                    }
                                    if (empty($pseudo)) {
                                        $ok = false;
                                        $error = 6; // invalid pseudo
                                        $invalidFields[] = 'pseudo';
                                    } else {
                                        // Vérification du pseudo dans la base de données
                                        $query = $login->prepare("SELECT COUNT(pseudo) FROM world_accounts WHERE pseudo = ?;");
                                        $query->bindParam(1, $pseudo);
                                        $query->execute();
                                        $row = $query->fetch();
                                        $query->closeCursor();

                                        if ($row['COUNT(pseudo)'] > 0) {
                                            $ok = false;
                                            $error = 7; // pseudo already taken
                                            $invalidFields[] = 'pseudo';
                                        }
                                        if (containsInvalidCharacters($pseudo)) {
                                            $ok = false;
                                            $error = 8; // invalid character in pseudo
                                            $invalidFields[] = 'pseudo';
                                            $invalidCharMsg = $translations["ALERTES_029"];
                                        }
                                    }
                                }
                            }

                            $configFile = 'configuration/configuration.php';
                            require_once($configFile);

                            if ($ok) {
                                $query = $login->prepare("SELECT COUNT(account) FROM world_accounts WHERE account = ?;");
                                $query->bindParam(1, $username);
                                $query->execute();
                                $row = $query->fetch();
                                $query->closeCursor();

                                if ($row['COUNT(account)'] > 0) {
                                    echo $translations["ALERTES_030"] . "<br />";
                                } else {
                                    // Génération d'un jeton unique
                                    $emailToken = md5(uniqid());

                                    $query = $login->prepare("INSERT INTO world_accounts (account, pass, email, question, reponse, dateRegister, pseudo, email_token) VALUES (?, ?, ?, ?, ?, ?, ?, ?);");

                                    $query->bindParam(1, $username);
                                    $password = hash("SHA512", md5($password));
                                    $query->bindParam(2, $password);
                                    $query->bindParam(3, $email);
                                    $query->bindParam(4, $question);
                                    $query->bindParam(5, $answer);
                                    $date = date('d/m/y H:i');
                                    $query->bindParam(6, $date);
                                    $query->bindParam(7, $pseudo);
                                    $query->bindParam(8, $emailToken);

                                    $query->execute();
                                    $query->closeCursor();

                                    echo $translations["SUCCESS_010"] . "<br />";

                                    $confirmationLink = URL_SITE . "?page=email_confirmation&token=" . $emailToken;
                                    $to = $email;
                                    $subject = "Confirmation d'adresse e-mail Saharash";
                                    $message = "
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
            }
            .container {
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #ffffff;
                border: 1px solid #e1e1e1;
            }
            .header {
                background-color: #3B71B6;
                color: #ffffff;
                padding: 10px 0;
                text-align: center;
            }
            .content {
                padding: 20px;
            }
            .button {
                display: inline-block;
                padding: 10px 20px;
                background-color: #2980B9;
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
                border: none;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Confirmation d'adresse e-mail</h1>
            </div>
            <div class='content'>
                <p>Bonjour, $pseudo</p>
                <p>Vous devez procéder à une vérification simple avant de créer votre compte ". TITLE ." Cette adresse email est-elle bien la vôtre ? Veuillez confirmer qu'il s'agit de la bonne adresse à utiliser pour votre nouveau compte. Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse e-mail :</p>
                <center>
                    <a href='$confirmationLink' class='button'>Activer mon compte</a>
                </center>
                <hr>
                <p>Cordialement,<br>L'équipe Saharash</p>
                <p>Si vous avez des difficultés à cliquer sur le bouton \"Vérification de l'adresse e-mail\", copiez et collez l'URL ci-dessous dans votre navigateur Web :</p>
                <p>$confirmationLink</p>
            </div>
        </div>
    </body>
    </html>";
                                    $headers = "Saharash\r\n";
                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                                    mail($to, $subject, $message, $headers);


                                    echo "<meta http-equiv='refresh' content='10; url=" . URL_SITE . "'> ";
                                }
                            } else {
                                if (!empty($invalidFields)) {
                                    if (in_array('pseudo', $invalidFields) && $error == 7) {
                                        echo $translations["ALERTES_031"] . "<br />";
                                    } else if ($error == 10) {
                                        echo $translations["ALERTES_032"] . "<br />";
                                    } else {
                                        echo $translations["ALERTES_033"] . implode(', ', $invalidFields) . "</div><br />";

                                    }
                                }
                                if (in_array('pseudo', $invalidFields) && $error == 8) {
                                    echo $translations["ALERTES_034"] . $invalidCharMsg . "</div><br />";
                                }
                                if (!empty($blockedEmailMsg)) {
                                    echo $translations["ALERTES_034"] . $blockedEmailMsg . "</div><br />";

                                }
                            }
                        } else {
                            // Initialiser les variables avec des valeurs par défaut
                            $username = '';
                            $email = '';
                            $question = '';
                            $pseudo = '';
                        }
                    }
                    ?>
                    <form method="POST" action="#">
                        <div class="row">
                            <div class="control-group col-md-6 col-xs-12">
                                <label class="control-label" for="username"><?php echo $translations["MOTS_179"]; ?></label>
                                <div class="controls margin-top-5">
                                    <input type="text" class="form-control" name="username"
                                           value="<?php echo $username; ?>" required>
                                </div>
                            </div>
                            <div class="control-group col-md-6 col-xs-12">
                                <label class="control-label" for="email"><?php echo $translations["MOTS_180"]; ?></label>
                                <div class="controls margin-top-5">
                                    <input type="text" class="form-control" id="email" name="email"
                                           value="<?php echo $email; ?>" required>
                                </div>
                            </div>
                            <div class="control-group col-md-6 col-xs-12 margin-top-10">
                                <label class="control-label" for="password"><?php echo $translations["MOTS_181"]; ?></label>
                                <div class="controls margin-top-5">
                                    <div class="password-toggle-icon">
                                        <input type="password" class="form-control" id="password" name="password" value="" required oninput="updatePasswordComplexity(this.value)">
                                        <i class="fa-solid fa-eye toggle-icon" onclick="togglePasswordVisibility('password')">&nbsp;&nbsp;<img src="img/register/passwordpower0.png" alt="Password Complexity" id="password-complexity-image" class="password-complexity-image"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="control-group col-md-6 col-xs-12 margin-top-10">
                                <label class="control-label" for="repeat-password"><?php echo $translations["MOTS_182"]; ?></label>
                                <div class="controls margin-top-5">
                                    <div class="password-toggle-icon">
                                        <input type="password" class="form-control" id="repeat-password" name="repeat-password" value="" required>
                                        <i class="fa-solid fa-eye toggle-icon" onclick="togglePasswordVisibility('repeat-password')"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group col-md-6 col-xs-12 margin-top-10">
                                <label class="control-label" for="question"><?php echo $translations["MOTS_183"]; ?></label>
                                <div class="controls margin-top-5">
                                    <input type="text" class="form-control" id="question" name="question"
                                           value="<?php echo $question; ?>" required>
                                </div>
                            </div>
                            <div class="control-group col-md-6 col-xs-12 margin-top-10">
                                <label class="control-label" for="answer"><?php echo $translations["MOTS_184"]; ?></label>
                                <div class="controls margin-top-5">
                                    <input type="text" class="form-control" id="answer" name="answer" value="" required>
                                </div>
                            </div>

                            <div class="control-group col-md-6 col-xs-12 margin-top-10">
                                <label class="control-label" for="answer"><?php echo $translations["MOTS_185"]; ?></label>
                                <div class="controls margin-top-5">
                                    <input type="text" class="form-control" id="pseudo" name="pseudo"
                                           value="<?php echo $pseudo; ?>" required>
                                </div>
                            </div>

                            <div class="control-group col-md-12 col-xs-12">
                                <label class="control-label margin-top-10" for="security-password"><?php echo $translations["MOTS_186"]; ?></label>

                                <div class="control-label margin-top-10">
                                    <img class="" src="./img/captcha.php" alt="Captcha">
                                </div>

                                <div class="controls margin-top-5">
                                    <input type="text" class="form-control" id="security-password"
                                           name="security-password" value="" required>
                                </div>

                            </div>
                            <br/>
                            <div style="margin-top: 15px;" class="control-group col-md-12 col-xs-12">
                                <div class="checkbox pull-left no-padding no-margin-bottom margin-top-5">
                                    <input type="checkbox" id="checkbox1" name="checkbox">
                                    <label for="checkbox1"><?php echo $translations["MOTS_187"]; ?></label>
                                </div>
                                <button type="submit" name="register" class="btn btn-success pull-right">Terminer l'inscription
                                </button>
                            </div>
                        </div>
                    </form>
                    <h2 class="page-header text-center no-margin-top"></h2>
                </section>
            </div>
        </div>
    </div>
</div>
<script>
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling; // Get the sibling icon element

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function updatePasswordComplexity(password) {
        const passwordComplexityLevels = [
            { level: 0, imagePath: 'img/register/passwordpower0.png' },
            { level: 1, imagePath: 'img/register/passwordpower1.png' },
            { level: 2, imagePath: 'img/register/passwordpower2.png' },
            { level: 3, imagePath: 'img/register/passwordpower3.png' },
            { level: 4, imagePath: 'img/register/passwordpower4.png' },
            { level: 5, imagePath: 'img/register/passwordpower5.png' },
        ];

        let complexityLevel = 0;

        if (password.length >= 6) {
            complexityLevel++; // Ajoute 1 au niveau de complexité si la longueur du mot de passe est d'au moins 6 caractères
        }

        if (/[A-Z]/.test(password)) {
            complexityLevel++; // Ajoute 1 au niveau de complexité si le mot de passe contient au moins une lettre majuscule
        }

        if (/[0-9]/.test(password)) {
            complexityLevel++; // Ajoute 1 au niveau de complexité si le mot de passe contient au moins un chiffre
        }

        if (/[@#$%^&*!]/.test(password)) {
            complexityLevel++; // Ajoute 1 au niveau de complexité si le mot de passe contient au moins un caractère spécial parmi [@#$%^&*!]
        }

        if (/[a-z]/.test(password)) {
            complexityLevel++; // Ajoute 1 au niveau de complexité si le mot de passe contient au moins une lettre minuscule
        }

        // Update the password complexity image
        const complexityImage = document.getElementById('password-complexity-image');
        for (const levelData of passwordComplexityLevels) {
            if (complexityLevel >= levelData.level) {
                complexityImage.src = levelData.imagePath;
            } else {
                break;
            }
        }
    }
</script>
<!-- ./leftside -->
