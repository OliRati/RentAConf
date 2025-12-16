<?php
include "./functions.php";
require './connexion-bd.php';

function validatePassword($password)
{
    $errors = "";

    // Check minimum length
    if (strlen($password) < 8) {
        $errors .= "at least 8 characters";
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        if (!empty($errors))
            $errors .= ", ";
        $errors .= "at least one uppercase letter";
    }

    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        if (!empty($errors))
            $errors .= ", ";
        $errors .= "at least one lowercase letter";
    }

    // Check for at least one digit
    if (!preg_match('/[0-9]/', $password)) {
        if (!empty($errors))
            $errors .= ", ";
        $errors .= "at least one digit";
    }

    if (!empty($errors))
        $errors = "Password must contain " . $errors . ".";

    return $errors;
}

/**
 * Vérifie si une date est valide selon un format donné
 *
 * @param string $date   La date à vérifier
 * @param string $format Le format attendu (ex: 'd/m/Y')
 * @return bool          true si valide, false sinon
 */
function isValidDate(string $date, string $format = 'd/m/Y'): bool
{
    // Crée un objet DateTime à partir du format
    $dt = DateTime::createFromFormat($format, $date);

    // Vérifie :
    // 1. Que la création a réussi
    // 2. Qu'il n'y a pas d'erreurs ou avertissements
    // 3. Que la date reformatée correspond exactement à l'entrée
    return $dt
        && $dt->format($format) === $date
        && empty(DateTime::getLastErrors()['warning_count'])
        && empty(DateTime::getLastErrors()['error_count']);
}

/**
 * Vérifie si un numéro de téléphone est dans un format valide
 * 
 * @param mixed $phone   Le numero de téléphone à vérifier
 * @return bool          true si valide, false sinon
 */
function isValidPhone($phone)
{
    //
    // /^           Debut du regex
    // (?:\+?\d) 
    //    \+?       Le signe + est optionnel (0 ou 1 fois)
    //    \d        Un chiffre obligatoire juste après
    //
    // [0-9]{9,14}  Après le premier chiffre, il doit y avoir entre 9 et 14 chiffres supplementaires
    // $/           Fin du regex
    //

    $pattern = '/^(?:\+?\d)[0-9]{9,14}$/';
    return preg_match($pattern, $phone);
}

$nom = "";
$prenom = "";
$email = "";
$password = "";
$password_confirm = "";
$date_naissance = "";
$telephone = "";
$pays = "";
$type_participant = "";
$centres_interet = [];

$conditions_valide = false;

$liste_pays = [
    'Andorre',
    'Allemagne',
    'Angleterre',
    'Belgique',
    'Canada',
    'Espagne',
    'France',
    'Grece',
    'Italie',
    'Japon',
    'Luxembourg',
    'Monaco',
    'Suède',
    'Suisse'
];

