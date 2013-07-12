<?php

/*
 * Estimate_History class file
 */

namespace Moneybird\Estimate;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;

/**
 * Estimate_History
 */
class History extends AbstractModel implements Mapable {
	
	protected $action; 
	protected $createdAt;
	protected $description;
	protected $id; 
	protected $updatedAt; 
	protected $userId;
	
	protected $_readonlyAttr = array(
		'createdAt',
		'id', 
		'updatedAt', 
		'userId',
	);
}
