<?php

/*
 * CurrentSession class file
 */
namespace Moneybird;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;

/**
 * CurrentSession
 */
class CurrentSession extends AbstractModel implements Mapable
{

    protected $email;
    protected $language;
    protected $name;
    protected $timeZone;
    protected $_readonlyAttr = array(
        'email',
        'language',
        'name',
        'timeZone',
    );
}
