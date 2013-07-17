<?php

/**
 * Disclosure is a class meant for communication between Domain Objects and the
 * views that are to display the values of those domain models.
 *
 * The rationale is that the Domain Object does not have to disclose any properties
 * it doesn't want to, and as such, no longer has need for accessors or mutators.
 * The disclosure should only be used in the view layer.
 */
namespace Moneybird;

/**
 * Disclosure
 */
class Disclosure
{

    protected $values;

    public function __construct($values)
    {
        $this->values = $values;
    }

    public function toArray()
    {
        return $this->values;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->values)) {
            return $this->values[$key];
        }
        throw new Exception('Property ' . $key . ' has not been disclosed');
    }

    public function __set($key, $value)
    {
        throw new Exception('A disclosure is read-only, don\'t try to write to it.');
    }

    public function __isset($key)
    {
        return array_key_exists($key, $this->values);
    }
}
