<?php
namespace App\Controllers\Participer;
use App\Models\Participer\Participer;
use App\Models\Participer\ParticiperDAO;
use Exception;

class ReadParticiperByIdJoueur {

    private $id_joueur;

    public function __construct($id_joueur) {
        $this->id_joueur = $id_joueur;
    }   

    public function execute() {
        return ParticiperDAO::ReadParticiperByIdJoueur($this->id_joueur);
    }
}
?>