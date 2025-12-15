<?php
include "./functions.php";
require './connexion-bd.php';

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = "SELECT * FROM participants";

$stm = $pdo->query($sql);

$conferences = $stm->fetchAll();
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
        <h2>Liste des conférences</h2>

        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">id</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Email</th>
                    <th scope="col">Type de participant</th>
                    <th scope="col">Centres d'interêt</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach($conferences as $indiv) {
                    ?>
                <tr class="table-light">
                    <th scope="row"><?= $indiv['id'] ?></th>
                    <td><?= $indiv['nom'].' '.$indiv['prenom'] ?></td>
                    <td><?= $indiv['email'] ?></td>
                    <td><?= $indiv['type_participant'] ?></td>
                    <td><?= $indiv['centres_interet'] ?></td>
                    <td>Kill</td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>