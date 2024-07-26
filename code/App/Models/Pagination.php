<?php

namespace Models;

trait Pagination{
    
    protected int $amount = 15;
    protected int $offset;
    protected int $page_number;
    
    public function setAmount( int $amount ): void
    {
        $this->amount = $amount;
    }
    
    public function setOffset( int $offset ): void
    {
        $this->offset = $offset;
    }
    
    protected function calculatePagination( $page )
    {
        $this->page_number = $page;
        $this->offset = ( $this->page_number - 1 ) * $this->amount;
    }
    
    
}