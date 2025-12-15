<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription Conference</title>
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
</head>

<body>
    <?php include "./nav.php" ?>

    <main>
        <div class="container">
            <?php
            $nom = "";
            $status = "";

            if (isset($_GET['status'])) {
                $status = trim(strip_tags($_GET['status']));
            }

            if (isset($_GET['nom'])) {
                $nom = trim(strip_tags($_GET['nom']));
            }

            if (($status === 'ok') && !empty($nom)) {
                ?>
                <p>Merci pour votre inscription, <?= $nom ?></p>
                <?php
            } else {
                ?>
                <p>Accès non valide à la page de confirmation.</p>
                <a href="./index.php">Retourner a l'acceuil</a>
                <?php
            }
            ?>
        </div>
    </main>

    <?php include "./footer.php" ?>
</body>

</html>