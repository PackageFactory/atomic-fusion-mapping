<?php
namespace PackageFactory\AtomicFusion\Mapping\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\AbstractFusionObject;
use Neos\Utility\ObjectAccess;
use Neos\Fusion\FusionObjects\RawArrayImplementation;
use PackageFactory\AtomicFusion\Mapping\Domain\Model\NodeMapping;

/**
 * Render a raw collection that repreents mapped node data
 */
class NodeMappingImplementation extends RawArrayImplementation
{

    /**
     * @var array
     */
    protected $ignoreProperties = ['node'];

    /**
     * @return mixed
     */
    public function getNode()
    {
        return $this->fusionValue('node');
    }

    /**
     *
     *
     * @return array
     */
    public function evaluate()
    {
        $node = $this->getNode();
        $data = parent::evaluate();
        $nodeMapping =  new NodeMapping($data, $node);
        return $nodeMapping;
    }
}
