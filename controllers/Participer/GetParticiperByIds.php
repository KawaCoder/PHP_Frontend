<?php
namespace App\Controllers\Participer;
use App\Models\Participer\Participer;
use App\Models\Participer\ParticiperDAO;

class GetParticiperByIds {

    private $id_joueur;
    private $id_match;

    public function __construct($id_joueur, $id_match) {
        $this->id_joueur = $id_joueur;
        $this->id_match = $id_match;
    }   

    public function execute() {
        return ParticiperDAO::getParticiperByIds($this->id_joueur, $this->id_match);
    }
}
?>