<?php declare(strict_types=1);

namespace AdminApiTokenizer\Core\Framework\Api\Response;

use AdminApiTokenizer\Tokenizer\Pipelines\TokenizerInterface;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Response\Type\Api\JsonApiType;
use Shopware\Core\Framework\Api\Serializer\JsonApiEncoder;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SalesChannel\Api\StructEncoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JsonApiTokenizedType extends JsonApiType
{
    /**
     * @var TokenizerInterface[]
     */
    private $tokenizers;

    public function __construct(JsonApiEncoder $serializer, StructEncoder $structEncoder, iterable $tokenizers)
    {
        parent::__construct($serializer, $structEncoder);
        $this->tokenizers = $tokenizers;
    }

    public function createListingResponse(Criteria $criteria, EntitySearchResult $searchResult, EntityDefinition $definition, Request $request, Context $context): Response
    {
        if($this->needsProtection($context)) {
            $searchResult = $this->tokenizeProtectedFields($searchResult);
        }
        return parent::createListingResponse($criteria, $searchResult, $definition, $request, $context);
    }

    private function tokenizeProtectedFields(EntitySearchResult $searchResult): EntitySearchResult
    {
        foreach($searchResult as $item) {
            $this->protect($item);
        }

        return $searchResult;
    }

    private function protect(Entity $entity): Entity
    {
        foreach($this->tokenizers as $tokenizer) {
            if($tokenizer->supports($entity->getApiAlias())) {
                $entity = $tokenizer->protect($entity);
            }
        }

        return $entity;
    }

    private function needsProtection(Context $context)
    {
        if($context->getScope() === Context::USER_SCOPE) {
            /** @var AdminApiSource $source */
            $source = $context->getSource();
            return !$source->isAdmin();
        }
    }
}
