<?php
namespace App\Controllers\Match;
use App\Models\Match\Match_;
use App\Models\Match\MatchDAO;
use Exception;

class CreateMatch {

    private Match_ $match;

    public function __construct(
        $date_match,
        $nom_equipe_adverse,
        $lieu_de_rencontre,
        $points_subis,
        $points_marques,
        $domiciliation,
        $sens_match
    ) {
        $match = new Match_(
            $date_match,
            $nom_equipe_adverse,
            $lieu_de_rencontre,
            $points_subis,
            $points_marques,
            $domiciliation,
            $sens_match
        );
        $this->match = $match;
    }   

    public function execute() {
        return MatchDAO::CreateMatch($this->match);
    }
}
?>