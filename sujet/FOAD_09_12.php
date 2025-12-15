<?php
/*
FORMULAIRE D’INSCRIPTION À UNE CONFÉRENCE

Structure du projet :
/projet/
index.php
header.php
footer.php
success.php

FORMULAIRE À CRÉER DANS index.php (méthode POST) :

Champs du formulaire :
nom (text)
prenom (text)
email (email)
password (password)
password_confirm (password)
date_naissance (date)
telephone (text)
pays (select, minimum 5 pays)
type_participant (radio : Étudiant, Professionnel, Speaker)
centres_interet (checkbox[] : PHP, JavaScript, DevOps, IA)
conditions (checkbox obligatoire)
bouton submit

Le formulaire doit utiliser method="POST" et envoyer vers la même page.
Inclure header.php et footer.php.

Le traitement doit s'exécuter uniquement si $_SERVER['REQUEST_METHOD'] === 'POST'.

Nettoyage des données pour chaque champ texte :
trim(), strip_tags().

Règles de validation :

nom / prenom :
obligatoires
longueur entre 2 et 30 caractères

email :
obligatoire
format valide (filter_var)

password :
obligatoire
minimum 8 caractères
doit contenir une majuscule, une minuscule, un chiffre (preg_match)

password_confirm :
obligatoire
doit être identique à password

date_naissance :
obligatoire
vérifier validité (DateTime::createFromFormat)
vérifier que l’utilisateur a au moins 18 ans

telephone :
obligatoire
vérification avec preg_match
commence par + ou un chiffre
entre 10 et 15 chiffres

pays :
obligatoire
doit appartenir à une liste définie en PHP

type_participant :
obligatoire
valeurs admises : Etudiant, Professionnel, Speaker

centres_interet :
au moins une case doit être cochée
valeurs autorisées : PHP, JavaScript, DevOps, IA

conditions :
obligatoire, sinon refus du formulaire

Gestion des erreurs :
Utiliser un tableau $errors.
Chaque erreur ajoute un message explicite.
Si des erreurs existent :
afficher la liste
ne pas rediriger
réafficher les valeurs (sauf les mots de passe)

En cas de succès :
Redirection vers success.php via GET : (header location)


Dans success.php :
Vérifier $_GET['status'] et $_GET['nom'].
Si status = ok :
afficher “Merci pour votre inscription, NOM.”
Sinon :
afficher “Accès non valide à la page de confirmation.”
Ajouter un lien vers index.php.

Lors d’erreurs dans le formulaire :
afficher les messages sous les champs ou en bas de page (au choix) 
réafficher les valeurs entrées (hors mot de passe)
*/
