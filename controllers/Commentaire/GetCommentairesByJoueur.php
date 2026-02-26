<?php
namespace App\Controllers\Commentaire;

use App\Models\Commentaire\CommentaireDAO;

class GetCommentairesByJoueur
{
    private $id_joueur;

    public function __construct($id_joueur)
    {
        $this->id_joueur = $id_joueur;
    }

    public function execute()
    {
        $dao = new CommentaireDAO();
        return $dao->findByJoueur($this->id_joueur);
    }
}
