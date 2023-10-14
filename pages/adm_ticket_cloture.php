<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'configuration/configuration.php'; // Inclure ta configuration

    $ticketId = $_POST['ticket_id'];

    try {
        $updateQuery = $web->prepare("UPDATE website_ticket_site SET status = 'Résolu' WHERE id = :ticket_id");
        $updateQuery->bindParam(':ticket_id', $ticketId);
        $updateQuery->execute();
        // Tu n'as pas besoin de rediriger, car la mise à jour se fait en arrière-plan
        echo "Le ticket a été clôturé !";
        // Redirige vers la page actuelle avec l'ID du ticket
       // header("Location: ?page=adm_ticket");
        //exit();
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>
