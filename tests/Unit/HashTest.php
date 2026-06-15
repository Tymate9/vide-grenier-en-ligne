<?php

namespace Tests\Unit;

use App\Utility\Hash;
use PHPUnit\Framework\TestCase;

class HashTest extends TestCase
{
    public function testGenerateReturns64HexChars()
    {
        $result = Hash::generate('mot-de-passe');

        // Un hash SHA-256 fait toujours 64 caracteres hexadecimaux (0-9 et a-f)
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $result);
    }

    public function testGenerateIsReproducible()
    {
        $firstResult = Hash::generate('mot-de-passe', 'sel123');
        $secondResult = Hash::generate('mot-de-passe', 'sel123');

        // Avec la meme chaine et le meme sel, le resultat doit toujours etre identique
        $this->assertSame($firstResult, $secondResult);
    }

    public function testGenerateWithDifferentSaltGivesDifferentResult()
    {
        $resultWithSaltA = Hash::generate('mot-de-passe', 'selA');
        $resultWithSaltB = Hash::generate('mot-de-passe', 'selB');

        // Changer le sel doit changer le hash, meme si la chaine d'origine est identique
        $this->assertNotSame($resultWithSaltA, $resultWithSaltB);
    }

    public function testGenerateSaltReturnsRequestedLength()
    {
        $salt = Hash::generateSalt(32);

        // La longueur de la chaine generee doit correspondre exactement a la longueur demandee
        $this->assertSame(32, strlen($salt));
    }

    public function testGenerateSaltOnlyUsesAllowedCharacters()
    {
        $allowedCharacters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789/\\][{}\'\";:?.>,<!@#$%^&*()-_=+|";

        $salt = Hash::generateSalt(50);

        for ($i = 0; $i < strlen($salt); $i++) {
            // Chaque caractere genere doit appartenir au jeu de caracteres autorise
            $this->assertStringContainsString($salt[$i], $allowedCharacters);
        }
    }

    public function testGenerateSaltProducesDifferentResultsOnEachCall()
    {
        $firstSalt = Hash::generateSalt(32);
        $secondSalt = Hash::generateSalt(32);

        // Deux appels successifs doivent produire des resultats differents (caractere aleatoire)
        $this->assertNotSame($firstSalt, $secondSalt);
    }
}
