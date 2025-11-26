<?php

namespace Espo\Modules\Crm\Classes\Select\Contact\BoolFilters;

use Espo\Core\Select\Bool\Filter;
use Espo\ORM\Query\Part\Where\OrGroupBuilder;
use Espo\ORM\Query\Part\Condition as Cond;
use Espo\ORM\Query\SelectBuilder;

class HasPhoneNumber implements Filter
{
    public function apply(SelectBuilder $queryBuilder, OrGroupBuilder $orGroupBuilder): void
    {
        $queryBuilder->leftJoin('phoneNumbers', 'phoneNumbersFilter', ['primary' => true]);

        $orGroupBuilder->add(
            Cond::notEqual(
                Cond::column('phoneNumbersFilter.name'),
                null
            )
        );
    }
}