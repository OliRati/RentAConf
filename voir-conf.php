<?php
include "./functions.php";
require './connexion-bd.php';

$idVoir = $_GET['id'] ?? null;

if (!is_numeric($idVoir)) {
    dd("Cette conférence n'existe pas !!!");
}

$pdo = new PDO($dsn, $user, $pass, $options);
$stm = $pdo->prepare("SELECT * FROM participants WHERE id = :id");
$stm->bindParam(':id', $idVoir, PDO::PARAM_INT);

if (!$stm->execute()) {
    echo "Pas de données pour cette requête.";
    exit;
}

$indiv = $stm->fetch();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/bootstrap.min.css">
    <title>Document</title>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <h2>Details sur une conférence</h2>
        <div class="card border-info mb-3" style="max-width: 20rem;">
            <div class="card-header">id:<?= $indiv['id'] ?></div>
            <div class="card-body">
                <h4 class="card-title"><?= $indiv['nom'] . ' ' . $indiv['prenom'] ?></h4>
                <p class="card-text">
                    Date de naissance : <?= $indiv['date_naissance'] ?><br>
                    Pays : <?= $indiv['pays'] ?><br>
                    Email : <?= $indiv['email'] ?><br>
                    Type de participation : <?= $indiv['type_participant'] ?><br>
                    Centres d'interêt : <?= $indiv['centres_interet'] ?><br>
                    Tel : <?= $indiv['telephone'] ?><br>
                </p>
                <p>
                    Date d'inscription : <?= $indiv['date_inscription'] ?>
                </p>
            </div>
        </div>
    </div>
</body>

</html>