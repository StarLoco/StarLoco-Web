<?php
var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'configuration/configuration.php'; // Inclure ta configuration

    $accountId = $_POST['account_id']; // Utilise 'account_id' au lieu de 'guid'

    try {
        $deleteQuery = $login->prepare("DELETE FROM world_accounts WHERE guid = :account_id");
        $deleteQuery->bindParam(':account_id', $accountId);
        $deleteQuery->execute();
        echo "Le compte a bien été supprimé !";
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>
