<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$inscription_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['inscription'])) {
    if (isset($_POST['email'], $_POST['prenom'], $_POST['nom'], $_POST['role'], $_POST['password'])) {

        // Sécurisation des entrées utilisateur
        $prenom = htmlspecialchars($_POST['prenom']);
        $nom = htmlspecialchars($_POST['nom']);
        $email = htmlspecialchars($_POST['email']);
        $role = htmlspecialchars($_POST['role']); // Le rôle est caché et pré-défini
        $password_input = $_POST['password'];

        // Génération du login (première lettre du prénom + nom, limité à 13 caractères puis suffixé par "25")
        $login = strtolower(substr($prenom, 0, 1)) . strtolower($nom);
        if (strlen($login) > 13) {
            $login = substr($login, 0, 13);
        }
        $login .= "25";

        // Hachage du mot de passe
        $password_hashed = password_hash($password_input, PASSWORD_DEFAULT);

        // Paramètres de connexion à la base de données
        $host       = 'sql109.infinityfree.com';
        $dbname     = 'if0_38342359_brasserie';
        $username_db= 'if0_38342359';
        $password_db= 'gE0DeROeqK';

        try {
            // Connexion PDO avec gestion des erreurs
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username_db, $password_db);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Insertion dans la table "user"
            $stmt = $pdo->prepare('INSERT INTO user (nom, prenom, email, role, login, password) VALUES (:nom, :prenom, :email, :role, :login, :password)');
            $stmt->execute([
                'nom'      => $nom,
                'prenom'   => $prenom,
                'email'    => $email,
                'role'     => $role,
                'login'    => $login,
                'password' => $password_hashed
            ]);

            // Inscription réussie, redirection vers index
            header("Location: index.php");
            exit();
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
    <title>Inscription - Terroirs et Saveurs</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Styles identiques -->
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
        .btn {
            display: inline-block;
            padding: 15px 25px;
            background-color: #e74c3c;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px;
        }
        .btn:hover {
            background-color: #c0392b;
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
            <a href="index.php" class="w3-bar-item w3-button">Accueil</a>
            <a href="#inscription" class="w3-bar-item w3-button">Inscription</a>
            <a href="connexion.php" class="w3-bar-item w3-button">Connexion</a>
        </div>
    </nav>

    <!-- Overlay pour petits écrans -->
    <div class="w3-overlay w3-hide-large" onclick="w3_close()" style="cursor:pointer" id="myOverlay"></div>

    <!-- Contenu principal -->
    <div class="w3-main" style="margin-left:340px;margin-right:40px">
        <div class="w3-container" style="margin-top:80px" id="inscription">
            <h1 class="w3-xxxlarge w3-text-red"><b>Inscription</b></h1>
            <hr style="width:50px;border:5px solid red" class="w3-round">
            <?php echo $inscription_message; ?>
            <form action="" method="post">
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
                    <label>Mot de passe</label>
                    <input class="w3-input w3-border" type="password" name="password" required>
                </div>
                <!-- Champ role caché : le rôle est fixé à "Client" (valeur 1) -->
                <input type="hidden" name="role" value="1">
                <input type="submit" name="inscription" class="w3-button w3-block w3-padding-large w3-red" value="S'inscrire">
            </form>
            <p>
                Déjà inscrit ? <a href="connexion.php" class="btn">Se connecter</a>
            </p>
        </div>
    </div>
    <script>
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
