<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService\Validator;

use PolderKnowledge\EntityService\ServiceResult;

/**
 * The EntityNotExists validator will validate a certain entity was found.
 * Validation will fail when the entity is present
 */
class EntityNotExists extends AbstractEntityValidator
{
    /**
     * Error constants
     */
    const ERROR_OBJECT_FOUND = 'objectFound';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        self::ERROR_OBJECT_FOUND => "Object matching '%value%' was found",
    );

    /**
     * Returns true when $this->entityService returns an empty result
     * when $this->method is called with the given criteria
     *
     * @param mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $result = $this->fetchResult($value);

        if ($result instanceof ServiceResult) {
            if ($result->count() === 0) {
                return true;
            }
        }

        $this->error(self::ERROR_OBJECT_FOUND, $value);

        return false;
    }

}
