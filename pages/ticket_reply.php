<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_002"]; ?></li>
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
    <meta http-equiv="refresh" content="2;url=?page=ticket&id=<?php echo $ticketId; ?>">
</head>
<body>
<?php echo $translations["SUCCESS_012"]; ?>
</body>
</html>
</div>
