<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    echo "<script>window.location.replace(\"?page=signin\")</script>";
    return;
}

// Vérifier si le panier est vide
if (empty($_SESSION['cart'])) {
    echo $translations["MOTS_127"];
    echo '<script>
    setTimeout(function() {
        window.location.href = "?page=shop";
    }, 2000); // 3000 millisecondes = 3 secondes
</script>';
    return;
}

// Récupérer le nombre de points de l'utilisateur
$queryPoints = $login->prepare("SELECT points FROM world_accounts WHERE guid = :user_id;");
$queryPoints->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
$queryPoints->execute();
$userPoints = $queryPoints->fetch(PDO::FETCH_COLUMN);
$queryPoints->closeCursor();

$totalPrice = 0; // Initialise le total des prix

// Parcourir les articles du panier
foreach ($_SESSION['cart'] as $item) {
    $priceQuery = $web->prepare("SELECT price, reduc FROM website_shop_objects WHERE name = :item_name;");
    $priceQuery->bindParam(':item_name', $item['name'], PDO::PARAM_STR);
    $priceQuery->execute();
    $result = $priceQuery->fetch(PDO::FETCH_ASSOC);
    $price = $result['price'];
    $reduction = $result['reduc'];
    $priceQuery->closeCursor();

    // Appliquer la réduction au prix
    $priceWithReduction = $price - ($price * ($reduction / 100));

    $subtotal = $priceWithReduction; // Sous-total par article
    $totalPrice += $subtotal; // Ajoute le sous-total au total global
}

// Vérifier si l'utilisateur a suffisamment de points
$canPlaceOrder = ($userPoints >= $totalPrice);

?>
<div class="leftside">
    <ol class="breadcrumb">
        <li><a href="?page=index"><?php echo $translations["MOTS_006"]; ?></a></li>
        <li class="active"><?php echo $translations["MOTS_006"]; ?></li>
    </ol>

    <?php
    // Initialisation des variables
    $totalPrice = 0;
    $itemQuantities = array();

    foreach ($_SESSION['cart'] as $item) {
        $priceQuery = $web->prepare("SELECT price FROM website_shop_objects WHERE name = :item_name;");
        $priceQuery->bindParam(':item_name', $item['name'], PDO::PARAM_STR);
        $priceQuery->execute();
        $price = $priceQuery->fetch(PDO::FETCH_COLUMN);
        // Appliquer la réduction au prix
        $priceWithReduction = $price - ($price * ($reduction / 100));

        $subtotal = $priceWithReduction; // Sous-total par article
        $totalPrice += $subtotal; // Ajoute le sous-total au total global

        // Comptage des quantités par nom d'objet
        if (array_key_exists($item['name'], $itemQuantities)) {
            $itemQuantities[$item['name']]++;
        } else {
            $itemQuantities[$item['name']] = 1;
        }
    }
    ?>
    <h2><?php echo $translations["MOTS_129"]; ?></h2>

    <table class="table">
        <thead>
        <tr>
            <th><?php echo $translations["MOTS_130"]; ?></th>
            <th><?php echo $translations["MOTS_131"]; ?></th>
            <th><?php echo $translations["MOTS_132"]; ?></th>
            <th><?php echo $translations["MOTS_133"]; ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($itemQuantities as $itemName => $itemQuantity) : ?>
            <tr>
                <td>
                    <img src="img/shop/blue.png"/> <?php echo ($itemQuantity > 1) ? "Objet {$itemName} (x{$itemQuantity})" : $itemName; ?>
                </td>
                <td>
                    <?php
                    $priceQuery = $web->prepare("SELECT price, reduc FROM website_shop_objects WHERE name = :item_name;");
                    $priceQuery->bindParam(':item_name', $itemName, PDO::PARAM_STR);
                    $priceQuery->execute();
                    $result = $priceQuery->fetch(PDO::FETCH_ASSOC);
                    $price = $result['price'];
                    $reduction = $result['reduc'];
                    $priceQuery->closeCursor();

                    echo $price * $itemQuantity . ' ' . NOM_POINT;

                    ?>
                </td>
                <td>
                    <?php
                    if ($reduction != 0) {
                        echo $reduction . '%';
                    }
                    ?>
                </td>
                <td>
                    <?php
                    $priceWithReduction = $price - ($price * ($reduction / 100));
                    $subtotal = $priceWithReduction * $itemQuantity; // Sous-total par article
                    echo $subtotal . ' ' . NOM_POINT;
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="3"><b><?php echo $translations["MOTS_023"]; ?></b></td>
            <td><?php echo $totalPrice . ' ' . NOM_POINT; ?></td>
        </tr>
        </tfoot>
    </table>
    <?php if ($canPlaceOrder) : ?>
        <a href="?page=shop&clear_cart=true"><i class="fa fa-trash-o"></i> <?php echo $translations["MOTS_025"]; ?></a>
        <div style="text-align: right;">
            <form method="post">
                <button type="submit" name="confirm_order" class="btn btn-info"><?php echo $translations["MOTS_134"]; ?></button>
            </form>
        </div>
    <?php else : ?>
       <?php echo $translations["ALERTES_016"]; ?>
    <?php endif; ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//si c'est confirmer on fait le reste
