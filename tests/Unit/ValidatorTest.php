<?php

namespace Tests\Unit;

use App\Utility\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testValidDataReturnsNoErrors()
    {
        $data = [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'message' => "Bonjour, votre annonce m'interesse.",
        ];

        $errors = Validator::validateContactForm($data);

        // Avec des donnees completes et un email valide, aucune erreur ne doit etre renvoyee
        $this->assertSame([], $errors);
    }

    public function testEmptyNameReturnsError()
    {
        $data = [
            'name' => '',
            'email' => 'jean.dupont@example.com',
            'message' => "Bonjour, votre annonce m'interesse.",
        ];

        $errors = Validator::validateContactForm($data);

        // Un nom vide doit declencher une erreur de validation
        $this->assertNotEmpty($errors);
    }

    public function testInvalidEmailReturnsError()
    {
        $data = [
            'name' => 'Jean Dupont',
            'email' => 'ceci-n-est-pas-un-email',
            'message' => "Bonjour, votre annonce m'interesse.",
        ];

        $errors = Validator::validateContactForm($data);

        // Un email mal forme doit declencher une erreur de validation
        $this->assertNotEmpty($errors);
    }

    public function testEmptyMessageReturnsError()
    {
        $data = [
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@example.com',
            'message' => '',
        ];

        $errors = Validator::validateContactForm($data);

        // Un message vide doit declencher une erreur de validation
        $this->assertNotEmpty($errors);
    }
}
