<?php
    if (isset($_POST['email']) && isset($_POST['prenom']) && isset($_POST['nom']) && isset($_POST['role'])) {

        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];

        $login = strtolower(substr($prenom, 0, 1)) . strtolower($nom);
        if (strlen($login) > 13) {
            $login = substr($login, 0, 13); 
        }
        $login = $login . "25"; 
        
       
        $pdo = new PDO('mysql:host=localhost;dbname=projet_brasserie', 'root', '');
        $result = $pdo->prepare('INSERT INTO user (nom, prenom, email, role, login) VALUES (:nom, :prenom, :email, :role, :login)');
        $result->execute(array(
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'login' => $login 
        ));

        echo "vous avez bien été inscris";
    } else {
        echo "Une erreur a ete rencontree";
    }
?>
