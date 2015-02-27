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
 * The EntityExists validator will validate a certain entity was found. Validation will
 * fail when the entity is not present
 */
class EntityExists extends AbstractEntityValidator
{
    /**
     * Error constants
     */
    const ERROR_NO_OBJECT_FOUND = 'noObjectFound';

    /**
     * @var array Message templates
     */
    protected $messageTemplates = array(
        self::ERROR_NO_OBJECT_FOUND => "No object matching '%value%' was found",
    );

    /**
     * Returns true when $this->entityService returns an entity
     * when $this->method is called with the given criteria
     *
     * @param  mixed $value
     * @return boolean
     */
    public function isValid($value)
    {
        $result = $this->fetchResult($value);

        if ($result instanceof ServiceResult) {
            if ($result->count() === 1) {
                return true;
            }
        }

        $this->error(self::ERROR_NO_OBJECT_FOUND, $value);

        return false;
    }
}
