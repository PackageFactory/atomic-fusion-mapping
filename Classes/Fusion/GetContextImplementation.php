<?php
namespace PackageFactory\AtomicFusion\Mapping\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Neos\Utility\ObjectAccess;

/**
 * Fusion object to access values from current context by name
 */
class GetContextImplementation extends AbstractFusionObject
{
    /**
     * @return mixed
     */
    public function getProperty()
    {
        return $this->fusionValue('property');
    }

    /**
     * Just return the context property
     *
     * @return mixed
     */
    public function evaluate()
    {
        if ($property = $this->getProperty()) {
            return ObjectAccess::getProperty( $this->runtime->getCurrentContext(), $property);
        } else {
            return null;
        }
    }
}
