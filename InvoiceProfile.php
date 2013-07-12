<?php

/*
 * InvoiceProfile class file
 */

namespace Moneybird;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;

/**
 * InvoiceProfile
 */
class InvoiceProfile extends AbstractModel implements Mapable {
	
	protected $id;
	protected $name;
	
	protected $_readonlyAttr = array(
		'id',
		'name',
	);
}