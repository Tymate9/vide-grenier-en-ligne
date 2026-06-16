<?php

namespace App\Utility;

/**
 * Validator:
 */
class Validator {

    /**
     * Valide les champs du formulaire de contact d'une annonce.
     *
     * @param array $data Données du formulaire (clés attendues : name, email, message)
     * @return array Liste des messages d'erreur (vide si les données sont valides)
     */
    public static function validateContactForm($data) {
        $errors = [];

        if (empty($data['name']) || empty($data['message']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Merci de renseigner votre nom, un email valide et un message.';
        }

        return $errors;
    }

}
