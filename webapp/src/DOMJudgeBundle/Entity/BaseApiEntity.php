<?php declare(strict_types=1);

namespace DOMJudgeBundle\Entity;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use DOMJudgeBundle\Service\EventLogService;
use Exception;

/**
 * Class BaseApiEntity
 *
 * Base entity class that entities should use to support getting their API ID.
 *
 * @package DOMJudgeBundle\Entity
 */
abstract class BaseApiEntity
{
    /**
     * Get the API ID field name for this entity
     * @param EventLogService $eventLogService
     * @return string
     * @throws Exception
     */
    public function getApiIdField(EventLogService $eventLogService)
    {
        return $eventLogService->apiIdFieldForEntity($this);
    }

    /**
     * Get the API ID for this entity
     * @param EventLogService $eventLogService
     * @return mixed
     * @throws Exception
     */
    public function getApiId(EventLogService $eventLogService)
    {
        $field = $eventLogService->apiIdFieldForEntity($this);
        $method = 'get'.ucfirst($field);
        return $this->{$method}();
    }
}
