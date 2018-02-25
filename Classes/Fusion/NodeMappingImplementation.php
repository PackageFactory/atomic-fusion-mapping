<?php
namespace PackageFactory\AtomicFusion\Mapping\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\Exception as FusionException;
use Neos\ContentRepository\Domain\Model\NodeInterface;
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
     * Render data as RawArray but store a wrapper object with additional
     * node-reference for later automatically applying contentELementWrapping
     * during rendering of `Neos.Fusion:Collection`
     *
     * @throws \Neos\Fusion\Exception
     * @return array
     */
    public function evaluate()
    {
        $node = $this->getNode();
        if (!($node && $node instanceof NodeInterface)) {
            throw new FusionException(sprintf('The node-attribute is required and must be of type "NodeInterface" "%s" given.', is_object($node) ? get_class($node) : gettype($node)));
        }
        $data = parent::evaluate();
        $nodeMapping = new NodeMapping($data, $node);
        return $nodeMapping;
    }
}
