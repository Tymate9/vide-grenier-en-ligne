<?php

namespace App\Models;

use App\Utility\Hash;
use Core\Model;
use App\Core;
use Exception;
use App\Utility;

/**
 * User Model:
 */
class User extends Model {

    /**
     * Crée un utilisateur
     */
    public static function createUser($data) {
        $db = static::getDB();

        $stmt = $db->prepare('INSERT INTO users(username, email, password, salt) VALUES (:username, :email, :password,:salt)');

        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':salt', $data['salt']);

        $stmt->execute();

        return $db->lastInsertId();
    }

    public static function getByLogin($login)
    {
        $db = static::getDB();

        $stmt = $db->prepare("
            SELECT * FROM users WHERE ( users.email = :email) LIMIT 1
        ");

        $stmt->bindParam(':email', $login);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un utilisateur à partir de son identifiant et du hash
     * de son token "se souvenir de moi"
     */
    public static function getByRememberToken($userId, $tokenHash)
    {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT * FROM users WHERE id = :id AND remember_token = :token LIMIT 1');

        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':token', $tokenHash);
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Enregistre (ou efface si null) le hash du token "se souvenir de moi" d'un utilisateur
     */
    public static function updateRememberToken($userId, $tokenHash)
    {
        $db = static::getDB();

        $stmt = $db->prepare('UPDATE users SET remember_token = :token WHERE id = :id');

        $stmt->bindParam(':id', $userId);

        if ($tokenHash === null) {
            $stmt->bindValue(':token', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':token', $tokenHash);
        }

        $stmt->execute();
    }

    /**
     * Tente de restaurer la session de l'utilisateur à partir du cookie
     * "se souvenir de moi" (id:token)
     */
    public static function loginFromRememberMeCookie()
    {
        list($userId, $token) = array_pad(explode(':', $_COOKIE['remember_me'], 2), 2, null);

        if (empty($userId) || empty($token)) {
            return;
        }

        $user = static::getByRememberToken($userId, Hash::generate($token));

        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
            ];
        }
    }


    /**
     * ?
     * @access public
     * @return string|boolean
     * @throws Exception
     */
    public static function login() {
        $db = static::getDB();

        $stmt = $db->prepare('SELECT * FROM articles WHERE articles.id = ? LIMIT 1');

        $stmt->execute([$id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


}
