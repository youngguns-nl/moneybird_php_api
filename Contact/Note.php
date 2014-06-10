<?php

/*
 * Note class file
 */
namespace Moneybird\Contact;

use Moneybird\Domainmodel\AbstractModel;
use Moneybird\Mapper\Mapable;
use Moneybird\Storable;
use Moneybird\Service as ServiceInterface;
use Moneybird\Contact;
use Moneybird\Exception;
use Moneybird\NotValidException;

/**
 * Note
 */
class Note extends AbstractModel implements Mapable, Storable
{

    protected $createdAt;
    protected $id;
    protected $note;
    protected $_readonlyAttr = array(
        'createdAt',
        'id',
    );

    /**
     * Inserts note
     * @param ServiceInterface $service
     * @param Contact $contact
     * @return self
     * @throws NotValidException
     * @todo throw more specific exception on invalid parent
     */
    public function save(ServiceInterface $service, Contact $contact = null)
    {
        if (!$this->validate()) {
            throw new NotValidException('Unable to validate contact note');
        }

        if ($contact === null) {
            throw new Exception('$contact must be instance of Contact');
        }

        $newContact = $service->saveNote($this, $contact);
        $contact->setData($newContact->toArray(), false);
        return $this->reload(
            end($newContact->notes)
        );
    }

    /**
     * Deletes a contact note
     * @param Service $service
	 * @param Contact $contact
     */
    public function delete(ServiceInterface $service, Contact $contact = null)
    {
        if ($contact === null) {
            throw new Exception('$contact must be instance of Contact');
        }
        $service->deleteNote($this, $contact);
    }
}
