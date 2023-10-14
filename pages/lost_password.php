<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_116"]; ?></li>
    </ol>
    <?php
    // Vérifier si la connexion est autorisée
    $query = $web->prepare("SELECT lost_password FROM website_general;");
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $lost_passwordAllowed = $result['lost_password'];
    $query->closeCursor();

    // Afficher un message si mdp oublié est désactivé
    if ($lost_passwordAllowed == 'non') {
        echo $translations["ALERTES_007"];
    } else {
// Si le rejoin est activé, afficher le contenu
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 col-xs-12">
                        <section class="section margin-top-20 margin-bottom-20 no-border">
                            <h2 class="page-header text-center no-margin-top"><i class="fa fa-sign-in"></i> <?php echo $translations["MOTS_117"]; ?></h2>
                            <?php
                            function generatePassword($length = 8)
                            {
                                $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                                $count = mb_strlen($chars);

                                for ($i = 0, $result = ''; $i < $length; $i++) {
                                    $index = rand(0, $count - 1);
                                    $result .= mb_substr($chars, $index, 1);
                                }

                                return $result;
                            }

                            if (!isset($_POST['next1']) && !isset($_POST['change'])) { ?>
                                <form autocomplete="off" method="POST" action="#">
                                    <input type="text" class="form-control" name="account" placeholder="Nom de compte"
                                           required="">
                                    <span class="help-block"></span>

                                    <div class="margin-top-20">
                                        <center>
                                            <button type="submit" name="next1" class="btn btn-success pull-mid"><?php echo $translations["MOTS_118"]; ?></button>
                                        </center>
                                    </div>
                                </form>
                                <a href="#"
                                   class="text-dark margin-top-20 padding-top-20 help-block border-top-light btn-icon-right"></a>
                                <?php
                            } else if (isset($_POST['next1']) && isset($_POST['account'])) {

                                $account = filter_var($_POST['account']);
                                $query = $login->prepare("SELECT * FROM world_accounts WHERE account = ?;");
                                $query->bindParam(1, $account);
                                $query->execute();
                                $query->setFetchMode(PDO:: FETCH_OBJ);
                                $row = $query->fetch();
                                $query->closeCursor();
                                if ($row !== false) { ?>
                                    <form autocomplete="off" method="POST"
                                          action="?page=lost_password&user=<?php echo $account; ?>">
                                        <input type="text" class="form-control" name="account"
                                               value="<?php echo $row->account; ?>" disabled>
                                        <span class="help-block"></span>
                                        <input type="text" class="form-control" name="question"
                                               value="<?php echo $row->question; ?>" disabled>
                                        <span class="help-block"></span>
                                        <input type="text" class="form-control" name="answer"
                                               placeholder="Votre réponse..">

                                        <div class="margin-top-20">
                                            <center>
                                                <button type="submit" name="change" class="btn btn-success pull-mid"><?php echo $translations["MOTS_119"]; ?></button>
                                            </center>
                                        </div>
                                    </form>
                                    <a href="#"
                                       class="text-dark margin-top-20 padding-top-20 help-block border-top-light btn-icon-right"></a>
                                    <?php
                                } else {
                                    echo $translations["ALERTES_008"];
                                    echo "<meta http-equiv='refresh' content='3; url=" . URL_SITE . "?page=lost_password'> ";
                                }
                            } else if (isset($_POST['change']) && isset($_POST['answer']) && isset($_GET['user'])) {
                                $account = filter_var($_GET['user']);
                                $answer = filter_var($_POST['answer']);
                                $query = $login->prepare("SELECT * FROM world_accounts WHERE `account` = ? AND `reponse` = ?;");
                                $query->bindParam(1, $account);
                                $query->bindParam(2, $answer);
                                $query->execute();
                                $ok = $query->rowCount();
                                $query->setFetchMode(PDO:: FETCH_OBJ);
                                $row = $query->fetch();
                                $query->closeCursor();

                                if ($ok) {
                                    // Récupérer l'adresse e-mail et le pseudo de l'utilisateur
                                    $email = $row->email;
                                    $pseudo = $row->pseudo;
                                    $newPass = generatePassword(10);
                                    $password = hash('SHA512', md5($newPass));
                                    $query = $login->prepare("UPDATE world_accounts SET `pass` = ? WHERE `account` LIKE ?;");
                                    $query->bindParam(1, $password);
                                    $query->bindParam(2, $account);
                                    $query->execute();
                                    $query->closeCursor();
                                    echo $translations["SUCCESS_005"];
                                    $to = $email;
                                    $subject = "Réinitialisation du mot de passe Saharash";
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
                                </style>
                            </head>
                            <body>
                                <div class='container'>
                                    <div class='header'>
                                        <h1>Réinitialisation du mot de passe</h1>
                                    </div>
                                    <div class='content'>
                                        <p>Bonjour, $pseudo</p>
                                        <p>Veuillez trouver ci-joint votre nouveau mot de passe : <strong>$newPass</strong></p>
                                        <hr>
                                        <p>Cordialement,<br>l'équipe Saharash</p>
                                    </div>
                                </div>
                            </body>
                            </html>";
                                    $headers = "From: Saharash\r\n";
                                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

                                    mail($to, $subject, $message, $headers);


                                    echo "<meta http-equiv='refresh' content='10; url=" . URL_SITE . "'> ";
                                } else {
                                    echo $translations["ALERTES_009"];
                                    echo "<meta http-equiv='refresh' content='3; url=" . URL_SITE . "?page=lost_password'> ";
                                }
                            }
                            ?>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <br/> <br/>
</div>

<!-- ./leftside -->
