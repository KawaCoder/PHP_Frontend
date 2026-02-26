<?php
namespace App\Controllers\Match;
use App\Models\Match\Match_;
use App\Models\Match\MatchDAO;
use Exception;

class DeleteMatch {

    public string $id_match;

    public function __construct($id_match) {
        $this->id_match = $id_match;
    }

    public function execute() {
        try {
            MatchDAO::DeleteMatch($this->id_match);
            return TRUE;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
?>