<?php

/*
 * Interface for service classes that store Domainmodels
 */
namespace Moneybird;

/**
 * Domainmodel Service
 */
interface Service
{

    /**
     * Constructor
     * @param ApiConnector $connector 
     */
    public function __construct(ApiConnector $connector);
}
