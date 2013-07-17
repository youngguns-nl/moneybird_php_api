<?php

/*
 * Inteface for service classes that store Domeinmodels
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
