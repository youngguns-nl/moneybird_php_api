<?php

/*
 * SimpleXMLElement class file
 */

namespace Moneybird;

/**
 * Extension for SimpleXMLElement
 * @author Alexandre FERAUD
 */
class SimpleXMLElement extends \SimpleXMLElement {

	/**
	 * Add CDATA text in a node
	 * @param string $cdata_text The CDATA value  to add
	 */
	protected function addCData($cdata_text) {
		$node = dom_import_simplexml($this);
		$no = $node->ownerDocument;
		$node->appendChild($no->createCDATASection($cdata_text));
	}

	/**
	 * Create a child with CDATA value
	 * @param string $name The name of the child element to add.
	 * @param string $cdata_text The CDATA value of the child element.
	 */
	public function addChildCData($name, $cdata_text) {
		$child = $this->addChild($name);
		$child->addCData($cdata_text);
	}
	
	/**
	 * Adds a child element to the XML node
	 * @param string $name The name of the child element to add.
	 * @param string $value [optional] If specified, the value of the child element.
	 * @param string $namespace [optional] If specified, the namespace to which the child element belongs.
	 * @return SimpleXMLElement The addChild method returns a SimpleXMLElement object representing the child added to the XML node.
	 */
	public function addChild ($key, $value = null, $namespace = null) {
		return parent::addChild($key, str_replace('&', '&amp;', $value), $namespace);
    } 

	/**
	 * Add SimpleXMLElement code into a SimpleXMLElement
	 * @param SimpleXMLElement $append
	 */
	public function appendXML(SimpleXMLElement $append) {
		if (strlen(trim((string) $append)) == 0) {
			$xml = $this->addChild($append->getName());
			foreach ($append->children() as $child) {
				$xml->appendXML($child);
			}
		} else {
			$xml = $this->addChild($append->getName(), (string) $append);
		}
		foreach ($append->attributes() as $n => $v) {
			$xml->addAttribute($n, $v);
		}
	}

}

