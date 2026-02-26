<?php
namespace App\Controllers\Participer;
use App\Models\Participer\Participer;
use App\Models\Participer\ParticiperDAO;
use Exception;

class UpdateParticiper {

    private Participer $participation;

    public function __construct(
        $id_joueur,
        $id_match,
        $poste,
        $est_titulaire,
        $evaluation
    ) {
        $participation = new Participer(
            $id_joueur,
            $id_match,
            $poste,
            $est_titulaire,
            $evaluation
        );
        $this->participation = $participation;
    }   

    public function execute() {
        return ParticiperDAO::UpdateParticiper($this->participation);
    }
}
?>