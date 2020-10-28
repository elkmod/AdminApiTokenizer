<?php declare(strict_types=1);

namespace AdminApiTokenizer\Tokenizer\Pipelines\Customer;

use AdminApiTokenizer\Tokenizer\Pipelines\TokenizerInterface;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressCollection;
use Shopware\Core\Checkout\Customer\Aggregate\CustomerAddress\CustomerAddressEntity;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class CustomerTokenizer implements TokenizerInterface
{
    const ENTITY = 'customer';

    /**
     * @var AddressTokenizer
     */
    private $addressTokenizer;

    public function __construct(AddressTokenizer $addressTokenizer)
    {
        $this->addressTokenizer = $addressTokenizer;
    }

    public function supports(string $apiAlias): bool
    {
        return $apiAlias === self::ENTITY;
    }

    public function protect(Entity $customer): Entity
    {
        /** @var $customer CustomerEntity */
        $customer->setLastName(' ');
        $customer->setEmail('(protected)');

        if($customer->getAddresses() !== null) {

            $customer->setAddresses(new CustomerAddressCollection($customer->getAddresses()->map(function(CustomerAddressEntity $address) {
                return $this->addressTokenizer->protect($address);
            })));
        }

        return $customer;
    }
}
