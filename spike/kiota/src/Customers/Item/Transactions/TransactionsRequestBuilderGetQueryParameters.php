<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Transactions;

use Microsoft\Kiota\Abstractions\QueryParameter;

class TransactionsRequestBuilderGetQueryParameters 
{
    /**
     * @QueryParameter("date_from")
     * @var string|null $dateFrom 
    */
    public ?string $dateFrom = null;
    
    /**
     * @QueryParameter("date_to")
     * @var string|null $dateTo 
    */
    public ?string $dateTo = null;
    
    /**
     * @var int|null $page 
    */
    public ?int $page = null;
    
    /**
     * Instantiates a new TransactionsRequestBuilderGetQueryParameters and sets the default values.
     * @param string|null $date_from 
     * @param string|null $date_to 
     * @param int|null $page 
    */
    public function __construct(?string $date_from = null, ?string $date_to = null, ?int $page = null) {
        $this->dateFrom = $date_from;
        $this->dateTo = $date_to;
        $this->page = $page;
    }

}
