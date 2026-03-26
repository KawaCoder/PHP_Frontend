<?php
namespace Controllers\Match;

class MatchDTO {

    private $id_match;
    private $date_match;
    private $nom_equipe_adverse;
    private $lieu_de_rencontre;
    private $points_subis;
    private $points_marques;
    private $domiciliation;
    private $sens_match;

    public function __construct(array $data) {
        $this->id_match = $data['id_match'];
        $this->date_match = $data['date_match'];
        $this->nom_equipe_adverse = $data['nom_equipe_adverse'];
        $this->lieu_de_rencontre = $data['lieu_de_rencontre'];
        $this->points_subis = $data['points_subis'];
        $this->points_marques = $data['points_marques'];
        $this->domiciliation = $data['domiciliation'];
        $this->sens_match = $data['sens_match'];
    }
}
?>