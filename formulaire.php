<?php
// Formulaire Ajouter / Modifier
// const FORM_TYPE should be 'edit' or 'add'
?>
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

            <?php if (FORM_TYPE != 'edit') { ?>
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

            <?php } ?>

            <!-- Input Date Naissance -->

            <div class="<?php if (empty($date_naissance))
                echo "has-danger";
            else
                echo "has-success"; ?>">
                <label class="form-label mt-4" for="inputDateNaissance">Date de naissance</label>
                <input type="date" class="form-control <?php if (empty($date_naissance))
                    echo "is-invalid";
                else
                    echo "is-valid"; ?>" id="InputDateNaissance" name="date_naissance" value="<?= $date_naissance ?>">
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
                    <input class="form-check-input" type="radio" name="type_participant" id="optionsRadiosEtudiant"
                        value="Etudiant(e)" <?php if ($type_participant === 'Etudiant(e)')
                            echo 'checked=""'; ?>>
                    <label class="form-check-label" for="optionsRadiosEtudiant">
                        Etudiant(e)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_participant" id="optionsRadiosProfessionnel"
                        value="Professionnel(le)" <?php if ($type_participant === 'Professionnel(le)')
                            echo 'checked=""'; ?>>
                    <label class="form-check-label" for="optionsRadiosProfessionnel">
                        Professionnel(le)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="type_participant" id="optionsRadiosSpeaker"
                        value="Speaker" <?php if ($type_participant === 'Speaker')
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
                <?php
                foreach ($liste_interets as $interet) {
                    ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="centres_interet[]" value="<?= $interet ?>"
                            id="optionsCheckbox<?= $interet ?>" <?php if (in_array($interet, $centres_interet))
                                  echo "checked"; ?>>
                        <label class="form-check-label" for="optionsCheckbox<?= $interet ?>">
                            <?= $interet ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </fieldset>

            <?php if (isset($error['centres_interet'])) { ?>
                <div class="<?php if (count($centres_interet) < 1)
                    echo "invalid-feedback";
                else
                    echo "valid-feedback"; ?> d-block">
                    <?= $error['centres_interet'] ?>
                </div>
            <?php } ?>

            <?php if (FORM_TYPE != "edit") { ?>
                <fieldset>
                    <legend class="mt-4">Accepter les conditions d'utilisation</legend>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="conditions_valide" id="optionsRadios1"
                            value="conditions_valide" <?php if ($conditions_valide)
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
            <?php } ?>

            <button type="submit" class="btn btn-primary mt-3 px-5" name="envoyer">
                <?php if (FORM_TYPE === 'edit')
                    echo 'Modifier';
                else
                    echo 'Envoyer'; ?></button>

            <?php if (FORM_TYPE === 'edit') { ?>
                <button type="submit" class="btn btn-primary mt-3 px-5" name="annuler">Annuler</button>
            <?php } ?>

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