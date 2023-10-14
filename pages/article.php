<?php
// Vérifier si la connexion est autorisée
$query = $web->prepare("SELECT commentaire FROM website_general;");
$query->execute();
$result = $query->fetch(PDO::FETCH_ASSOC);
$commentaireAllowed = $result['commentaire'];
$query->closeCursor();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Assurez-vous que le formulaire est valide et que les données sont correctes
    $comment_text = $_POST['content'];
    $account = $_SESSION['user'];
    $comment_date = date('Y-m-d H:i:s');
    $article_id = $_POST['article_id'];


    $queryUserPseudo = $login->prepare("SELECT pseudo FROM world_accounts WHERE account = ?;");
    $queryUserPseudo->execute([$account]);
    $row = $queryUserPseudo->fetch(PDO::FETCH_ASSOC);
    $replyPseudo = $row['pseudo'];


    if (!empty($comment_text)) {
        // Le commentaire n'est pas vide, vous pouvez l'insérer dans la base de données
        $query = $web->prepare("INSERT INTO website_timeline_news_comments (article_id, pseudo, comment_text, comment_date) VALUES (?, ?, ?, ?)");
        $query->execute([$article_id, $replyPseudo, $comment_text, $comment_date]);
        // Message de succès
        echo "<script>window.location.href = '?page=article&id=$article_id';</script>";
    } else {
        // Le commentaire est vide, affichez un message d'erreur ou faites ce que vous souhaitez
//        echo $translations["ALERTES_047"];

    }

    // Message de succès
    echo "<script>window.location.href = '?page=article&id=$article_id';</script>";

}

?>
<style>
    /* styles.css */

    /* Style pour l'article */
    .article {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
    }

    /* Style pour l'image */
    .article img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 0 auto;
        border-radius: 5px;
    }

    /* Style pour le titre */
    .article h1 {
        font-size: 24px;
        font-weight: bold;
        margin-top: 20px;
        margin-bottom: 10px;
    }

    /* Style pour la date */
    .article .date {
        font-size: 14px;
        color: #777;
    }

    /* Style pour le contenu de l'article */
    .article .content {
        font-size: 16px;
        line-height: 1.5;
        margin-top: 20px;
    }
    .news-icon {
        float: left;
        margin-right: 30px; /* Ajustez la marge à votre convenance */
    }

    .comments {
        margin-top: 30px;
    }

    .comment-box {
        border: 1px solid #ccc;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .comment-box p {
        margin: 0;
    }

    .comment-box p strong {
        color: #53a0e0;
        font-weight: bold;
    }

    .comments h2 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    .avatar-container {
        margin-top: -10px;
    }
    .pseudo-container {
        margin-top: -54px;
        margin-left: 63px;
    }
    .comment-container {
        margin-left: 64px;
        margin-top: 8px;
    }
    .avatar-modo {
        background: url("../img/assets/bg-staff.jpg");
    }

