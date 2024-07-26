<?php

namespace Safronik\Core\Modules\DB\Repositories;

use Safronik\DB\DB;

abstract class _BaseRepository{
    
    protected DB $db;
    
    public function __construct( DB $db )
    {
        $this->db = $db;
    }
}