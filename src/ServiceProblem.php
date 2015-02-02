<?php
/**
 * Polder Knowledge / Entity Service (http://polderknowledge.nl)
 *
 * @link http://developers.polderknowledge.nl/gitlab/polderknowledge/entityservice for the canonical source repository
 * @copyright Copyright (c) 2015-2015 Polder Knowledge (http://www.polderknowledge.nl)
 * @license http://polderknowledge.nl/license/proprietary proprietary
 */

namespace PolderKnowledge\EntityService;

/**
 * The ServiceProblem class represents a problem
 */
class ServiceProblem
{
    /**
     * A code that describes this problem.
     *
     * @var integer
     */
    protected $code;

    /**
     * A messages that describes this problem.
     *
     * @var string
     */
    protected $message;

    /**
     * Initializes a new instance of this class.
     *
     * @param string $message A messages that describes this problem.
     * @param integer $code A code that describes this problem.
     */
    public function __construct($message, $code = 1)
    {
        $this->code = (int)$code;
        $this->message = (string)$message;
    }

    /**
     * Gets the code that describes this problem.
     *
     * @return integer Returns an integer containing the code.
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Gets the messages that describes this problem.
     *
     * @return string Returns a string containing the message.
     */
    public function getMessage()
    {
        return $this->message;
    }
}
