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

    // Assurez-vous d'utiliser la variable de connexion correctement ici
    $insertQuery = $web->prepare("INSERT INTO website_logs (guid, account, date, page) VALUES (:guid, :account, :date, :page)");

    // Liez les valeurs aux paramètres nommés
    $insertQuery->bindParam(':guid', $guid, PDO::PARAM_STR);
    $insertQuery->bindParam(':account', $account, PDO::PARAM_STR);
    $insertQuery->bindParam(':date', $date, PDO::PARAM_STR);
    $insertQuery->bindParam(':page', $page, PDO::PARAM_STR);

    // Exécutez la requête SQL d'insertion ici en utilisant la connexion à la base de données
    $insertQuery->execute();
}
?>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=adm_administration">Administration</a></li>
        <li><a href="?page=adm_ticket">Tickets</a></li>
        <li class="active">Envoie ticket</li>
    </ol>
    <?php
    ob_start(); // Début de la mise en mémoire tampon
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $ticketId = $_POST['ticket_id'];
        $replyContent = $_POST['reply_content'];
        $replyDate = date("Y-m-d H:i:s"); // Obtenir la date actuelle au format "Y-m-d H:i:s"

        // Récupérer le pseudo de l'utilisateur à partir de la table world_accounts
        $account = $_SESSION['user'];
        $queryUserPseudo = $login->prepare("SELECT pseudo FROM world_accounts WHERE account = ?;");
        $queryUserPseudo->execute([$account]);
        $row = $queryUserPseudo->fetch(PDO::FETCH_ASSOC);
        $replyPseudo = $row['pseudo'];

        // Insérez la réponse dans la base de données
        $query = $web->prepare("INSERT INTO website_ticket_replies (ticket_id, reply_content, date, pseudo) VALUES (?, ?, ?, ?)");
        $query->execute([$ticketId, $replyContent, $replyDate, $replyPseudo]);

        // Mettez à jour le statut du ticket si nécessaire
        $query = $web->prepare("UPDATE website_ticket_site SET status = ? WHERE id = ?");
        $query->execute(['En cours', $ticketId]);
    }

    ob_end_flush(); // Fin de la mise en mémoire tampon et envoi du contenu au navigateur
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="refresh" content="2;url=?page=adm_ticket&id=<?php echo $ticketId; ?>">
    </head>
    <body>
    <div class='alert alert-success no-border-radius no-margin' style='text-align: center!important;' role='success'><strong>Oh yes!</strong> Votre réponse a été enregistrée. Vous allez être redirigé vers la page de détail du ticket...</div>
    </body>
    </html>
</div>>
