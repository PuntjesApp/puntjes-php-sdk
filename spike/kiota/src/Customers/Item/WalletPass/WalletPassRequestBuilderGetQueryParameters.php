<?php

namespace Puntjes\Spike\Kiota\Customers\Item\WalletPass;

class WalletPassRequestBuilderGetQueryParameters 
{
    /**
     * @var GetPlatformQueryParameterType|null $platform 
    */
    public ?GetPlatformQueryParameterType $platform = null;
    
    /**
     * Instantiates a new WalletPassRequestBuilderGetQueryParameters and sets the default values.
     * @param GetPlatformQueryParameterType|null $platform 
    */
    public function __construct(?GetPlatformQueryParameterType $platform = null) {
        $this->platform = $platform;
    }

}
