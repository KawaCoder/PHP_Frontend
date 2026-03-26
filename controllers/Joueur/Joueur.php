<?php
namespace Controllers\Joueur;

class JoueurDTO
{
    public $id_joueur;
    public $nom_joueur;
    public $prenom_joueur;
    public $numero_licence;
    public $date_naiss;
    public $taille;
    public $poids;
    public $statut_joueur;
    public $commentaire;

    public function __construct(array $data) {
        $this->id_joueur = $data['id_joueur'];
        $this->nom_joueur = $data['nom_joueur'];
        $this->prenom_joueur = $data['prenom_joueur'];
        $this->numero_licence = $data['numero_licence'];
        $this->date_naiss = $data['date_naiss'];
        $this->taille = $data['taille'];
        $this->poids = $data['poids'];
        $this->statut_joueur = $data['statut_joueur'];
        $this->commentaire = $data['commentaire'];
    }
}