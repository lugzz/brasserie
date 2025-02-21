<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .button {
            display: inline-block;
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur Terroirs et Saveurs</h1>
        <p>Choisissez votre destination :</p>
        <a href="?destination=site" class="button">Site Principal</a>
        <a href="?destination=blog" class="button">Blog</a>
    </div>

    <?php
    // Redirection vers le fichier index.php dans le sous-répertoire "site"
    $site_url = 'site/index.php';

    // Redirection vers le blog
    $blog_url = 'https://www.terroirsetsaveurs.com/blog';

    // Condition de redirection
    if (isset($_GET['destination'])) {
        if ($_GET['destination'] == 'blog') {
            header("Location: $blog_url");
        } else {
            header("Location: $site_url");
        }
        exit();
    }
    ?>
</body>
</html>