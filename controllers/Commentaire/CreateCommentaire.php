<?php
namespace App\Controllers\Commentaire;

use App\Models\Commentaire\Commentaire;
use App\Models\Commentaire\CommentaireDAO;
use Exception;
use DateTime;

class CreateCommentaire
{
    private Commentaire $commentaire;

    public function __construct($id_joueur, $texte)
    {
        $texte = trim($texte ?? '');
        if (!$id_joueur) {
            throw new Exception("ID joueur manquant pour le commentaire.");
        }
        if ($texte === '') {
            throw new Exception("Le contenu du commentaire ne peut pas être vide.");
        }

        $comm = new Commentaire();
        $comm->setIdJoueur($id_joueur);
        $comm->setCommentaire($texte);
        $comm->setDateCommentaire((new DateTime())->format('Y-m-d'));

        $this->commentaire = $comm;
    }

    public function execute()
    {
        $dao = new CommentaireDAO();
        return $dao->create($this->commentaire);
    }
}
