<?php
namespace App\Controllers\Match;
use App\Models\Match\Match_;
use App\Models\Match\MatchDAO;
use Exception;

class UpdateMatch {

    private $id_match;
    private $date_match;
    private $nom_equipe_adverse;
    private $lieu_de_rencontre;
    private $points_subis;
    private $points_marques;
    private $domiciliation;
    private $sens_match;

    public function __construct(
        $id_match,
        $date_match,
        $nom_equipe_adverse,
        $lieu_de_rencontre,
        $points_subis,
        $points_marques,
        $domiciliation,
        $sens_match
    ) {
        $this->id_match = $id_match;
        $this->date_match = $date_match;
        $this->nom_equipe_adverse = $nom_equipe_adverse;
        $this->lieu_de_rencontre = $lieu_de_rencontre;
        $this->points_subis = $points_subis;
        $this->points_marques = $points_marques;
        $this->domiciliation = $domiciliation;
        $this->sens_match = $sens_match;
    }

    public function execute() {
        $match = new Match_(
            $this->date_match,
            $this->nom_equipe_adverse,
            $this->lieu_de_rencontre,
            $this->points_subis,
            $this->points_marques,
            $this->domiciliation,
            $this->sens_match
        );
        $match->setId($this->id_match);
        MatchDAO::UpdateMatch($match);
    }
}
?>