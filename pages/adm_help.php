<?php

if (!isset($_SESSION['user']) || !in_array($_SESSION['data']->guid, ADMIN_GUID)) {
    // Code de sortie ou de redirection si les conditions ne sont pas satisfaites
    exit;
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $guid = $_SESSION['id'];
    $account = $_SESSION['user']; // Assurez-vous de récupérer l'identifiant de l'utilisateur connecté correctement
    $date = date('Y-m-d H:i:s');

    // Requête pour obtenir la valeur du champ 'log' depuis la table 'website_general'
    $getLogSettingQuery = $web->prepare("SELECT log FROM website_general");
    $getLogSettingQuery->execute();
    $logSetting = $getLogSettingQuery->fetchColumn(); // Récupère la valeur du champ 'log'

// Si la valeur du champ 'log' est "oui", procédez à la journalisation
    if ($logSetting === "oui") {
        $insertQuery = $web->prepare("INSERT INTO website_logs (guid, account, date, page) VALUES (:guid, :account, :date, :page)");

        // Liez les valeurs aux paramètres nommés
        $insertQuery->bindParam(':guid', $guid, PDO::PARAM_STR);
        $insertQuery->bindParam(':account', $account, PDO::PARAM_STR);
        $insertQuery->bindParam(':date', $date, PDO::PARAM_STR);
        $insertQuery->bindParam(':page', $page, PDO::PARAM_STR);

        // Exécutez la requête SQL d'insertion ici en utilisant la connexion à la base de données
        $insertQuery->execute();
    }
}
?>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li class="active">Help site</li>
    </ol>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="no-border no-padding-top">
                <div class="page-header margin-top-10"><h4>Page d'aide a la configuration du site</h4></div>
            </section>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <section class="section section-white no-border no-padding-top">
                <div class="section section-default padding-25">
                    <ul>
                        <h1>Histoire :</h1>
                        <br/>
                        Le site est une base de Starloco avec une modification quasi générale recoder en php 8.1.
                        <hr/>
                        <h1>Configuration :</h1>
                        <br/>
                        <li>Je vous conseille de mettre votre site sur une base LINUX "DEBIAN" qui permettra de consommer moins.<br></li>
                        - INSTALLER DEBIAN 11 dernière version a ce jour.<br>
                        - INSTALLER MYSQL-SERVER "apt install mysql-server" une fois installer mettre les SQL dedans<br>
                        - INSTALLER SSMTP "apt install ssmtp" qui permettra l'envoie de mail pour la partie
                        enregistrement du site.<br>
                        <br/>
                        <li>Le site dispose de l'inscription et mot de passe oublier par mail qui généra automatiquement un token pour la
                            vérification du mail et un envoie direct du mot de passe en cas d'oublie,
                            pour la configuration de "ssmtp" copier-coller cette config :
                        </li>
                        <br>
                        #<br>
                        # /etc/ssmtp.conf -- a config file for sSMTP sendmail.<br>
                        #<br>
                        # Serveur SMTP à utiliser<br>
                        mailhub=SMTP:PORT<br>
                        # Réécrire le domaine de l'expéditeur<br>
                        rewriteDomain=VOTRE DOMAINE<br>
                        # Nom de la machine<br>
                        hostname=LE NOM DE VOTRE MACHINE<br>
                        ## Mettre YES permet au programme qui envoie un courriel de modifier l'entête du message
                        concernant l'émetteur.<br>
                        FromLineOverride=YES<br>
                        ## Authentification sur le relais smtp<br>
                        UseSTARTTLS=yes<br>
                        UseTLS=yes<br>
                        #IMPORTANT: The following line is mandatory for TLS authentication<br>
                        TLS_CA_File=/etc/pki/tls/certs/ca-bundle.crt<br>
                        ## Nom d'utilisateur SMTP<br>
                        AuthUser=EMAIL D'ENVOIE<br>
                        ## Mot de passe associe au compte<br>
                        AuthPass=MDP EMAIL<br/>
                        <br/>
                        <li>Vous devais aussi configurer le fichier configuration qui est dans le dossier: "configuration/configuration.php</li><br>
                        <br/>
                        <h1>Fonctionnalités :</h1>
                        <br/>
                        <li>Une page d'ouverture de serveur qui est paramétrable dans la section Admin --> gestion du site
                            qui permet de mettre la date et l'heure d'ouverture et d'appuyer sur le switch pour l'activer ou le désactiver. Une fois le chrono a 60 secondes de l'ouverture
                            le fond va s'assombrir jusqu'à qu'on ne voit plus rien, un rafraîchissement de la page se fera pour montrer le site.
                        </li>
                        <br/>
                        <li>Une page Maintenance qui permettra de faire votre maintenance en toute tranquillité, si vous êtes connecté avant de lancer la maintenance avec le switch qui est dans
                            la page ADMIN -> gestion du site, vous pourrez naviguer tranquillement, mais ce qui n'est pas connecté en tant qu'administrateur verrons la page maintenance.
                        </li>
                        <br/>
                        <li>Vous avez aussi le choix d'activer ou de désactiver toujours dans la même page toutes les pages du site, si un problème avec une de ces pages op le switch et la page sera désactivée jusqu'à ce que vous la réactiviez.</li>
                        <br/>
                        <li>Une page NEWS qui permettra de mettre des news sur l'accueil ou vous pourrez ajouter, modifier ou de supprimer avec un tableau qui affichera 10 par 10.
                            Pour de ce qui est de l'ajout vous avez une fonctionnalité avec un calendrier qui permettra de mettre les news à l'avance et le jour J celle-ci se publiera. </li>
                        <br/>
                        <li>Une page NEWS RSS qui permet de faire la même operation que les news normal.</li>
                        <br/>
                        <li>Une page ACCOUNT et PERSO qui permettra d'administrer vos comptes et personnages en regardant ou en supprimant ceux-ci.</li>
                        <br/>
                        <li>Une page LOTERIE OU GAGNANT LOTERIE qui permettra d'administrer vos loteries avec un code qui génère automatiquement ou en le modifiant à votre guise, avec des probabilités, des min maxi que le code peut être utilisé,
                            ainsi qu'une page gagnant pour voir qui a gagné et à quelle heure et quel code.</li>
                        <br/>
                        <li>Un système de SUPPORT qui permet aux administrateurs et autres de répondre au ticket, support, bugtracker et de voir aussi l'erreur qui y aurait dans l'encyclopédie "manque objet ou monstre". </li>
                        <br/>
                        <li>Une page DOMAINE est aussi accessible qui permettra d'ajouter ou non des domaines jetables qui permettra à la page INSCRIPTION de les rejeter.</li>
                        <br/>
                        <li>Une page ACHAT point et boutique qui permet de visualiser si une personne achète ou pas avec la date, etc..</li>
                        <br/>
                        <li>Une page BOUTIQUE qui permet d'ajouter, supprimer ou modifier celle-ci, ainsi qu'une page pour modifier si les catégories sont accessibles ou pas "armes, chapeaux, etc…</li>
                        <br/>
                        <li>Et pour finir un système de LOG sur la partie ADMIN qui permettra de savoir qui a fait quoi et à quelle heure.</li>
                    </ul>
                </div>
            </section>
        </div>
    </div>
</div>
<!-- ./leftside -->
