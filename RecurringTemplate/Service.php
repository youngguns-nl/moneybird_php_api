<?php

/*
 * RecurringTemplate service class
 */

namespace Moneybird;

/**
 * RecurringTemplate service
 */
class RecurringTemplate_Service implements Service {
	
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
		return $this->connector->getById('RecurringTemplate', $id);
	}
	
	/**
	 * Get all templates
	 * 
	 * @param string $filter
	 * @param RecurringTemplate_Subject $parent
	 * @return RecurringTemplate_Array
	 * @throws InvalidFilterException 
	 */
	public function getAll($filter = null, RecurringTemplate_Subject $parent = null) {
		return $this->connector->getAll('RecurringTemplate', $filter, $parent);
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