$liste_interets = ['PHP', 'Javascript', 'DevOps', 'IA'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['nom'])) {
        $tmp_nom = trim(strip_tags($_POST['nom']));

        if ($tmp_nom === "")
            $error['nom'] = "Le nom ne peut être vide";
        elseif ((strlen($tmp_nom) < 2) || (strlen($tmp_nom) > 30))
            $error['nom'] = "Le nom doit avoir entre 2 et 30 caractères";
        else {
            $nom = $tmp_nom;
        }
    } else {
        // Just in case of manipulation from the front side
        $error['nom'] = 'Le nom doit être défini';
    }

    if (isset($_POST['prenom'])) {
        $tmp_prenom = trim(strip_tags($_POST['prenom']));

        if ($tmp_prenom === "")
            $error['prenom'] = "Le prenom ne peut pas être vide";
        elseif ((strlen($tmp_prenom) < 2) || (strlen($tmp_prenom) > 30))
            $error['prenom'] = "Le prenom doit avoir entre 2 et 30 caractères";
        else {
            $prenom = $tmp_prenom;
        }
    } else {
        $error['prenom'] = "Le prenom doit être défini";
    }

    if (isset($_POST['email'])) {
        $tmp_email = trim(htmlentities($_POST['email']));
        if ($tmp_email === '')
            $error['email'] = "L'email ne doit pas etre vide.";
        elseif (!filter_var($tmp_email, FILTER_VALIDATE_EMAIL))
            $error['email'] = "L'email n'est pas valide";
        else {
            $email = $tmp_email;
        }
    } else {
        $error['email'] = "L'email doit être défini";
    }

    if (isset($_POST['password'])) {
        $tmp_password = $_POST['password'];

        if ($tmp_password === '')
            $error['password'] = "Le mot de passe ne doit pas être vide";
        elseif ((strlen($tmp_password) < 8) || (strlen($tmp_password) > 50))
            $error['password'] = "Le mot de passe doit contenir entre 8 et 50 caractères.";
        else {
            $ret = validatePassword($tmp_password);
            if (empty($ret)) {
                $password = $tmp_password;
            } else
                $error['password'] = $ret;
        }
    } else {
        $error['password'] = "Le mot de passe doit être défini";
    }

    if (isset($_POST['password_confirm'])) {
        $tmp_password_confirm = $_POST['password_confirm'];

        if ($tmp_password_confirm === '')
            $error['password_confirm'] = "La confirmation du mot de passe doit être défini";
        elseif (!empty($password)) {
            if ($tmp_password_confirm != $password)
                $error['password_confirm'] = "Les mots de passe sont différents";
            else {
                $password_confirm = $tmp_password_confirm;
            }
        } else {
            $error['password_confirm'] = "Entrez un mot de passe, puis sa confirmation";
        }
    } else {
        $error['password_confirm'] = "La confirmation du mot de passe doit être defini";
    }

    if (isset($_POST['date_naissance'])) {
        $tmp_date_naissance = trim(strip_tags($_POST['date_naissance']));
        if ($tmp_date_naissance === '')
            $error['date_naissance'] = 'La date de naissance est requise';
        else {
            if (!isValidDate($tmp_date_naissance, 'Y-m-d'))
                $error['date_naissance'] = "La date de naissance n'est pas valide";
            else {
                $date_obj = new DateTime($tmp_date_naissance);
                $today = new DateTime();
                $age = $today->diff($date_obj)->y;
                if ($age < 18)
                    $error['date_naissance'] = "Vous devez avoir au moins 18 ans";
                else {
                    $date_naissance = $tmp_date_naissance;
                }
            }
        }
    } else {
        $error['date_naissance'] = "La date de naissance doit être définie";
    }

    if (isset($_POST['telephone'])) {
        $tmp_telephone = trim(strip_tags($_POST['telephone']));
        if ($tmp_telephone === '')
            $error['telephone'] = 'Le numéro de téléphone est requis';
        else {
            if (isValidPhone($tmp_telephone)) {
                $telephone = $tmp_telephone;
            } else
                $error['telephone'] = "Le numéro de téléphone n'est pas valide";
        }
    } else {
        $error['telephone'] = "Le numéro de téléphone doit être défini";
    }

    if (isset($_POST['pays'])) {
        $tmp_pays = trim(strip_tags($_POST['pays']));

        // Sanitize values received with allowed list
        if (in_array($tmp_pays, $liste_pays))
            $pays = $tmp_pays;
    }

    if (empty($pays)) {
        $error['pays'] = "Choisissez un pays d'origine";
    }

    if (isset($_POST['type_participant'])) {
        $tmp_type_participant = $_POST['type_participant'];

        // Sanitize values received with allowed list
        $allowed = ['Etudiant(e)', 'Professionnel(le)', 'Speaker'];
        if (in_array($tmp_type_participant, $allowed)) {
            $type_participant = $tmp_type_participant;
        }
    }

    if (empty($type_participant))
        $error['type_participant'] = 'Selectionnez un type de participation';

    if (isset($_POST['centres_interet'])) {
        $tmp_centres_interet = $_POST['centres_interet'];

        // Sanitize values received with allowed list
        $centres_interet = array_intersect($tmp_centres_interet, $liste_interets);
    }

    if (count($centres_interet) < 1) {
        $error['centres_interet'] = "Au moins un centre d'interêt doit être sélectionné";
    }

    if (isset($_POST['conditions_valide'])) {
        $conditions_valide = true;
    } else {
        $error['conditions_valide'] = "Vous devez accepter les conditions pour utiliser le service";
        $conditions_valide = false;
    }

    if (isset($_POST['envoyer'])) {
        if (empty($error)) {
            // Here everythings is valid

            try {
                $pdo = new PDO($dsn, $user, $pass, $options);

                // Connection to database is established

                // Prepare sql request
                $sql = "INSERT INTO participants (
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
                        );";

                $stmt = $pdo->prepare($sql);

                // Set vars and execute request

                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $centres_interet_string = implode(',', $centres_interet);

                $date_inscription = date('Y-m-d H:i:s');

                $stmt->execute([
                    ':nom' => $nom,
                    ':prenom' => $prenom,
                    ':email' => $email,
                    ':password_hash' => $password_hash,
                    ':date_naissance' => $date_naissance,
                    ':telephone' => $telephone,
                    ':pays' => $pays,
                    ':type_participant' => $type_participant,
                    ':centres_interet' => $centres_interet_string,
                    ':conditions_valide' => $conditions_valide,
                    ':date_inscription' => $date_inscription,
                ]);

            } catch (PDOException $e) {
                // Log error, don’t expose details to user
                error_log("Database error: " . $e->getMessage());

                // Show generic message
                $error['sql'] = "Something went wrong. Please try again later.";
            }

            if (!isset($error)) {
                header("Location: success.php?status=ok&nom=" . urlencode($nom));
                exit;
            }
        }
    }
}
?>

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
            <h1>Inscription à la conférence</h1>

            <?php
            const FORM_TYPE = "add";
            require "formulaire.php"; ?>
        </div>
    </main>
    <?php include "./footer.php" ?>
</body>

</html>