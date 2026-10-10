<?php

namespace Puntjes\Spike\Kiota\Campaigns;

class CampaignsRequestBuilderGetQueryParameters 
{
    /**
     * @var string|null $branch 
    */
    public ?string $branch = null;
    
    /**
     * @var int|null $page 
    */
    public ?int $page = null;
    
    /**
     * Instantiates a new CampaignsRequestBuilderGetQueryParameters and sets the default values.
     * @param string|null $branch 
     * @param int|null $page 
    */
    public function __construct(?string $branch = null, ?int $page = null) {
        $this->branch = $branch;
        $this->page = $page;
    }

}
