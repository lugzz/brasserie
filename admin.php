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

// Fonction pour générer le login
function generateLogin($prenom, $nom) {
    $prenom_initial = strtolower(substr($prenom, 0, 1)); // Première lettre du prénom
    $nom_substring = strtolower(substr($nom, 0, 9));   // 9 premières lettres du nom
    return $prenom_initial . $nom_substring . '25'; // Format du login
}

// Mise à jour (modification) d'un utilisateur si 'Id' est envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Id'])) {
    // Générer le nouveau login basé sur les nouveaux nom et prénom
    $login = generateLogin($_POST['Prenom'], $_POST['Nom']);

    // Vérifier si le login existe déjà dans la base de données
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM user WHERE login = :login AND id != :id'); // Vérifier pour un autre utilisateur
    $stmt->execute(['login' => $login, 'id' => $_POST['Id']]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        die("Erreur : Ce login existe déjà. Veuillez en choisir un autre.");
    }

    // Mise à jour de l'utilisateur avec le nouveau login
    $stmt = $pdo->prepare('UPDATE user SET nom = :nom, prenom = :prenom, email = :email, role = :role, etat_compte = :etat_compte, login = :login WHERE id = :id');
    $stmt->execute([
        'id' => $_POST['Id'],
        'nom' => $_POST['Nom'],
        'prenom' => $_POST['Prenom'],
        'email' => $_POST['Email'],
        'role' => $_POST['Role'],
        'etat_compte' => $_POST['EtatCompte'],
        'login' => $login,  // Nouveau login
    ]);
    echo "Utilisateur modifié avec succès.";
}

// Ajout d'un nouvel utilisateur si aucune 'Id' n'est envoyée
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['Id']) && isset($_POST['Nom'], $_POST['Prenom'], $_POST['Email'], $_POST['Role'], $_POST['EtatCompte'])) {
    // Générer le login
    $login = generateLogin($_POST['Prenom'], $_POST['Nom']);

    // Vérifier si le login existe déjà dans la base de données
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM user WHERE login = :login');
    $stmt->execute(['login' => $login]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        die("Erreur : Ce login existe déjà. Veuillez en choisir un autre.");
    }

    // Hash du mot de passe par défaut
    $password_hashed = password_hash("motdepasse", PASSWORD_DEFAULT);

    // Insertion de l'utilisateur dans la base
    $stmt = $pdo->prepare('INSERT INTO user (nom, prenom, email, role, etat_compte, login, password, premiere_co) 
                       VALUES (:nom, :prenom, :email, :role, :etat_compte, :login, :password, :premiere_co)');
$stmt->execute([
    'nom' => $_POST['Nom'],
    'prenom' => $_POST['Prenom'],
    'email' => $_POST['Email'],
    'role' => $_POST['Role'],
    'etat_compte' => $_POST['EtatCompte'],
    'login' => $login,
    'password' => $password_hashed,
    'premiere_co' => "0",
]);

    echo "Utilisateur ajouté avec succès.";
}

// Suppression d'un utilisateur si 'deleteId' est envoyé
if (isset($_GET['deleteId'])) {
    $stmt = $pdo->prepare('DELETE FROM user WHERE id = :id');
    $stmt->execute(['id' => $_GET['deleteId']]);
    echo "Utilisateur supprimé avec succès.";
}

// Récupération des utilisateurs
$users = $pdo->query('SELECT * FROM user')->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les produits pour l'interface principale
try {
    $sql = "SELECT * FROM produits";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des produits : " . $e->getMessage());
}

// Modifier un utilisateur si 'editId' est envoyé
$editUser = null;
if (isset($_GET['editId'])) {
    $stmt = $pdo->prepare('SELECT * FROM user WHERE id = :id');
    $stmt->execute(['id' => $_GET['editId']]);
    $editUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Liste des Utilisateurs</h1>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>État du Compte</th>
                <th>Login</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td><?= htmlspecialchars($user['etat_compte']) ?></td>
                    <td><?= htmlspecialchars($user['login']) ?></td>
                    <td>
                        <a href="?editId=<?= $user['id'] ?>">Modifier</a> |
                        <a href="?deleteId=<?= $user['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p><a href="?action=add" class="button">Ajouter Utilisateur</a></p>

    <?php if ($editUser): ?>
        <h2>Modifier Utilisateur</h2>
        <form action="" method="post">
            <input type="hidden" name="Id" value="<?= htmlspecialchars($editUser['id']) ?>">
            <label for="Nom">Nom:</label>
            <input type="text" name="Nom" value="<?= htmlspecialchars($editUser['nom']) ?>"><br>
            <label for="Prenom">Prénom:</label>
            <input type="text" name="Prenom" value="<?= htmlspecialchars($editUser['prenom']) ?>"><br>
            <label for="Email">Email:</label>
            <input type="email" name="Email" value="<?= htmlspecialchars($editUser['email']) ?>"><br>
            <label for="Role">Rôle:</label>
            <input type="text" name="Role" value="<?= htmlspecialchars($editUser['role']) ?>"><br>
            <label for="EtatCompte">État du Compte:</label>
            <input type="text" name="EtatCompte" value="<?= htmlspecialchars($editUser['etat_compte']) ?>"><br>
            <button type="submit">Modifier</button>
        </form>
    <?php endif; ?>

    <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
        <h2>Ajouter Utilisateur</h2>
        <form action="" method="post">
            <label for="Nom">Nom:</label>
            <input type="text" name="Nom" required><br>
            <label for="Prenom">Prénom:</label>
            <input type="text" name="Prenom" required><br>
            <label for="Email">Email:</label>
            <input type="email" name="Email" required><br>
            <label for="Role">Rôle:</label>
            <input type="text" name="Role" required><br>
            <label for="EtatCompte">État du Compte:</label>
            <input type="text" name="EtatCompte" required><br>
            <button type="submit">Ajouter</button>
        </form>
    <?php endif; ?>

    <a href="index.php" class="button">Retour à l'interface</a>
</body>
</html>
