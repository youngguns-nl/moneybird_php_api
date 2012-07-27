<?php

/*
 * IncomingInvoice_History class file
 */

namespace Moneybird;

/**
 * IncomingInvoice_History
 */
class IncomingInvoice_History extends Domainmodel_Abstract implements Mapper_Mapable {
	
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
