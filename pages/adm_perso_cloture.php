<?php
var_dump($_POST);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'configuration/configuration.php'; // Inclure ta configuration

    $persosId = $_POST['perso_id']; // Utilise 'account_id' au lieu de 'guid'

    try {
        $deleteQuery = $login->prepare("DELETE FROM world_players WHERE id = :perso_id");
        $deleteQuery->bindParam(':perso_id', $persosId);
        $deleteQuery->execute();
        echo "Le personnage a bien été supprimé !";
    } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
    }
}
?>