</style>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_227"];?></li>
    </ol>
    <?php
    // Votre code de connexion à la base de données ici

    // Vérifiez si l'ID de l'article est valide
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $articleId = $_GET['id'];

        // Requête SQL pour récupérer les détails de l'article par son ID
        $query = $web->prepare('SELECT title, visibilite, content, img, icon, commentaire FROM website_timeline_news WHERE id = ?');
        $query->bindParam(1, $articleId, PDO::PARAM_INT);
        $query->execute();
        $article = $query->fetch(PDO::FETCH_OBJ);

        // Vérifiez si l'article existe
        if ($article) {
            // Convertissez la date au format français
            $dateVisibilite = new DateTime($article->visibilite);
            setlocale(LC_TIME, 'fr_FR.UTF-8');
            $dateFormatee = strftime('%d %B %Y, %H:%M', $dateVisibilite->getTimestamp());

            // Affichez les détails de l'article avec une mise en page stylisée
            echo '<article class="article">';
            echo '<h1><img src="./img/icon_news/' . $article->icon . '.png" alt="News" class="news-icon" />   ' . $article->title . '</h1>';
            echo '<p class="date">' . $dateFormatee . '</p>';
            echo '<hr>';
            if (!empty($article->img)) {
                echo '<img class="img-responsive full-width" src="./img/news/' . $article->img . '" alt="Image de l\'article">';
            }
            echo '<p class="content">' . $article->content . '</p>';
            echo '</article>';
        } else {
            echo 'L\'article avec l\'ID ' . $articleId . ' n\'existe pas.';
        }
    } else {
        echo 'ID de l\'article non valide.';
    }
    ?>
    <hr>
    <!-- Affichage des commentaires -->
    <?php
    $article_id = $_GET['id'];
    $queryCommentCount = $web->prepare('SELECT COUNT(*) AS comment_count FROM website_timeline_news_comments WHERE article_id = ?');
    $queryCommentCount->bindValue(1, $article_id, PDO::PARAM_INT); // Liez la valeur de $article_id au paramètre de la requête
    $queryCommentCount->execute();
    $commentCount = $queryCommentCount->fetch(PDO::FETCH_OBJ)->comment_count;
    ?>
    <h2><?php echo $translations["MOTS_228"];?> (<?php echo $commentCount; ?>)</h2>
    <?php
    // Afficher un message si les commentaires est désactivé
    if ($commentaireAllowed == 'non') {
        echo $translations["ALERTES_045"];
    } else {
        // Si le commentaire est activé, afficher le contenu
        ?>
    <?php
        // Vérifiez si l'utilisateur est connecté
        if (isset($_SESSION['user'])) {
            // L'utilisateur est connecté, vérifiez également la valeur de la colonne "commentaire" pour l'article en cours
            if ($article->commentaire === 'oui') {
                // Affichez le formulaire de commentaire seulement si l'utilisateur est connecté et "commentaire" est égal à "oui"
                echo '<div class="section section-default padding-25">
        <div class="row">
            <form action="?page=article&id=' . $articleId . '" method="post" onsubmit="return validateForm()">
                <div class="form-group">
                    <textarea class="form-control" name="content" id="editor" rows="4" width="300"></textarea>
                </div>
                <input type="hidden" name="article_id" value="' . $articleId . '">
                <button type="submit" class="btn btn-primary">' . $translations["MOTS_082"] . '</button>
            </form>
            
            <script>
                function validateForm() {
                    var editorData = CKEDITOR.instances.editor.getData();
                    if (editorData.trim() === "") {
                        alert("Le contenu du commentaire ne peut pas être vide.");
                        return false; 
                    }
                    return true; // Soumet le formulaire
                };
            </script>
        </div>
    </div>';
            } else {
                // Si "commentaire" est égal à "non", n'affichez pas le formulaire
                echo $translations["ALERTES_046"];
            }
        } else {
            echo $translations["INFOS_007"];
        }

?>
    <?php } ?>
    <div class="comments">
        <?php
        // Récupérer les commentaires associés à cet article depuis la base de données
        $queryComments = $web->prepare('SELECT pseudo, comment_text, comment_date FROM website_timeline_news_comments WHERE article_id = ? ORDER BY comment_date DESC');
        $queryComments->bindParam(1, $articleId, PDO::PARAM_INT);
        $queryComments->execute();

        // Convertissez la date au format français
        $dateVisibilite = new DateTime($article->comment_date);
        setlocale(LC_TIME, 'fr_FR.UTF-8');
        $dateFormatee = strftime('%d %B %Y, %H:%M', $dateVisibilite->getTimestamp());

        $pseudosWithCustomStyles = [
            "[Majordom]" => "background: url('../img/assets/bg-staff.jpg') center / cover no-repeat; color: #ffffff; border-color: #f2d214;",
        ];

        while ($comment = $queryComments->fetch(PDO::FETCH_OBJ)) {
            $dateVisibilite = new DateTime($comment->comment_date);
            setlocale(LC_TIME, 'fr_FR.UTF-8');
            $dateFormatee = strftime('%d %B %Y à %H:%M', $dateVisibilite->getTimestamp());

            // Récupérer l'avatar de l'auteur du commentaire depuis la base de données
            $queryAvatar = $login->prepare('SELECT avatar FROM world_accounts WHERE pseudo = ?');
            $queryAvatar->bindParam(1, $comment->pseudo, PDO::PARAM_STR);
            $queryAvatar->execute();
            $avatar = $queryAvatar->fetchColumn();

            // Vérifiez si le commentaire a un style CSS personnalisé en fonction de son pseudonyme
            $customStyle = isset($pseudosWithCustomStyles[$comment->pseudo]) ? $pseudosWithCustomStyles[$comment->pseudo] : '';

            // Définir un style par défaut si aucun style personnalisé n'est trouvé
            $defaultStyle = 'border: 1px solid #ccc; padding: 20px; margin-bottom: 20px; background-color: #f0f0f0; border-radius: 5px;';

            echo '<div class="comment-box" style="' . ($customStyle ? $customStyle : $defaultStyle) . '">';
            // Afficher l'avatar ici
            if ($avatar) {
                echo '<div class="avatar-container">';
                echo '<img src="img/avatar/' . $avatar . '" alt="Avatar" width="50" height="50">';
                echo '</div>';
            }
            echo '<div class="pseudo-container">';
            echo '<p><strong>' . $comment->pseudo . '</strong> - ' . $dateFormatee . '</p>';
            echo '</div>';
            echo '<div class="comment-container">';
            echo '<p>' . $comment->comment_text . '</p>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
    <br/>
</div>
<!-- ./leftside -->