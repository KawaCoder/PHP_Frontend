<?php
namespace App\Controllers\Participer;
use App\Models\Participer\Participer;
use App\Models\Participer\ParticiperDAO;
use Exception;

class ReadParticiperByIdMatch {

    private $id_match;

    public function __construct($id_match) {
        $this->id_match = $id_match;
    }   

    public function execute() {
        return ParticiperDAO::ReadParticiperByIdMatch($this->id_match);
    }
}
?>