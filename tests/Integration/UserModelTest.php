<?php

namespace Tests\Integration;

use App\Config;
use App\Models\User;
use App\Utility\Hash;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 * Tests d'integration du modele User : ces tests utilisent la VRAIE base MySQL
 * (le conteneur db_dev), via la configuration de App\Config.
 *
 * Un utilisateur de test dedie (email "phpunit.*@example.com") est cree avant
 * chaque test puis supprime apres, pour ne laisser aucune trace en base.
 */
class UserModelTest extends TestCase
{
    private const TEST_EMAIL = 'phpunit.user@example.com';
    private const TEST_USERNAME = 'phpunit_test_user';
    private const TEST_PASSWORD = 'motdepasse';
    private const TEST_SALT = 'sel-de-test-phpunit';

    /** @var int Identifiant de l'utilisateur de test cree dans setUp() */
    private $testUserId;

    protected function setUp(): void
    {
        // Securite : on supprime d'abord d'eventuels restes d'un test precedent
        // qui aurait echoue avant son nettoyage.
        $this->deleteTestUsers();

        // On cree un utilisateur de test dedie, avec le meme schema de donnees
        // que App\Controllers\User::register() (username, email, password hashe, salt).
        $this->testUserId = User::createUser([
            'username' => self::TEST_USERNAME,
            'email' => self::TEST_EMAIL,
            'password' => Hash::generate(self::TEST_PASSWORD, self::TEST_SALT),
            'salt' => self::TEST_SALT,
        ]);
    }

    protected function tearDown(): void
    {
        // Nettoyage systematique, meme si le test a echoue : on ne laisse
        // aucun utilisateur "phpunit.*" en base.
        $this->deleteTestUsers();
    }

    /**
     * Ouvre une connexion PDO avec les memes identifiants que l'application
     * (App\Config : meme hote db_dev, meme base vide_grenier).
     */
    private function connectToDatabase(): PDO
    {
        $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8';

        return new PDO($dsn, Config::DB_USER, Config::DB_PASSWORD);
    }

    /**
     * Supprime tous les utilisateurs de test dont l'email correspond au
     * motif "phpunit.*@example.com".
     */
    private function deleteTestUsers(): void
    {
        $db = $this->connectToDatabase();
        $db->exec("DELETE FROM users WHERE email LIKE 'phpunit.%@example.com'");
    }

    public function testCreateUserCanBeRetrievedByLogin()
    {
        // L'identifiant retourne par createUser() doit etre un identifiant valide
        $this->assertGreaterThan(0, (int) $this->testUserId);

        $retrievedUser = User::getByLogin(self::TEST_EMAIL);

        // On verifie que toutes les informations enregistrees sont relues a l'identique
        $this->assertSame(self::TEST_USERNAME, $retrievedUser['username']);
        $this->assertSame(self::TEST_EMAIL, $retrievedUser['email']);
        $this->assertSame(Hash::generate(self::TEST_PASSWORD, self::TEST_SALT), $retrievedUser['password']);
        $this->assertSame(self::TEST_SALT, $retrievedUser['salt']);
    }

    public function testUpdateRememberTokenCanBeRetrievedThenCleared()
    {
        $tokenHash = Hash::generate('jeton-de-test-phpunit');

        // 1. On enregistre le hash du token "se souvenir de moi" pour cet utilisateur,
        //    comme le fait App\Controllers\User::login() a la connexion.
        User::updateRememberToken($this->testUserId, $tokenHash);

        // 2. L'utilisateur doit etre retrouvable via son id + le hash du token.
        $foundUser = User::getByRememberToken($this->testUserId, $tokenHash);

        $this->assertIsArray($foundUser);
        $this->assertSame(self::TEST_USERNAME, $foundUser['username']);

        // 3. On efface le token, comme le fait App\Controllers\User::logoutAction().
        User::updateRememberToken($this->testUserId, null);

        // 4. Une fois le token efface, il ne doit plus permettre de retrouver l'utilisateur.
        $userAfterClear = User::getByRememberToken($this->testUserId, $tokenHash);

        $this->assertFalse($userAfterClear);
    }
}
