<?php
namespace Controllers\Participer;

class Participer {

    public $id_joueur;
    public $id_match;
    public $poste;
    public $est_titulaire;
    public $evaluation;

    public function __construct(array $data) {
        $this->id_joueur = $data['id_joueur'];
        $this->id_match = $data['id_match'];
        $this->poste = $data['poste'];
        $this->est_titulaire = $data['est_titulaire'];
        $this->evaluation = $data['evaluation'];
    }
}
?>