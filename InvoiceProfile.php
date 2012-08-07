<?php

/*
 * InvoiceProfile class file
 */

namespace Moneybird;

/**
 * InvoiceProfile
 */
class InvoiceProfile extends Domainmodel_Abstract implements Mapper_Mapable {
	
	protected $id;
	protected $name;
	
	protected $_readonlyAttr = array(
		'id',
		'name',
	);
}