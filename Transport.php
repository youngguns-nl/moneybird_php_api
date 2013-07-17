<?php

/*
 * Transport interface file
 */
namespace Moneybird;

/**
 * Transport interface
 */
interface Transport
{

    /**
     * Perform the request
     *
     * @param string $url URL of request
     * @param string $requestMethod (GET|POST|PUT|DELETE)
     * @param string $data Data in string format
     * @param array $headers
     * @return string
     * @throws HttpRequest\Exception
     * @throws HttpRequest\HttpStatusException
     * @throws HttpRequest\UnknownHttpStatusException
     * @throws HttpRequest\ConnectionErrorException
     * @access public
     */
    public function send($url, $requestMethod, $data = null, Array $headers = null);

    /**
     * Number of requests left
     * @return int
     */
    public function requestsLeft();

    /**
     * Get last response
     *
     * @return string
     * @access public
     */
    public function getLastResponse();

    /**
     * Set useragent
     * @param string $userAgent
     * @access public
     * @return HttpClient
     */
    public function setUserAgent($userAgent);
}