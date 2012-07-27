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
	
	public function save(ApiConnector $service) {
		throw new Exception('Cannot save InvoiceProfile');
	}
	
	public function delete(ApiConnector $service) {
		throw new Exception('Cannot delete InvoiceProfile');
	}
}