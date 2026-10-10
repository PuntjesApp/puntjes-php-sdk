<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Ledger;

use Microsoft\Kiota\Abstractions\QueryParameter;

class LedgerRequestBuilderGetQueryParameters 
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
     * @var GetTypeQueryParameterType|null $type 
    */
    public ?GetTypeQueryParameterType $type = null;
    
    /**
     * Instantiates a new LedgerRequestBuilderGetQueryParameters and sets the default values.
     * @param string|null $date_from 
     * @param string|null $date_to 
     * @param int|null $page 
     * @param GetTypeQueryParameterType|null $type 
    */
    public function __construct(?string $date_from = null, ?string $date_to = null, ?int $page = null, ?GetTypeQueryParameterType $type = null) {
        $this->dateFrom = $date_from;
        $this->dateTo = $date_to;
        $this->page = $page;
        $this->type = $type;
    }

}
