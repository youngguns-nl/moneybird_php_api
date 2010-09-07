<?php

/**
 * Interface for MoneybirdCompany
 *
 */
interface iMoneybirdCompany extends iMoneybirdObject
{
	/**
	 * Set a reference to the Api
	 *
	 * @param MoneybirdApi $api
	 * @access public
	 */
	public function setApi(MoneybirdApi $api);
}

/**
 * Contact in Moneybird
 *
 */
class MoneybirdCompany extends MoneybirdObject implements iMoneybirdCompany
{
	/**
	 * Api object
	 *
	 * @access private
	 * @var MoneybirdApi
	 */
	protected $api;

	/**
	 * Set a reference to the Api
	 *
	 * @param MoneybirdApi $api
	 * @access public
	 */
	public function setApi(MoneybirdApi $api)
	{
		$this->api = $api;
	}

	/**
	 * Save company
	 *
	 * @return MoneybirdCompany
	 * @access public
	 */
	public function save()
	{
		return $this->api->saveSettings($this);
	}
}