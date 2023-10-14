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

$sectionsToShow = array();

// Vérifier les autorisations pour chaque section et décider si elle doit être affichée
if (isset($restrictedPagesPermissions['adm_site']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_site'])) {
    $sectionsToShow[] = 'Panel General';
}
if (isset($restrictedPagesPermissions['adm_news']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_news'])) {
    $sectionsToShow[] = 'NEWS';
}
if (isset($restrictedPagesPermissions['adm_account']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_account'])) {
    $sectionsToShow[] = 'ACCOUNTS - PERSOS';
}
if (isset($restrictedPagesPermissions['adm_loterie']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_loterie'])) {
    $sectionsToShow[] = 'LOTERIE';
}
if (isset($restrictedPagesPermissions['adm_ticket']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_ticket'])) {
    $sectionsToShow[] = 'SUPPORT';
}
if (isset($restrictedPagesPermissions['adm_categorie_boutique']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_categorie_boutique'])) {
    $sectionsToShow[] = 'BOUTIQUE';
}
if (isset($restrictedPagesPermissions['adm_achat_point']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_achat_point'])) {
    $sectionsToShow[] = 'ACHATS';
}
?>
<style>
    .boutonhelp {
        position: absolute;
        right: 710px; /* Ajuste la valeur selon le décalage horizontal souhaité */
        top: 300px;
        z-index: 9999;
    }

</style>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index">Accueil</a></li>
        <li class="active">Panel d'administration</li>
    </ol>
    <div class="boutonhelp"><a href="?page=adm_help" title="HELP pour le site"><img src="img/devtool/infos.png"/></a>
    </div>
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="page-header margin-top-10"><h4>Panel d'administration</h4></div>
            <?php foreach ($sectionsToShow as $sectionTitle) : ?>
                <div class="section section-default padding-25">
                    <div class="page-header margin-top-10"><h4><?= $sectionTitle ?></h4></div>
                    <?php if ($sectionTitle === 'Panel General') : ?>
                        <?php if (isset($restrictedPagesPermissions['adm_site']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_site'])) : ?>
                            <a href="?page=adm_site">Gestion du site</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_gestion_domaine']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_gestion_domaine'])) : ?>
                            <a href="?page=adm_gestion_domaine">Gestion des domaines poubelle</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_logs_site']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_logs_site'])) : ?>
                            <a href="?page=adm_logs_site">Logs site</a><br/>
                        <?php endif; ?>

                    <?php elseif ($sectionTitle === 'NEWS') : ?>
                        <?php if (isset($restrictedPagesPermissions['adm_news']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_news'])) : ?>
                            <a href="?page=adm_news">Gestion des News</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_commentaires']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_commentaires'])) : ?>
                            <a href="?page=adm_commentaires">Gestion des Commentaires</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_news_rss']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_news_rss'])) : ?>
                            <a href="?page=adm_news_rss">Gestion des News RSS</a><br/>
                        <?php endif; ?>

                    <?php elseif ($sectionTitle === 'ACCOUNTS - PERSOS') : ?>
                        <?php if (isset($restrictedPagesPermissions['adm_account']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_account'])) : ?>
                            <a href="?page=adm_account">Gestion des Accounts</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_perso']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_perso'])) : ?>
                            <a href="?page=adm_perso">Gestion des Personnages</a><br/>
                        <?php endif; ?>

                    <?php elseif ($sectionTitle === 'LOTERIE') : ?>
                        <?php if (isset($restrictedPagesPermissions['adm_loterie']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_loterie'])) : ?>
                            <a href="?page=adm_loterie">Gestion de la loterie</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_loterie_gagnant']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_loterie_gagnant'])) : ?>
                            <a href="?page=adm_loterie_gagnant">Gestion des gagnant de la loterie</a><br/>
                        <?php endif; ?>

                    <?php elseif ($sectionTitle === 'SUPPORT') : ?>
                        <?php if (isset($restrictedPagesPermissions['adm_ticket']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_ticket'])) : ?>
                            <a href="?page=adm_ticket">Gestion des tickets</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_bugtracker']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_bugtracker'])) : ?>
                            <a href="?page=adm_bugtracker">Gestion des bugs</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_tickets_encyclopedie']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_tickets_encyclopedie'])) : ?>
                            <a href="?page=adm_tickets_encyclopedie">Gestion des erreurs encyclopédie</a><br/>
                        <?php endif; ?>

                    <?php elseif ($sectionTitle === 'BOUTIQUE') : ?>
                        <?php if (isset($restrictedPagesPermissions['adm_categorie_boutique']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_categorie_boutique'])) : ?>
                            <a href="?page=adm_categorie_boutique">Gestion des catégories de la boutique</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_boutique']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_boutique'])) : ?>
                            <a href="?page=adm_boutique">Gestion de la boutique</a><br/>
                        <?php endif; ?>

                    <?php elseif ($sectionTitle === 'ACHATS') : ?>
                        <?php if (isset($restrictedPagesPermissions['adm_achat_point']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_achat_point'])) : ?>
                            <a href="?page=adm_achat_point">Gestion des achats de points</a><br/>
                        <?php endif; ?>
                        <?php if (isset($restrictedPagesPermissions['adm_achat_boutique']) && in_array($_SESSION['data']->guid, $restrictedPagesPermissions['adm_achat_boutique'])) : ?>
                            <a href="?page=adm_achat_boutique">Gestion des achats de boutique</a><br/>
                        <?php endif; ?>

                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

					
					