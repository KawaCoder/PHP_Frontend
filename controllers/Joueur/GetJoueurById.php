<?php
namespace App\Controllers\Joueur;

use App\Models\Joueur\JoueurDAO;

class GetJoueurById
{
    private $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function execute()
    {
        $dao = new JoueurDAO();
        return $dao->find($this->id);
    }
}
