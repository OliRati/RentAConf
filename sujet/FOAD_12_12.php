<?php
/*
FORMULAIRE D’INSCRIPTION À UNE CONFÉRENCE AVEC PDO

PARTIE BASE DE DONNÉES AVEC PDO ET INSERTION



Objectif :
Une fois que les données sont validées sans erreur,
 les enregistrer dans une base MySQL avec PDO et une requête préparée.

Étape 1 : création de la base et de la table (dans MySQL, hors PHP)

Dans phpMyAdmin, exécuter ces commandes :

CREATE DATABASE conference;

USE conference;

CREATE TABLE participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    date_naissance DATE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    pays VARCHAR(50) NOT NULL,
    type_participant VARCHAR(20) NOT NULL,
    centres_interet TEXT NOT NULL,
    conditions_valide TINYINT(1) NOT NULL DEFAULT 0,
    date_inscription DATETIME NOT NULL
);

Remarque importante :
le mot de passe ne doit jamais être stocké en clair
on stocke uniquement un hash généré avec password_hash()

Étape 2 : configuration PDO dans index.php

En haut de index.php, définir les variables de connexion :

$host    = 'localhost';
$db      = 'conference';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

Construire le DSN :

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

Préparer aussi un tableau d’options :

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

Étape 3 : comprendre le principe de PDO

PDO est une classe qui permet de se connecter à plusieurs types de bases de données avec une seule interface.
https://www.php.net/manual/fr/book.pdo.php

créer une connexion à MySQL avec new PDO($dsn, $user, $pass, $options)
exécuter des requêtes SQL via des requêtes préparées
liaison de paramètres nommés pour éviter les injections SQL
exécution sécurisée avec execute()

Étape 4 : connexion à la base au moment de l’insertion

Dans la partie du code où il n’y a plus d’erreurs de validation :

utiliser un bloc try catch

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    // suite du code d’insertion
} catch (PDOException $e) {
    // en cas d’erreur de connexion ou autre
    ajouter un message dans $errors
    par exemple : $errors[] = "Erreur de connexion à la base de données.";
    ne pas rediriger, réafficher le formulaire
}

Étape 5 : préparer les données pour l’insertion

centres_interet est reçu comme un tableau depuis le formulaire
on peut le transformer en chaîne avec implode(',', $centres_interet)

conditions_valide :
si la case conditions est cochée, stocker 1
sinon stocker 0 (mais normalement on ne passe pas ici si elle n’est pas cochée)

password :
générer le hash avec password_hash($password, PASSWORD_DEFAULT)
stockage dans la colonne password_hash

date_inscription :
utiliser date('Y-m-d H:i:s') pour la date et l’heure actuelles

Étape 6 : écriture de la requête d’insertion

Préparer une requête SQL avec uniquement des paramètres nommés :

INSERT INTO participants (
    nom,
    prenom,
    email,
    password_hash,
    date_naissance,
    telephone,
    pays,
    type_participant,
    centres_interet,
    conditions_valide,
    date_inscription
) VALUES (
    :nom,
    :prenom,
    :email,
    :password_hash,
    :date_naissance,
    :telephone,
    :pays,
    :type_participant,
    :centres_interet,
    :conditions_valide,
    :date_inscription
);

Dans le code PHP :

$sql = "INSERT INTO participants (...) VALUES (...)";
$stmt = $pdo->prepare($sql);

puis lier les paramètres

$stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
stmt pour prenom, email, etc.
pour conditions_valide, utiliser PDO::PARAM_INT

ou bien passer toutes les valeurs dans un tableau directement à execute :

$stmt->execute([
    ':nom'              => $nom,
    ':prenom'           => $prenom,
    ':email'            => $email,
    ':password_hash'    => $password_hash,
    ':date_naissance'   => $date_naissance,
    ':telephone'        => $telephone,
    ':pays'             => $pays,
    ':type_participant' => $type_participant,
    ':centres_interet'  => $centres_interet_string,
    ':conditions_valide'=> $conditions_valide,
    ':date_inscription' => $date_inscription,
]);

Si execute() fonctionne sans lever d’exception, l’enregistrement est fait.
Ensuite, faire la redirection vers success.php avec header() puis exit.

Étape 7 : ordre logique complet
                        FOAD_09_12 

                        1) afficher le formulaire la première fois
                        2) si la requête est en POST, récupérer les valeurs
                        3) nettoyer les valeurs (trim, strip_tags)
                        4) valider chaque champ
                        5) si erreurs présentes, afficher les erreurs, réafficher le formulaire
                        
                        FOAD_12_12
                        6) si aucune erreur, préparer les données pour la base
                        7) se connecter à MySQL avec PDO dans un try catch
                        8) préparer la requête INSERT avec des paramètres nommés
                        9) exécuter la requête
                        10) si tout est bon, rediriger vers success.php avec des paramètres GET


*/
