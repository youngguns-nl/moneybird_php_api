<?php

/*
 * IncomingInvoice_History class file
 */

namespace Moneybird\IncomingInvoice;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;

/**
 * IncomingInvoice_History
 */
class History extends AbstractModel implements Mapable {
	
	protected $action; 
	protected $createdAt;
	protected $description;
	protected $id; 
	protected $incomingInvoiceId;
	protected $updatedAt; 
	protected $userId;
	
	protected $_readonlyAttr = array(
		'createdAt',
		'id', 
		'incomingInvoiceId',
		'updatedAt', 
		'userId',
	);
}
