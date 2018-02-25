<?php
namespace PackageFactory\AtomicFusion\Mapping\Domain\Model;

use Neos\Flow\Annotations as Flow;
use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Eel\ProtectedContextAwareInterface;

class NodeMapping implements ProtectedContextAwareInterface, \ArrayAccess
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var NodeInterface
     */
    protected $node;

    public function __construct(array $data, NodeInterface $node)
    {
        $this->data = $data;
        $this->node = $node;
    }

    /**
     * @return NodeInterface
     */
    public function getNode() {
        return $this->node;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        unset($this->data[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed|null
     */
    public function offsetGet($offset) {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }

    /**
     * @param string $methodName
     * @return boolean
     */
    public function allowsCallOfMethod($methodName) {
        if ($methodName == 'getNode') {
            return true;
        }
        return false;
    }
}