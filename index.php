<?php
$inscription_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'], $_POST['prenom'], $_POST['nom'], $_POST['role'])) {

        // Sécurisation des entrées utilisateur
        $prenom = htmlspecialchars($_POST['prenom']);
        $nom = htmlspecialchars($_POST['nom']);
        $email = htmlspecialchars($_POST['email']);
        $role = htmlspecialchars($_POST['role']);

        // Génération du login (première lettre du prénom + nom, limité à 13 caractères puis suffixé par "25")
        $login = strtolower(substr($prenom, 0, 1)) . strtolower($nom);
        if (strlen($login) > 13) {
            $login = substr($login, 0, 13);
        }
        $login .= "25";

        // Paramètres de connexion à la base de données
        $host     = 'sql109.infinityfree.com';
        $dbname   = 'if0_38342359_brasserie';
        $username = 'if0_38342359';
        $password = 'gE0DeROeqK';

        try {
            // Connexion PDO avec gestion des erreurs
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Requête d'insertion dans la table "user"
            $result = $pdo->prepare('INSERT INTO user (nom, prenom, email, role, login) VALUES (:nom, :prenom, :email, :role, :login)');
            $result->execute([
                'nom'     => $nom,
                'prenom'  => $prenom,
                'email'   => $email,
                'role'    => $role,
                'login'   => $login
            ]);

            $inscription_message = "<p class='w3-text-green'>Vous avez bien été inscrit !</p>";
        } catch (PDOException $e) {
            $inscription_message = "<p class='w3-text-red'>Une erreur s'est produite : " . $e->getMessage() . "</p>";
        }
    } else {
        $inscription_message = "<p class='w3-text-red'>Veuillez remplir tous les champs.</p>";
    }
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
        .w3-xxxlarge {
            color: red;
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
    </style>
</head>
<body>

    <!-- Sidebar/menu -->
    <nav class="w3-sidebar w3-collapse w3-top w3-large w3-padding" style="z-index:3;width:300px;font-weight:bold;" id="mySidebar">
        <br>
        <div class="w3-container">
            <h3 class="w3-padding-64"><b>Terroirs<br>et Saveurs</b></h3>
        </div>
        <div class="w3-bar-block">
            <a href="#accueil" class="w3-bar-item w3-button">Accueil</a>
            <a href="#produits" class="w3-bar-item w3-button">Nos Produits</a>
            <a href="#nous-sommes" class="w3-bar-item w3-button">Qui Nous Sommes</a>
            <a href="#inscription" class="w3-bar-item w3-button">Inscription</a>
        </div>
    </nav>

    <!-- Overlay pour petits écrans -->
    <div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" id="myOverlay"></div>

    <!-- Contenu principal -->
    <div class="w3-main" style="margin-left:340px;margin-right:40px">

        <!-- Accueil -->
        <div class="w3-container" style="margin-top:80px" id="accueil">
            <h1 class="w3-xxxlarge w3-text-red"><b>Bienvenue chez Terroirs et Saveurs</b></h1>
            <hr style="width:50px;border:5px solid red" class="w3-round">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec vel nunc non mauris aliquet vulputate.</p>
        </div>

        <!-- Nos Produits -->
        <div class="w3-container" id="produits" style="margin-top:75px">
            <h1 class="w3-xxxlarge w3-text-red"><b>Nos Produits</b></h1>
            <hr style="width:50px;border:5px solid red" class="w3-round">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.</p>
        </div>

        <!-- Qui Nous Sommes -->
        <div class="w3-container" id="nous-sommes" style="margin-top:75px">
            <h1 class="w3-xxxlarge w3-text-red"><b>Qui Nous Sommes</b></h1>
            <hr style="width:50px;border:5px solid red" class="w3-round">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed nisi. Nulla quis sem at nibh elementum imperdiet.</p>
        </div>

        <!-- Inscription -->
        <div class="w3-container" id="inscription" style="margin-top:75px">
            <h1 class="w3-xxxlarge w3-text-red"><b>Inscription</b></h1>
            <hr style="width:50px;border:5px solid red" class="w3-round">

            <!-- Message d'inscription -->
            <?php echo $inscription_message; ?>

            <form action="" method="post" id="form">
                <div class="w3-section">
                    <label>Nom</label>
                    <input class="w3-input w3-border" type="text" name="nom" required>
                </div>
                <div class="w3-section">
                    <label>Prénom</label>
                    <input class="w3-input w3-border" type="text" name="prenom" required>
                </div>
                <div class="w3-section">
                    <label>Email</label>
                    <input class="w3-input w3-border" type="email" name="email" required>
                </div>
                <div class="w3-section">
                    <label>Rôle :</label><br>
                    <label for="client">Client</label>
                    <input type="radio" name="role" value="Client" id="client" required>
                </div>
                <input type="submit" class="w3-button w3-block w3-padding-large w3-red" value="S'inscrire">
            </form>
        </div>

    </div>

    <script>
        // Fonctions pour ouvrir/fermer la sidebar
        function w3_open() {
            document.getElementById("mySidebar").style.display = "block";
            document.getElementById("myOverlay").style.display = "block";
        }
        function w3_close() {
            document.getElementById("mySidebar").style.display = "none";
            document.getElementById("myOverlay").style.display = "none";
        }
    </script>

</body>
</html>
