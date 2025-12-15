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

$nom = $indiv['nom'];
$prenom = $indiv['prenom'];
$email = $indiv['email'];
$password = "";
$password_confirm = "";
$date_naissance = $indiv['date_naissance'];
$telephone = $indiv['telephone'];
$pays = $indiv['pays'];
$type_participant = $indiv['type_participant'];
$centres_interet = explode(',', $indiv['centres_interet']);

$conditions_valide = $indiv['conditions_valide'];

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
        $allowed = ['PHP', 'Javascript', 'DevOps', 'IA'];
        $centres_interet = array_intersect($tmp_centres_interet, $allowed);
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

            <form action="" method="post">

                <!-- Input Nom -->

                <div class="<?php if (empty($nom))
                    echo "has-danger";
                else
                    echo "has-success"; ?>">
                    <label class="form-label mt-4" for="inputNom">Nom</label>
                    <input type="text" class="form-control <?php if (empty($nom))
                        echo "is-invalid";
                    else
                        echo "is-valid"; ?>" id="inputNom" name="nom" value="<?= $nom ?>">
                    <?php if (isset($error['nom'])) { ?>
                        <div class="<?php if (empty($nom))
                            echo "invalid-feedback";
                        else
                            echo "valid-feedback"; ?>">
                            <?= $error['nom'] ?>
                        </div>
                    <?php } ?>
                </div>

                <!-- Input Prenom -->

                <div class="<?php if (empty($prenom))
                    echo "has-danger";
                else
                    echo "has-success"; ?>">
                    <label class="form-label mt-4" for="inputPrenom">Prénom</label>
                    <input type="text" class="form-control <?php if (empty($prenom))
                        echo "is-invalid";
                    else
                        echo "is-valid"; ?>" id="inputPrenom" name="prenom" value="<?= $prenom ?>">
                    <?php if (isset($error['prenom'])) { ?>
                        <div class="<?php if (empty($prenom))
                            echo "invalid-feedback";
                        else
                            echo "valid-feedback"; ?>">
                            <?= $error['prenom'] ?>
                        </div>
                    <?php } ?>
                </div>

                <!-- Input Email -->

                <div class="<?php if (empty($email))
                    echo "has-danger";
                else
                    echo "has-success"; ?>">
                    <label class="form-label mt-4" for="inputEmail">Email</label>
                    <input type="text" class="form-control <?php if (empty($email))
                        echo "is-invalid";
                    else
                        echo "is-valid"; ?>" id="inputEmail" name="email" value="<?= $email ?>">
                    <?php if (isset($error['email'])) { ?>
                        <div class="<?php if (empty($email))
                            echo "invalid-feedback";
                        else
                            echo "valid-feedback"; ?>">
                            <?= $error['email'] ?>
                        </div>
                    <?php } ?>
                </div>

                <div class="d-flex">

                    <div class="col-6 pe-3">

                        <!-- Input Password -->

                        <div class="<?php if (empty($password))
                            echo "has-danger";
                        else
                            echo "has-success"; ?>">
                            <label class="form-label mt-4" for="inputPassword">Password</label>
                            <input type="password" class="form-control <?php if (empty($password))
                                echo "is-invalid";
                            else
                                echo "is-valid"; ?>" id="inputPassword" name="password">
                            <?php /* value="<?= $password ?>" */
                            if (isset($error['password'])) { ?>
                                <div class="<?php if (empty($password))
                                    echo "invalid-feedback";
                                else
                                    echo "valid-feedback"; ?>">
                                    <?= $error['password'] ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Input Password confirm -->

                        <div class="<?php if (empty($password_confirm))
                            echo "has-danger";
                        else
                            echo "has-success"; ?>">
                            <label class="form-label mt-4" for="inputPasswordConfirm">Confirm password</label>
                            <input type="password" class="form-control <?php if (empty($password_confirm))
                                echo "is-invalid";
                            else
                                echo "is-valid"; ?>" id="inputPasswordConfirm" name="password_confirm">
                            <?php /* value="<?= $password_confirm ?>" */
                            if (isset($error['password_confirm'])) { ?>
                                <div class="<?php if (empty($password_confirm))
                                    echo "invalid-feedback";
                                else
                                    echo "valid-feedback"; ?>">
                                    <?= $error['password_confirm'] ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Input Date Naissance -->

                        <div class="<?php if (empty($date_naissance))
                            echo "has-danger";
                        else
                            echo "has-success"; ?>">
                            <label class="form-label mt-4" for="inputDateNaissance">Date de naissance</label>
                            <input type="date" class="form-control <?php if (empty($date_naissance))
                                echo "is-invalid";
                            else
                                echo "is-valid"; ?>" id="InputDateNaissance" name="date_naissance"
                                value="<?= $date_naissance ?>">
                            <?php if (isset($error['date_naissance'])) { ?>
                                <div class="<?php if (empty($date_naissance))
                                    echo "invalid-feedback";
                                else
                                    echo "valid-feedback"; ?>">
                                    <?= $error['date_naissance'] ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Input Telephone -->

                        <div class="<?php if (empty($telephone))
                            echo "has-danger";
                        else
                            echo "has-success"; ?>">
                            <label class="form-label mt-4" for="inputTelephone">Téléphone</label>
                            <input type="text" class="form-control <?php if (empty($telephone))
                                echo "is-invalid";
                            else
                                echo "is-valid"; ?>" id="inputTelephone" name="telephone" value="<?= $telephone ?>">
                            <?php if (isset($error['telephone'])) { ?>
                                <div class="<?php if (empty($telephone))
                                    echo "invalid-feedback";
                                else
                                    echo "valid-feedback"; ?>">
                                    <?= $error['telephone'] ?>
                                </div>
                            <?php } ?>
                        </div>

                        <!-- Input selector Pays -->

                        <div>
                            <label for="selectPays" class="form-label mt-4">Pays</label>
                            <select class="form-select" id="selectPays" name="pays">
                                <option disabled <?php if (empty($pays))
                                    echo "selected"; ?>>Selectionnez un pays
                                </option>
                                <?php foreach ($liste_pays as $item) { ?>
                                    <option <?php if ($item === $pays)
                                        echo "selected" ?>><?= $item ?></option>
                                <?php } ?>
                            </select>

                            <?php if (isset($error['pays'])) { ?>
                                <div class="<?php if (empty($pays))
                                    echo "invalid-feedback";
                                else
                                    echo "valid-feedback"; ?> d-block">
                                    <?= $error['pays'] ?>
                                </div>
                            <?php } ?>
                        </div>

                    </div>

                    <div class="col-6 ps-3">
                        <!-- Input type de participant -->

                        <fieldset>
                            <legend class="mt-4">Type de participant</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type_participant"
                                    id="optionsRadiosEtudiant" value="Etudiant(e)" <?php if ($type_participant === 'Etudiant(e)')
                                        echo 'checked=""'; ?>>
                                <label class="form-check-label" for="optionsRadiosEtudiant">
                                    Etudiant(e)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type_participant"
                                    id="optionsRadiosProfessionnel" value="Professionnel(le)" <?php if ($type_participant === 'Professionnel(le)')
                                        echo 'checked=""'; ?>>
                                <label class="form-check-label" for="optionsRadiosProfessionnel">
                                    Professionnel(le)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type_participant"
                                    id="optionsRadiosSpeaker" value="Speaker" <?php if ($type_participant === 'Speaker')
                                        echo 'checked=""'; ?>>
                                <label class="form-check-label" for="optionsRadiosSpeaker">
                                    Speaker
                                </label>
                            </div>

                            <?php if (isset($error['type_participant'])) { ?>
                                <div class="<?php if (empty($type_participant))
                                    echo "invalid-feedback";
                                else
                                    echo "valid-feedback"; ?> d-block">
                                    <?= $error['type_participant'] ?>
                                </div>
                            <?php } ?>

                        </fieldset>

                        <!-- Input centres d'interet -->

                        <fieldset>
                            <legend class="mt-4">Centres d'interêt</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="centres_interet[]" value="PHP"
                                    id="optionsCheckboxPhp" <?php if (in_array('PHP', $centres_interet))
                                        echo "checked"; ?>>
                                <label class="form-check-label" for="optionsCheckboxPhp">
                                    PHP
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="centres_interet[]"
                                    value="Javascript" id="optionsCheckboxJavascrit" <?php if (in_array("Javascript", $centres_interet))
                                        echo "checked"; ?>>
                                <label class="form-check-label" for="optionsCheckboxJavascrit">
                                    Javascript
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="centres_interet[]" value="DevOps"
                                    id="optionsCheckboxDevops" <?php if (in_array("devops", $centres_interet))
                                        echo "checked"; ?>>
                                <label class="form-check-label" for="optionsCheckboxDevops">
                                    DevOps
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="centres_interet[]" value="IA"
                                    id="optionsCheckboxIa" <?php if (in_array("IA", $centres_interet))
                                        echo "checked"; ?>>
                                <label class="form-check-label" for="optionsCheckboxIa">
                                    IA
                                </label>
                            </div>
                        </fieldset>

                        <?php if (isset($error['centres_interet'])) { ?>
                            <div class="<?php if (count($centres_interet) < 1)
                                echo "invalid-feedback";
                            else
                                echo "valid-feedback"; ?> d-block">
                                <?= $error['centres_interet'] ?>
                            </div>
                        <?php } ?>

                        <fieldset>
                            <legend class="mt-4">Accepter les conditions d'utilisation</legend>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="conditions_valide"
                                    id="optionsRadios1" value="conditions_valide" <?php if ($conditions_valide)
                                        echo "checked"; ?>>
                                <label class="form-check-label" for="optionsRadios1">
                                    J'accepte
                                </label>
                            </div>
                            <?php if (isset($error['conditions_valide'])) { ?>
                                <div class="<?php if (!$conditions_valide)
                                    echo "invalid-feedback";
                                else
                                    echo "valid-feedback"; ?> d-block">
                                    <?= $error['conditions_valide'] ?>
                                </div>
                            <?php } ?>
                        </fieldset>

                        <button type="submit" class="btn btn-primary mt-3 px-5" name="envoyer">Envoyer</button>

                        <?php if (isset($error['sql'])) { ?>
                            <div class="<?php if (isset($error['sql']))
                                echo "invalid-feedback";
                            else
                                echo "valid-feedback"; ?> d-block">
                                <?= $error['sql'] ?>
                            </div>
                        <?php } ?>

                    </div>

                </div>

            </form>
        </div>
    </main>
    <?php include "./footer.php" ?>
</body>

</html>