<?php

/**
 * Interface for MoneybirdCompany
 *
 */
interface iMoneybirdCompany extends iMoneybirdObject {

	/**
	 * Save company
	 *
	 * @return MoneybirdCompany
	 * @access public
	 */
	public function save();
}

/**
 * Contact in Moneybird
 *
 */
class MoneybirdCompany extends MoneybirdObject implements iMoneybirdCompany {

	/**
	 * Save company
	 *
	 * @return MoneybirdCompany
	 * @access public
	 */
	public function save() {
		return $this->api->saveSettings($this);
	}

}