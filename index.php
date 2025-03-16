<?php
// Démarrer la session pour gérer l'authentification
session_start();

// Informations de connexion à la base de données
$host = 'sql109.infinityfree.com';
$dbname = 'if0_38342359_brasserie';
$username = 'if0_38342359';  // Remplace par ton vrai username
$password = 'gE0DeROeqK';  // Remplace par ton vrai mot de passe

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Active les erreurs PDO
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// Requête pour récupérer tous les produits
try {
    $sql = "SELECT * FROM produits";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des produits : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Terroirs et Saveurs</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Inclusion des styles -->
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins">
    <style>
        body, h1, h2, h3, h4, h5 {
            font-family: "Poppins", sans-serif;
        }
        body {
            font-size: 16px;
            margin: 0;
            padding: 0;
            background-image: url('bar.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            color: white;
        }
        /* Changement de la couleur des titres en H1, H2, H3 via la classe w3-xxxlarge */
        .w3-xxxlarge {
            color: #dfaf2c;
        }
        .w3-sidebar {
            background-color: rgb(93, 89, 89);
        }
        .w3-sidebar a {
            color: white;
        }
        .w3-sidebar a:hover {
            background-color: rgb(208, 196, 192);
        }
        /* Style pour les cartes */
        .product-card {
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            margin: 10px;
            background-color: #fff;
            text-align: center;
            transition: transform 0.3s ease-in-out;
            color: black;
        }
        .product-card img {
            width: 100%;
            height: auto;
            border-radius: 5px;
        }
        .product-card:hover {
            transform: translateY(-10px);
        }
        .product-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        /* Style pour le logo */
        .logo {
            width: 100px; /* Ajustez la taille selon vos besoins */
            margin-bottom: 5px; /* Espacement entre le logo et le message */
        }
        /* Style pour le message de bienvenue dans la navbar */
        .welcome-msg {
            padding: 10px;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>

   <!-- Sidebar / Navbar -->
   <nav class="w3-sidebar w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold;" id="mySidebar">
        <div class="w3-container">
            <img src="brasserie_logo.png" alt="Logo Brasserie" class="logo">
            <?php if (isset($_SESSION['login'])): ?>
                <a href="#" class="w3-bar-item w3-button">Bonjour, <?php echo htmlspecialchars($_SESSION['login']); ?></a>
            <?php endif; ?>
            <h3 class="w3-padding-64"><b>Terroirs<br>et Saveurs</b></h3>
        </div>
        <div class="w3-bar-block">
            <a href="#accueil" class="w3-bar-item w3-button">Accueil</a>
            <a href="#produits" class="w3-bar-item w3-button">Nos Produits</a>
            <a href="#nous-sommes" class="w3-bar-item w3-button">Qui Nous Sommes</a>

            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] == '1'): ?>
                    <a href="admin.php" class="w3-bar-item w3-button">Administration</a>
                <?php elseif ($_SESSION['role'] == '2'): ?>
                    <a href="brasseur.php" class="w3-bar-item w3-button">Brasseur</a>
                <?php elseif ($_SESSION['role'] == '3'): ?>
                    <a href="direction.php" class="w3-bar-item w3-button">Direction</a>
                <?php elseif ($_SESSION['role'] == '4'): ?>
                    <a href="client.php" class="w3-bar-item w3-button">Client</a>
                <?php elseif ($_SESSION['role'] == '5'): ?>
                    <a href="caissier.php" class="w3-bar-item w3-button">Caissier</a>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="connexion.php" class="w3-bar-item w3-button">Connexion</a>
            <?php else: ?>
                <a href="logout.php" class="w3-bar-item w3-button">Déconnexion</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Overlay pour petits écrans -->
    <div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" id="myOverlay"></div>

    <!-- Contenu principal -->
    <div class="w3-main" style="margin-left:340px;margin-right:40px">
        <!-- Section Accueil -->
        <div class="w3-container" style="margin-top:80px" id="accueil">
            <h1 class="w3-xxxlarge"><b>Bienvenue chez Terroirs et Saveurs</b></h1>
            <hr style="width:50px;border:5px solid #dfaf2c" class="w3-round">
            <p>Bienvenue sur le site de la Brasserie Terroir & Saveurs ! Découvrez notre savoir-faire brassicole, nos produits locaux et l'histoire de notre brasserie. Nous vous offrons une expérience immersive autour de nos bières et spiritueux, tout en mettant en lumière les étapes de notre production artisanale. Profitez également de notre plateforme moderne pour en savoir plus sur nos produits et services.</p>
        </div>

        <!-- Section Nos Produits -->
        <div class="w3-container" id="produits" style="margin-top:75px">
            <h1 class="w3-xxxlarge"><b>Nos Produits</b></h1>
            <hr style="width:50px;border:5px solid #dfaf2c" class="w3-round">
            <p>Découvrez nos produits artisanaux :</p>

            <!-- Affichage des produits sous forme de cartes -->
            <div class="product-container">
                <?php if (!empty($produits)): ?>
                    <?php foreach ($produits as $produit): ?>
                        <div class="product-card">
                            <img src="<?php echo htmlspecialchars($produit['url_img']); ?>" alt="<?php echo htmlspecialchars($produit['nom_produit']); ?>">
                            <h3><?php echo htmlspecialchars($produit['nom_produit']); ?></h3>
                            <p><?php echo htmlspecialchars($produit['description_produit']); ?></p>
                            <p><strong><?php echo htmlspecialchars($produit['prix']); ?> €</strong></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucun produit trouvé.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section Qui Nous Sommes -->
        <div class="w3-container" id="nous-sommes" style="margin-top:75px">
            <h1 class="w3-xxxlarge"><b>Qui Nous Sommes</b></h1>
            <hr style="width:50px;border:5px solid #dfaf2c" class="w3-round">
            <p>Terroirs et Saveurs est une brasserie artisanale dédiée à la production de bières et spiritueux de qualité. Nous utilisons des ingrédients locaux pour offrir une expérience authentique et respectueuse de notre terroir.</p>
        </div>
    </div>
</body>
</html>
