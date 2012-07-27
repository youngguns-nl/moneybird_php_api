<?php

/*
 * Estimate_History class file
 */

namespace Moneybird;

/**
 * Estimate_History
 */
class Estimate_History extends Domainmodel_Abstract implements Mapper_Mapable {
	
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
