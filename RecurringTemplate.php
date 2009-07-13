<?php

/**
 * Interface for MoneybirdRecurringTemplate
 *
 */
interface iMoneybirdRecurringTemplate extends iMoneybirdObject
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
 * Interface for MoneybirdRecurringTemplateDetail
 *
 */
interface iMoneybirdRecurringTemplateDetail extends iMoneybirdObject
{
}

/**
 * RecurringTemplate in Moneybird
 *
 */
class MoneybirdRecurringTemplate extends MoneybirdObject implements iMoneybirdRecurringTemplate
{
	/**
	 * Api object
	 *
	 * @access private
	 * @var MoneybirdApi
	 */
	private $api;

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
	 * Load object from XML
	 *
	 * @access public
	 * @param SimpleXMLElement $xml
	 */
	public function fromXML(SimpleXMLElement $xml)
	{
		parent::fromXML($xml, array(
			'details'  => 'MoneybirdRecurringTemplateLine',
		));
	}

	/**
	 * Convert object to XML
	 *
	 * @access public
	 * @return string
	 */
	public function toXML()
	{
		return parent::toXML(
			array(
				'details' => 'details_attributes',
			),
			'<recurring-template>',
			'</recurring-template>'
		);
	}

	/**
	 * Save template
	 *
	 * @return MoneybirdRecurringTemplate
	 * @access public
	 */
	public function save()
	{
		return $this->api->saveRecurringTemplate($this);
	}

	/**
	 * Delete template
	 *
	 * @access public
	 */
	public function delete()
	{
		$this->api->deleteRecurringTemplate($this);
	}
}

/**
 * RecurringTemplateLine in Moneybird
 *
 */
class MoneybirdRecurringTemplateLine extends MoneybirdObject implements iMoneybirdRecurringTemplateDetail
{
	/**
	 * Convert to XML string
	 *
	 * @access public
	 * @return string
	 */
	public function toXML()
	{
		return parent::toXML(
			null,
			'<detail type="RecurringTemplateDetail">',
			'</detail>',
			array('total_price_excl_tax', 'total_price_incl_tax',)
		);
	}
}
