<?php

namespace Puntjes\Spike\Kiota\Rewards;

class RewardsRequestBuilderGetQueryParameters 
{
    /**
     * @var bool|null $affordable 
    */
    public ?bool $affordable = null;
    
    /**
     * @var string|null $identifier 
    */
    public ?string $identifier = null;
    
    /**
     * Instantiates a new RewardsRequestBuilderGetQueryParameters and sets the default values.
     * @param bool|null $affordable 
     * @param string|null $identifier 
    */
    public function __construct(?bool $affordable = null, ?string $identifier = null) {
        $this->affordable = $affordable;
        $this->identifier = $identifier;
    }

}
