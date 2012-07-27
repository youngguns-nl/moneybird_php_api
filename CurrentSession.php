<?php

/*
 * CurrentSession class file
 */

namespace Moneybird;

/**
 * CurrentSession
 */
class CurrentSession 
	extends 
		Domainmodel_Abstract 
	implements 
		Mapper_Mapable {
	
	protected $email; 
	protected $language; 
	protected $name; 
	protected $timeZone; 
	
	protected $_readonlyAttr = array(
		'email',
		'language',
		'name', 
		'timeZone',
	);
			
}
