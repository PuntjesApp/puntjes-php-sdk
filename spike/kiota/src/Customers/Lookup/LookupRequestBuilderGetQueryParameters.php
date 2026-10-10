<?php

namespace Puntjes\Spike\Kiota\Customers\Lookup;

use Microsoft\Kiota\Abstractions\QueryParameter;

class LookupRequestBuilderGetQueryParameters 
{
    /**
     * @QueryParameter("external_id")
     * @var string|null $externalId 
    */
    public ?string $externalId = null;
    
    /**
     * @var string|null $identifier 
    */
    public ?string $identifier = null;
    
    /**
     * Instantiates a new LookupRequestBuilderGetQueryParameters and sets the default values.
     * @param string|null $external_id 
     * @param string|null $identifier 
    */
    public function __construct(?string $external_id = null, ?string $identifier = null) {
        $this->externalId = $external_id;
        $this->identifier = $identifier;
    }

}
