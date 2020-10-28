<?php declare(strict_types=1);

namespace AdminApiTokenizer\Tokenizer\Pipelines\Order;

use AdminApiTokenizer\Tokenizer\Pipelines\Customer\AddressTokenizer;
use AdminApiTokenizer\Tokenizer\Pipelines\TokenizerInterface;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressCollection;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderCustomer\OrderCustomerEntity;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class OrderTokenizer implements TokenizerInterface
{
    private const ENTITY = 'order';

    /** @var AddressTokenizer */
    private $addressTokenizer;

    public function __construct(AddressTokenizer $addressTokenizer)
    {
        $this->addressTokenizer = $addressTokenizer;
    }

    public function supports(string $apiAlias): bool
    {
        return $apiAlias === self::ENTITY;
    }

    public function protect(Entity $order): Entity
    {
        /** @var $order OrderEntity */
        $order->setAddresses(new OrderAddressCollection($order->getAddresses()->map(function(OrderAddressEntity $address) {
            return $this->addressTokenizer->protect($address);
        })));


        $order->setOrderCustomer($this->protectOrderCustomer($order->getOrderCustomer()));

        return $order;
    }

    private function protectOrderCustomer(OrderCustomerEntity $customer): OrderCustomerEntity
    {
        $customer->setFirstName(' ');
        $customer->setLastName('(name protected)');
        $customer->setEmail('(protected)');

        return  $customer;
    }
}
