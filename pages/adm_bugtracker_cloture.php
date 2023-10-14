<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'configuration/configuration.php'; // Inclure ta configuration

    $ticketId = $_POST['ticket_id'];

    try {
        $updateQuery = $web->prepare("UPDATE website_bugtracker_site SET status = 'Résolu' WHERE id = :ticket_id");
        $updateQuery->bindParam(':ticket_id', $ticketId);
        $updateQuery->execute();
        echo "Le ticket a été clôturé !";

    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>