if (isset($_POST['confirm_order'])) {
    // Récupérer le template et le serveur depuis les éléments du panier
    $template = '';
    $server = '';

    foreach ($_SESSION['cart'] as $item) {
        if ($item['name'] === 'template') {
            $template = $item['id'];
        }
        if ($item['name'] === 'server') {
            $server = $item['server'];
        }
    }

//envoie au joueur ses objets

// Récupérer l'ID du joueur
    $playerId = $_SESSION['id'];

// Récupérer la valeur de "jp" depuis la base de données
    $jpQuery = $web->prepare("SELECT jp FROM website_shop_objects WHERE name = :template;");
    $jpQuery->bindParam(':template', $template, PDO::PARAM_INT);
    $jpQuery->execute();
    $jp = $jpQuery->fetch(PDO::FETCH_COLUMN);
    $jpQuery->closeCursor();

// Construire les informations pour la mise à jour
    $objectsArray = array();
    foreach ($_SESSION['cart'] as $item) {
        $objectsArray[] = $item['template'] . ',1,' . $jp;
    }
    $objects = implode(';', $objectsArray);

// Vérifier si l'ID du joueur existe déjà dans la table des cadeaux
    $giftsQuery = $jiva->prepare("SELECT * FROM gifts WHERE id = :id");
    $giftsQuery->bindParam(':id', $playerId);
    $giftsQuery->execute();
    $giftsRow = $giftsQuery->fetch(PDO::FETCH_OBJ);
    $giftsQuery->closeCursor();

    if ($giftsRow) {
        // L'ID du joueur existe déjà dans la table, effectuer une mise à jour
        $existingObjects = $giftsRow->objects;

        // Mettre à jour les objets existants avec les nouveaux objets
        $updatedObjects = $existingObjects . ';' . $objects;

        $updateQuery = $jiva->prepare("UPDATE gifts SET objects = :objects WHERE id = :id");
        $updateQuery->bindParam(':objects', $updatedObjects);
        $updateQuery->bindParam(':id', $playerId);
        $updateQuery->execute();
        $updateQuery->closeCursor();
    } else {
        // L'ID du joueur n'existe pas encore dans la table, effectuer une insertion
        $insertQuery = $jiva->prepare("INSERT INTO gifts (id, objects) VALUES (:id, :objects)");
        $insertQuery->bindParam(':id', $playerId);
        $insertQuery->bindParam(':objects', $objects);
        $insertQuery->execute();
        $insertQuery->closeCursor();
    }

    // Mettre à jour les points de l'utilisateur dans la base de données
    $newPoints = $userPoints - $totalPrice;
    $updatePointsQuery = $web->prepare("UPDATE world_accounts SET points = :new_points WHERE guid = :user_id;");
    $updatePointsQuery->bindParam(':new_points', $newPoints, PDO::PARAM_INT);
    $updatePointsQuery->bindParam(':user_id', $_SESSION['id'], PDO::PARAM_INT);
    $updatePointsQuery->execute();
    $updatePointsQuery->closeCursor();

    // Ajout de l'achat dans la base
    foreach ($_SESSION['cart'] as $item) {
        $template = $item['template'];
        $server = $item['server']; // Assurez-vous que $server est défini ici

        $query = $web->prepare("INSERT INTO `website_shop_objects_purchases` (accountID, template, server, date) VALUES (:accountID, :template, :server, :date)");
        $query->bindParam(':accountID', $_SESSION['id']);
        $query->bindParam(':template', $template);
        $query->bindParam(':server', $server);
        $query->bindValue(':date', date('d/m/y H:i'));
        $query->execute();
        $query->closeCursor();
    }

    // Vider le panier après la confirmation de la commande
    $_SESSION['cart'] = array();

    echo '<br/>'. $translations["SUCCESS_006"] .' ';
    echo '<script>setTimeout(function() {window.location.href = "?page=shop";}, 3000); // 3000 millisecondes = 3 secondes</script>';
}
?>
</div>