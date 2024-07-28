<?php

namespace Entities;

use Safronik\Models\Domains\EntityObject;

class CallInterval extends EntityObject
{
    protected int $start;
    protected int $calls;
    
    public static array $rules = [
        'id'    => [ 'required', 'type' => 'string',  'length' => 64, ],
        'start' => [ 'required', 'type' => 'integer', 'length' => 11, ],
        'calls' => [ 'required', 'type' => 'integer', 'length' => 11, ],
    ];
    
    public function __construct( mixed $id, int $start, int $calls )
    {
        $this->id    = $id;
        $this->start = $start;
        $this->calls = $calls;
    }
    
    public function isPassed( $duration ): bool
    {
        return time() >= $this->start + $duration;
    }
    
    public function isLimitExceeded( $limit ): bool
    {
        return $limit <= $this->calls;
    }
    
    public function getStart(): int
    {
        return $this->start;
    }
    
    public function getCalls(): int
    {
        return $this->calls;
    }
    
    public function incrementCalls(): void
    {
        $this->calls++;
    }
}