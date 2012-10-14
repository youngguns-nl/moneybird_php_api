<?php

/*
 * Interface for sendable objects
 */

namespace Moneybird;

/**
 * Sendable
 */
interface Sendable {
	/**
	 * Send the invoice or estimate
	 * @param Service $service
	 * @param string $method Send method (email|hand|post); default: email
	 * @param type $email Address to send to; default: contact e-mail
	 * @param type $message
	 * @return self 
	 */
	public function send(Service $service, $method='email', $email=null, $message=null);
}
