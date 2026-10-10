<?php

namespace Puntjes\Spike\Kiota\Products;

use Microsoft\Kiota\Abstractions\QueryParameter;

class ProductsRequestBuilderGetQueryParameters 
{
    /**
     * @var string|null $category 
    */
    public ?string $category = null;
    
    /**
     * @var int|null $page 
    */
    public ?int $page = null;
    
    /**
     * @QueryParameter("per_page")
     * @var int|null $perPage 
    */
    public ?int $perPage = null;
    
    /**
     * @var string|null $search 
    */
    public ?string $search = null;
    
    /**
     * @var GetStatusQueryParameterType|null $status 
    */
    public ?GetStatusQueryParameterType $status = null;
    
    /**
     * Instantiates a new ProductsRequestBuilderGetQueryParameters and sets the default values.
     * @param string|null $category 
     * @param int|null $page 
     * @param int|null $per_page 
     * @param string|null $search 
     * @param GetStatusQueryParameterType|null $status 
    */
    public function __construct(?string $category = null, ?int $page = null, ?int $per_page = null, ?string $search = null, ?GetStatusQueryParameterType $status = null) {
        $this->category = $category;
        $this->page = $page;
        $this->perPage = $per_page;
        $this->search = $search;
        $this->status = $status;
    }

}
