<?php
namespace App\Controllers\Match;
use App\Models\Match\Match_;
use App\Models\Match\MatchDAO;
use Exception;

class ReadMatch {

    public function __construct() {
    }

    public function execute() {
        return MatchDAO::ReadMatch();
    }
}
?>