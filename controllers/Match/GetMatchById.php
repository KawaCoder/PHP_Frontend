<?php
namespace App\Controllers\Match;
use App\Models\Match\Match_;
use App\Models\Match\MatchDAO;
use Exception;

class GetMatchById {

    private string $id_match;

    public function __construct($id_match) {
        $this->id_match = $id_match;
    }

    public function execute() {
        return MatchDAO::GetMatchById($this->id_match);
    }
}
?>