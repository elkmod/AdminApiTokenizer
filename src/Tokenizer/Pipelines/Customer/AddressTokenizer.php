<?php

namespace AdminApiTokenizer\Tokenizer\Pipelines\Customer;

use AdminApiTokenizer\Tokenizer\Pipelines\TokenizerInterface;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class AddressTokenizer implements TokenizerInterface
{
    private const ENTITIES = ['customer_address', 'order_address'];

    public function supports(string $apiAlias): bool
    {
        return in_array($apiAlias, self::ENTITIES);
    }

    public function protect(Entity $entity): Entity
    {
        /** @var $entity CustomerAddressEntity|OrderAddressEntity */
        $entity->setStreet('(protected)');
        $entity->setFirstName(' ');
        $entity->setLastName(' ');
        $entity->setZipcode(' ');
        $entity->setCity(' ');

        return $entity;
    }
}
