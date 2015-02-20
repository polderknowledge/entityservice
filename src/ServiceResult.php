<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use PolderKnowledge\EntityService\Exception\InvalidArgumentException;
use PolderKnowledge\EntityService\Exception\RuntimeException;
use PolderKnowledge\EntityService\ServiceResult;

/**
 * The ServiceResult class contains the result of a request to the entity service manager.
 */
class ServiceResult implements Countable, Iterator
{
    /**
     * The amount of entries that this result contains.
     *
     * @var null|int
     */
    protected $count = null;

    /**
     * The data source with entries.
     *
     * @var Iterator
     */
    protected $dataSource = null;

    /**
     * The amount of fields that this result has.
     *
     * @var int
     */
    protected $fieldCount = null;

    /**
     * The position that the iterator is at.
     *
     * @var int
     */
    protected $position = 0;

    /**
     * Initializes the ServiceResult with data from the given data source.
     *
     * @param array|IteratorAggregate|Iterator $dataSource The datasource to use data from.
     * @return ServiceResult Returns this instance for chaining.
     * @throws InvalidArgumentException Throws an instance of InvalidArgumentException when the data source is invalid.
     */
    public function initialize($dataSource)
    {
        $this->position = 0;

        if (is_array($dataSource)) {
            $dataSource = array_filter($dataSource);

            $first = current($dataSource);
            reset($dataSource);

            $this->count = count($dataSource);
            $this->fieldCount = $first === false ? 0 : count($first);
            $this->dataSource = new ArrayIterator($dataSource);
        } elseif ($dataSource instanceof IteratorAggregate) {
            $this->dataSource = $dataSource->getIterator();
        } elseif ($dataSource instanceof Iterator) {
            $this->dataSource = $dataSource;
        } else {
            throw new InvalidArgumentException(sprintf(
                'DataSource provided is not an array, ' .
                'nor does it implement Iterator or IteratorAggregate, got %s',
                (is_object($dataSource)) ? get_class($dataSource) : gettype($dataSource)
            ));
        }

        return $this;
    }

    /**
     * Get the data source used to create the result set.
     *
     * @return null|array|IteratorAggregate|Iterator
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Gets the amount of fields in individual rows of the result set.
     *
     * @return int
     */
    public function getFieldCount()
    {
        if (null !== $this->fieldCount) {
            return $this->fieldCount;
        }

        $dataSource = $this->getDataSource();
        if (null === $dataSource) {
            return 0;
        }

        $dataSource->rewind();
        if (!$dataSource->valid()) {
            $this->fieldCount = 0;
            return 0;
        }

        $row = $dataSource->current();
        if (is_object($row) && $row instanceof Countable) {
            $this->fieldCount = $row->count();
            return $this->fieldCount;
        }

        $row = (array)$row;
        $this->fieldCount = count($row);
        return $this->fieldCount;
    }

    /**
     * Casts this ServiceResult to an array of arrays.
     *
     * @return array
     * @throws RuntimeException Throws an instance of RuntimeException when a row is not castable to an array.
     */
    public function toArray()
    {
        $return = array();
        foreach ($this as $row) {
            if (is_array($row)) {
                $return[] = $row;
            } elseif (method_exists($row, 'toArray')) {
                $return[] = $row->toArray();
            } elseif (method_exists($row, 'getArrayCopy')) {
                $return[] = $row->getArrayCopy();
            } else {
                throw new RuntimeException(sprintf(
                    'Rows as part of this DataSource, ' .
                    'with type %s cannot be cast to an array',
                    gettype($row)
                ));
            }
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null
     */
    public function count()
    {
        if (null === $this->count) {
            $this->count = count($this->dataSource);
        }

        return $this->count;
    }

    /**
     * {@inheritdoc}
     * @return mixed
     */
    public function current()
    {
        return $this->dataSource->current();
    }

    /**
     * {@inheritdoc}
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->dataSource->next();
        $this->position++;
    }

    /**
     * A helper method to go to the previous enty in this result.
     *
     * @return void
     */
    public function prev()
    {
        $newPos = $this->key() - 1;
        $this->rewind();

        if ($newPos === -1) {
            $this->position = $newPos;
        } else {
            while ($newPos > $this->key()) {
                $this->next();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->dataSource->rewind();
        $this->position = 0;
    }

    /**
     * {@inheritdoc}
     * @return bool
     */
    public function valid()
    {
        return $this->position >= 0 && $this->dataSource->valid();
    }
}
