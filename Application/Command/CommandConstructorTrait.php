<?php
namespace Dende\Calendar\Application\Command;

/**
 * Class CommandConstructorTrait.
 */
trait CommandConstructorTrait
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $availableVars = get_object_vars($this);

        foreach ($params as $variable => $value) {
            if (array_key_exists($variable, $availableVars)) {
                $this->$variable = $value;
            }
        }
    }
}
