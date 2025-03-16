<?php
$host = 'sql109.infinityfree.com';
$dbname = 'if0_38342359_brasserie';
$username = 'if0_38342359';
$password = 'gE0DeROeqK'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les produits
$sql_produits = "SELECT * FROM produits";
$stmt_produits = $pdo->prepare($sql_produits);
$stmt_produits->execute();
$produits = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);

// Récupérer le stock
$stocks = $pdo->query('SELECT * FROM stock')->fetchAll(PDO::FETCH_ASSOC);

// Mise à jour du stock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('UPDATE stock SET malt = :malt, houblon = :houblon, levure = :levure, sucre = :sucre');
    $stmt->execute([
        'malt' => $_POST['malt'],
        'houblon' => $_POST['houblon'],
        'levure' => $_POST['levure'],
        'sucre' => $_POST['sucre'],
    ]);
    echo "Stock mis à jour avec succès.";
}

// Suppression d'un produit
if (isset($_GET['deleteProductId'])) {
    $deleteId = $_GET['deleteProductId'];
    $stmt = $pdo->prepare('DELETE FROM produits WHERE id_produit = :id');
    $stmt->execute(['id' => $deleteId]);
    echo "Produit supprimé avec succès.";
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $volume_biere = floatval($_POST['volume_biere']);
    $degre_alcool = floatval($_POST['degre_alcool']);

    $ebc_grains = array_map('floatval', explode(',', $_POST['ebc_grains']));
    

    $qtt_mal = ($volume_biere * $degre_alcool) / 20;


    $eau_brassage = $qtt_mal * 2.8;

  
    $eau_rincage = ($volume_biere * 1.25) - ($eau_brassage * 0.7);


    $poids_grains = array_fill(0, count($ebc_grains), $qtt_mal / count($ebc_grains));


    $somme_ebc_poids = 0;
    for ($i = 0; $i < count($ebc_grains); $i++) {
        $somme_ebc_poids += $ebc_grains[$i] * $poids_grains[$i];
    }
    $MCU = (4.23 * $somme_ebc_poids) / $volume_biere;


    $EBC = 2.9396 * pow($MCU, 0.6859);
    $SRM = 0.508 * $EBC;
    $amerisant = $volume_biere * 3;
    $houblon = $volume_biere * 1;
    $levure = $volume_biere / 2;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Stock et des Produits</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Gestion du Stock et des Produits</h1>

    <!-- Affichage des produits -->
    <h2>Liste des Produits</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nom du produit</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><?= htmlspecialchars($produit['nom_produit']) ?></td>
                    <td><?= htmlspecialchars($produit['description_produit']) ?></td>
                    <td><?= htmlspecialchars($produit['prix']) ?> €</td>
                    <td><?= htmlspecialchars($produit['quantite']) ?></td>
                    <td>
                        <a href="?editProductId=<?= $produit['id_produit'] ?>">Modifier</a> |
                        <a href="?deleteProductId=<?= $produit['id_produit'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Affichage du stock -->
    
    <!-- Formulaire de calcul du brassage -->
    <h2>Formulaire de Calcul du Brassage</h2>
    <form method="post">
        Volume de bière (L) : <input type="number" step="0.01" name="volume_biere" required><br>
        Degré d’alcool (%) : <input type="number" step="0.1" name="degre_alcool" required><br>
        EBC des grains (séparés par une virgule) : <input type="text" name="ebc_grains" required><br>
        <button type="submit">Calculer</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST') : ?>
        <h3>Résultats :</h3>
        <ul>
        <li>Quantité de malt : <?= $qtt_mal?> kg</li>
            <li>Eau de brassage : <?= $eau_brassage ?> L</li>
            <li>Eau de rinçage : <?= $eau_rincage?> L</li>
            <li>MCU : <?= $MCU ?></li>
            <li>EBC : <?= $EBC ?></li>
            <li>SRM : <?= $SRM ?></li>
            <li>Houblon amérisant : <?= $amerisant ?> g</li>
            <li>Levure utilisée : <?= $levure ?> g</li>
        </ul>
    <?php endif; ?>
    <h2>Gestion du Stock</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Malt</th>
                <th>Houblon</th>
                <th>Levure</th>
                <th>Sucre</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stocks as $stock): ?>
                <tr>
                    <td><?= htmlspecialchars($stock['malt']) ?></td>
                    <td><?= htmlspecialchars($stock['houblon']) ?></td>
                    <td><?= htmlspecialchars($stock['levure']) ?></td>
                    <td><?= htmlspecialchars($stock['sucre']) ?></td>
                    <td>
                        <a href="?editId=<?= $stock['id'] ?>">Modifier</a> |
                        <a href="?deleteId=<?= $stock['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>

</html>
