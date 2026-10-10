<?php

namespace Puntjes\Spike\Kiota\Statistics;

use Microsoft\Kiota\Abstractions\QueryParameter;

class StatisticsRequestBuilderGetQueryParameters 
{
    /**
     * @var string|null $branch 
    */
    public ?string $branch = null;
    
    /**
     * @var GetPeriodQueryParameterType|null $period 
    */
    public ?GetPeriodQueryParameterType $period = null;
    
    /**
     * @QueryParameter("top_products_limit")
     * @var int|null $topProductsLimit 
    */
    public ?int $topProductsLimit = null;
    
    /**
     * Instantiates a new StatisticsRequestBuilderGetQueryParameters and sets the default values.
     * @param string|null $branch 
     * @param GetPeriodQueryParameterType|null $period 
     * @param int|null $top_products_limit 
    */
    public function __construct(?string $branch = null, ?GetPeriodQueryParameterType $period = null, ?int $top_products_limit = null) {
        $this->branch = $branch;
        $this->period = $period;
        $this->topProductsLimit = $top_products_limit;
    }

}
