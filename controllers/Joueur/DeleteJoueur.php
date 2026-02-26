<?php
namespace App\Controllers\Joueur;

use App\Models\Joueur\JoueurDAO;
use Exception;

class DeleteJoueur
{
    private $id;

    public function __construct($id)
    {
        if (!$id) {
            throw new Exception("ID joueur manquant pour la suppression.");
        }
        $this->id = $id;
    }

    public function execute()
    {
        $dao = new JoueurDAO();
        return $dao->delete($this->id);
    }
}
