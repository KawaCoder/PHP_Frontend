<?php
namespace Controllers\Commentaire;

class CommentaireDTO
{
    public $id_commentaire;
    public $date_commentaire;
    public $commentaire;
    public $id_joueur;

    public function __construct(array $data) {
        $this->id_commentaire = $data['id_commentaire'];
        $this->date_commentaire = $data['date_commentaire']';
        $this->commentaire = $data['commentaire'];
        $this->id_joueur = $data['id_joueur'];
    }
}
