<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Utility\Upload;
use App\Utility\Validator;
use \Core\View;

/**
 * Product controller
 */
class Product extends \Core\Controller
{

    /**
     * Affiche la page d'ajout
     * @return void
     */
    public function indexAction()
    {
        $errors = [];

        if(isset($_POST['submit'])) {

            if (empty($_FILES['picture']['name']) || $_FILES['picture']['error'] === UPLOAD_ERR_NO_FILE) {
                $errors[] = 'Une photo est obligatoire pour publier une annonce.';
            }

            if (empty($errors)) {
                try {
                    $f = $_POST;

                    $f['user_id'] = $_SESSION['user']['id'];
                    $id = Articles::save($f);

                    $pictureName = Upload::uploadFile($_FILES['picture'], $id);

                    Articles::attachPicture($id, $pictureName);

                    header('Location: /product/' . $id);
                } catch (\Exception $e){
                        var_dump($e);
                }
            }
        }

        View::renderTemplate('Product/Add.html', [
            'errors' => $errors
        ]);
    }

    /**
     * Affiche la page d'un produit
     * @return void
     */
    public function showAction()
    {
        $id = $this->route_params['id'];
        $contactErrors = [];
        $contactSuccess = false;

        try {
            if (!isset($_POST['submit'])) {
                Articles::addOneView($id);
            }
            $suggestions = Articles::getSuggest();
            $article = Articles::getOne($id);
        } catch(\Exception $e){
            var_dump($e);
        }

        if (isset($_POST['submit'])) {
            $f = $_POST;

            $contactErrors = Validator::validateContactForm($f);

            if (empty($contactErrors)) {
                $senderName = str_replace(["\r", "\n"], '', $f['name']);
                $senderEmail = str_replace(["\r", "\n"], '', $f['email']);

                $subject = 'Vide Grenier en Ligne - ' . $article[0]['name'];
                $body = "Message de {$senderName} ({$senderEmail}) :\n\n{$f['message']}";
                $headers = "From: {$senderEmail}\r\nReply-To: {$senderEmail}";

                if (mail($article[0]['email'], $subject, $body, $headers)) {
                    $contactSuccess = true;
                } else {
                    $contactErrors[] = "Une erreur est survenue lors de l'envoi du message.";
                }
            }
        }

        View::renderTemplate('Product/Show.html', [
            'article' => $article[0],
            'suggestions' => $suggestions,
            'contactErrors' => $contactErrors,
            'contactSuccess' => $contactSuccess
        ]);
    }
}
