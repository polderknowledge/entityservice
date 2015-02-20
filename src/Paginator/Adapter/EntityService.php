<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Paginator\Adapter;

use Doctrine\Common\Collections\Criteria;
use PolderKnowledge\EntityService\Service\EntityServiceInterface;
use Zend\Paginator\Adapter\AdapterInterface;

/**
 * Paginator adapter for EntityServices
 */
class EntityService implements AdapterInterface
{
    /**
     * EntityService used to fetch adapter
     *
     * @var EntityServiceInterface
     */
    protected $entityService;

    /**
     * Criteria used to fetch entities
     *
     * @var array|Criteria
     */
    protected $criteria;

    /**
     * Order used to fetch criteria
     *
     * @var array
     */
    protected $order;

    /**
     * @param EntityServiceInterface $entityService
     * @param array|Criteria $criteria
     * @param array $order
     */
    public function __construct(EntityServiceInterface $entityService, $criteria = array(), array $order = null)
    {
        $this->entityService = $entityService;
        $this->criteria = $criteria;
        $this->order = $order;
    }

    /**
     * {@inhericDoc}
     *
     * @return int
     */
    public function count()
    {
        $result = $this->entityService->countBy(
            is_object($this->criteria) ? clone $this->criteria : $this->criteria
        );

        return (int) $result->current();
    }

    /**
     * {@inhericDoc}
     *
     * @param  int $offset Page offset
     * @param  int $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        return $this->entityService->findBy(
            is_object($this->criteria) ? clone $this->criteria : $this->criteria,
            $this->order,
            $itemCountPerPage,
            $offset
        );
    }
}
