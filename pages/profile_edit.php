<?php
session_start();

if (!isset($_SESSION['user'])) {
    echo "<script>window.location.replace(\"?page=signin\")</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['avatar'])) {
    $avatar = $_POST['avatar'];
    $user = $_SESSION['user']; // Récupérez le compte de l'utilisateur depuis la session



    // Mettez à jour l'avatar de l'utilisateur dans la base de données
    $query = $login->prepare("UPDATE world_accounts SET avatar = :avatar WHERE account = :user"); // Assurez-vous que la table et les colonnes sont correctes
    $query->bindParam(':avatar', $avatar, PDO::PARAM_STR);
    $query->bindParam(':user', $user, PDO::PARAM_STR);
    $query->execute();

    // Redirigez l'utilisateur vers une page de profil ou une autre page de votre choix
    echo "<script>window.location.replace(\"?page=profile\")</script>";
}
?>

<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_135"]; ?></li>
    </ol>
    <form method="post" action="">
        <p>Choisissez votre avatar :</p>
        <?php
        // Répertoire où sont stockées les images d'avatar
        $repertoire_avatars = "img/avatar/";

        // Liste des fichiers d'avatar dans le répertoire
        $avatars = glob($repertoire_avatars . "*.{jpg,png,webp}", GLOB_BRACE);// Vous pouvez ajuster l'extension selon vos besoins

        $avatarsExclus = ["avatar.jpg", "avatar2.jpg", "1.jpg"]; // Liste des avatars à exclure

        foreach ($avatars as $avatar) {
            $nomAvatar = basename($avatar);

            // Vérifiez si l'avatar est dans la liste des avatars exclus
            if (!in_array($nomAvatar, $avatarsExclus)) {
                echo '<label><input type="radio" name="avatar" value="' . $nomAvatar . '"><img src="' . $avatar . '" alt="" width="80" height="80"></label>';
            }
        }
        ?>
        <br/>
        <br/>
        <center><button type="submit" class="btn btn-primary">Valider</button></center>
    </form>
</div>
<!-- ./leftside -->
