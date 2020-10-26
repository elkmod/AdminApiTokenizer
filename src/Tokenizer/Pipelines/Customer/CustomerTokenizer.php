<?php declare(strict_types=1);

namespace AdminApiTokenizer\Tokenizer\Pipelines\Customer;

use AdminApiTokenizer\Tokenizer\Pipelines\TokenizerInterface;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class CustomerTokenizer implements TokenizerInterface
{
    const ENTITY = 'customer';

    public function supports(string $apiAlias): bool
    {
        return $apiAlias === self::ENTITY;
    }

    public function protect(Entity $customer): Entity
    {
        $customer->setFirstName(' ');
        $customer->setLastName('(name protected)');
        $customer->setEmail('(protected)');

        $customer->setAddresses(new CustomerAddressCollection($customer->getAddresses()->map(function(CustomerAddressEntity $address) {
            $address->setStreet('(protected)');
            $address->setFirstName(' ');
            $address->setLastName(' ');

            return $address;
        })));

        return $customer;
    }
}
