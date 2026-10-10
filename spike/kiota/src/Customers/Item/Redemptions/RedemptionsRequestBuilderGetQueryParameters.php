<?php

namespace Puntjes\Spike\Kiota\Customers\Item\Redemptions;

class RedemptionsRequestBuilderGetQueryParameters 
{
    /**
     * @var int|null $page 
    */
    public ?int $page = null;
    
    /**
     * @var GetStatusQueryParameterType|null $status 
    */
    public ?GetStatusQueryParameterType $status = null;
    
    /**
     * Instantiates a new RedemptionsRequestBuilderGetQueryParameters and sets the default values.
     * @param int|null $page 
     * @param GetStatusQueryParameterType|null $status 
    */
    public function __construct(?int $page = null, ?GetStatusQueryParameterType $status = null) {
        $this->page = $page;
        $this->status = $status;
    }

}
