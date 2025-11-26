<?php

namespace Espo\Modules\Crm\Classes\RecordHooks\Account;

use Espo\Core\Record\Hook\SaveHook;
use Espo\Modules\Crm\Entities\Account;
use Espo\ORM\Entity;
use Espo\ORM\EntityManager;

class BeforeUpdatePopulateDescription implements SaveHook
{
    public function __construct(private EntityManager $entityManager)
    {}

    public function process(Entity $entity): void
    {
        $repository = $this->entityManager->getRDBRepositoryByClass(Account::class);

        $collection = $repository
            ->getRelation($entity, 'contacts')
            ->order('name')
            ->find();

        $lines = [];

        foreach ($collection as $contact) {
            $name = $contact->get('name') ?? '';
            $role = $contact->get('contactRole') ?? '';
            $lines[] = trim($name . ($role !== '' ? ' - ' . $role : ''));
        }

        $entity->set('description', implode("\n", $lines));
    }
}