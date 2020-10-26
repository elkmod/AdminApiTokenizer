<?php declare(strict_types=1);

namespace AdminApiTokenizer\Tokenizer\Pipelines;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

interface TokenizerInterface {

    public function supports(string $apiAlias): bool;

    public function protect(Entity $entity): Entity;

}
