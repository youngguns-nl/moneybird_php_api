<?php

/*
 * RecurringTemplate service class
 */

namespace Moneybird\RecurringTemplate;

use Moneybird\Service as ServiceInterface;
use Moneybird\ApiConnector;
use Moneybird\RecurringTemplate;
use Moneybird\InvalidFilterException;

/**
 * RecurringTemplate service
 */
class Service implements ServiceInterface {
	
	/**
	 * ApiConnector object
	 * @var ApiConnector
	 */
	protected $connector;
	
	/**
	 * Constructor
	 * @param ApiConnector $connector 
	 */
	public function __construct(ApiConnector $connector) {
		$this->connector = $connector;
	}
	
	/**
	 * Get template by id
	 * @param int $id
	 * @return RecurringTemplate
	 */
	public function getById($id) {
		return $this->connector->getById(__NAMESPACE__, $id);
	}
	
	/**
	 * Get all templates
	 * 
	 * @param string $filter
	 * @param Subject $parent
	 * @return ArrayObject
	 * @throws InvalidFilterException 
	 */
	public function getAll($filter = null, Subject $parent = null) {
		return $this->connector->getAll(__NAMESPACE__, $filter, $parent);
	}
	
	/**
	 * Updates or inserts a template
	 * @param RecurringTemplate $template
	 * @return RecurringTemplate
	 */
	public function save(RecurringTemplate $template) {
		return $this->connector->save($template);
	}
	
	/**
	 * Deletes a template
	 * @param RecurringTemplate $template
	 * @return self
	 */
	public function delete(RecurringTemplate $template) {
		$this->connector->delete($template);
		return $this;
	}
	
}