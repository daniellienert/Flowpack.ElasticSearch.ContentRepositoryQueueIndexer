<?php
namespace Flowpack\ElasticSearch\ContentRepositoryQueueIndexer\Domain\Service;

use Flowpack\ElasticSearch\ContentRepositoryAdaptor\Exception;
use Neos\ContentRepository\Domain\Model\NodeData;
use Neos\ContentRepository\Domain\Repository\WorkspaceRepository;
use Neos\ContentRepository\Domain\Service\NodeTypeManager;
use Neos\ContentRepository\Exception\NodeTypeNotFoundException;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("singleton")
 */
class FakeNodeDataFactory
{
    /**
     * @var WorkspaceRepository
     * @Flow\Inject
     */
    protected $workspaceRepository;

    /**
     * @var NodeTypeManager
     * @Flow\Inject
     */
    protected $nodeTypeManager;

    /**
     * Thie creates a NodeData instance from the given payload
     *
     * @param array $payload
     * @return NodeData
     * @throws Exception
     */
    public function createFromPayload(array $payload)
    {
        if (!isset($payload['workspace']) || empty($payload['workspace'])) {
            throw new Exception('Unable to create fake node data, missing workspace value', 1508448007);
        }
        if (!isset($payload['path']) || empty($payload['path'])) {
            throw new Exception('Unable to create fake node data, missing path value', 1508448008);
        }
        if (!isset($payload['nodeIdentifier']) || empty($payload['nodeIdentifier'])) {
            throw new Exception('Unable to create fake node data, missing identifier value', 1508448009);
        }
        if (!isset($payload['nodeType']) || empty($payload['nodeType'])) {
            throw new Exception('Unable to create fake node data, missing nodeType value', 1508448011);
        }

        $workspace = $this->workspaceRepository->findOneByName($payload['workspace']);
        if ($workspace === null) {
            throw new Exception('Unable to create fake node data, workspace not found', 1508448028);
        }

        $nodeData = new NodeData($payload['path'], $workspace, $payload['nodeIdentifier'], isset($payload['dimensions']) ? $payload['dimensions'] : null);
        try {
            $nodeData->setNodeType($this->nodeTypeManager->getNodeType($payload['nodeType']));
        } catch (NodeTypeNotFoundException $e) {
            throw new Exception('Unable to create fake node data, node type not found', 1509362172);
        }

        $nodeData->setProperty('title', 'Fake node');
        $nodeData->setProperty('uriPathSegment', 'fake-node');

        $nodeData->setRemoved(true);

        return $nodeData;
    }
}
