<?php

/**
 * Exceptions for Moneybird
 *
 */
class MoneybirdException extends Exception
{
}

/**
 * Exception Authorization required (No authorization information provided with request)
 *
 */
class MoneybirdAuthorizationRequiredException extends MoneybirdException
{
}

/**
 * Exception Not accepted (The action you are trying to perform is not available in the API)
 *
 */
class MoneybirdNotAcceptedException extends MoneybirdException
{
}

/**
 * Exception Unprocessable entity (Entity was not created because of errors in parameters. Errors are included in XML response.)
 *
 */
class MoneybirdUnprocessableEntityException extends MoneybirdException
{
}

/**
 * Exception Internal server error (Something went wrong while processing the request. MoneyBird is notified of the error.)
 *
 */
class MoneybirdInternalServerErrorException extends MoneybirdException
{
}

/**
 * Exception The entity or action is not found in the API
 *
 */
class MoneybirdItemNotFoundException extends MoneybirdException
{
}

/**
 * Exception Unkown reponse
 *
 */
class MoneybirdUnknownResponseException extends MoneybirdException
{
}

/**
 * Exception Unkown filter
 *
 */
class MoneybirdUnknownFilterException extends MoneybirdException
{
}

/**
 * Exception Unknown send method
 *
 */
class MoneybirdUnknownSendMethodException extends MoneybirdException
{
}

/**
 * Exception Invalid id
 *
 */
class MoneybirdInvalidIdException extends MoneybirdException
{
}

/**
 * Getting or setting an unknown property
 *
 */
class MoneybirdUnknownPropertyException extends MoneybirdException
{
}

/**
 * Exception Connection error (probably cURL to blame)
 *
 */
class MoneybirdConnectionErrorException extends MoneybirdException
{
}

/**
 * Exception Unknown state of invoice
 *
 */
class MoneybirdUnknownInvoiceStateException extends MoneybirdException
{
}

/**
 * Exception Invalid type of moneybird object
 *
 */
class MoneybirdUnknownTypeException extends MoneybirdException
{
}

/**
 * Exception Invalid request
 *
 */
class MoneybirdInvalidRequestException extends MoneybirdException
{
}