<?php
namespace App\Controllers\Participer;
use App\Models\Participer\Participer;
use App\Models\Participer\ParticiperDAO;
use Exception;

class DeleteParticiper {

    private $id_joueur;
    private $id_match;

    public function __construct(
        $id_joueur,
        $id_match
    ) {
        $this->id_joueur = $id_joueur;
        $this->id_match = $id_match;
    }   

    public function execute() {
        return ParticiperDAO::DeleteParticiper($this->id_joueur, $this->id_match);
    }
}
?>