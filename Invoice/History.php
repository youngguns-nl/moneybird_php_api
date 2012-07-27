<?php

/*
 * Invoice_History class file
 */

namespace Moneybird;

/**
 * Invoice_History
 */
class Invoice_History extends Domainmodel_Abstract implements Mapper_Mapable {
	
	protected $action; 
	protected $createdAt;
	protected $description;
	protected $id; 
	protected $invoiceId;
	protected $updatedAt; 
	protected $userId;
	
	protected $_readonlyAttr = array(
		'createdAt',
		'id', 
		'invoiceId',
		'updatedAt', 
		'userId',
	);
}
