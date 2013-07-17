<?php

/*
 * Error class file
 */
namespace Moneybird;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;

/**
 * Error
 */
class Error extends AbstractModel implements Mapable
{

    protected $attribute;
    protected $message;

    /**
     * String representation of object
     * @return string
     */
    public function __toString()
    {
        return $this->attribute . ': ' . $this->message;
    }
}
