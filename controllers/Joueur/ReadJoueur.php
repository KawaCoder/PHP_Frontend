<?php
namespace App\Controllers\Joueur;

use App\Models\Joueur\JoueurDAO;

class ReadJoueur
{
    public function __construct()
    {
    }

    public function execute()
    {
        $dao = new JoueurDAO();
        return $dao->findAll();
    }
}
