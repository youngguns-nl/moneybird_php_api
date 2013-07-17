<?php

/*
 * Estimate service class
 */
namespace Moneybird\Estimate;

use Moneybird\Service as ServiceInterface;
use Moneybird\ApiConnector;
use Moneybird\InvalidFilterException;
use Moneybird\Estimate;

/**
 * Estimate service
 */
class Service implements ServiceInterface
{

    /**
     * ApiConnector object
     * @var ApiConnector
     */
    protected $connector;

    /**
     * Constructor
     * @param ApiConnector $connector
     */
    public function __construct(ApiConnector $connector)
    {
        $this->connector = $connector;
    }

    /**
     * Get estimate by id
     * @param int $id
     * @return Estimate
     */
    public function getById($id)
    {
        return $this->connector->getById(__NAMESPACE__, $id);
    }

    /**
     * Get all estimates
     *
     * @param string|integer $filter Filter name or id (advanced filters)
     * @param Subject $parent
     * @return ArrayObject
     * @throws InvalidFilterException
     */
    public function getAll($filter = null, Subject $parent = null)
    {
        return $this->connector->getAll(__NAMESPACE__, $filter, $parent);
    }

    /**
     * Updates or inserts an estimate
     * @param Estimate $estimate
     * @return Estimate
     */
    public function save(Estimate $estimate)
    {
        return $this->connector->save($estimate);
    }

    /**
     * Deletes an estimate
     * @param Estimate $estimate
     * @return self
     */
    public function delete(Estimate $estimate)
    {
        $this->connector->delete($estimate);
        return $this;
    }

    /**
     * Send the estimate. Returns the updated estimate
     * @param Estimate $estimate
     * @param string $method Send method (email|hand|post); default: email
     * @param type $email Address to send to; default: contact e-mail
     * @param type $message
     * @return Estimate 
     */
    public function send(Estimate $estimate, $method = 'email', $email = null, $message = null)
    {
        return $this->connector->send($estimate, $this->buildEnvelope($method, $email, $message));
    }

    /**
     * Build an envelope to send the estimate or reminder with
     * @param string $method Send method (email|hand|post); default: email
     * @param type $email Address to send to; default: contact e-mail
     * @param type $message
     * @return Envelope
     * @access protected
     */
    protected function buildEnvelope($method = 'email', $email = null, $message = null)
    {
        return new Envelope(
                array(
                    'sendMethod' => $method,
                    'email' => $email,
                    'estimateEmail' => $message,
                )
        );
    }

    /**
     * Get the raw PDF content
     * @param Estimate $estimate
     * @return string
     */
    public function getPdf(Estimate $estimate)
    {
        return $this->connector->getPdf($estimate);
    }
}