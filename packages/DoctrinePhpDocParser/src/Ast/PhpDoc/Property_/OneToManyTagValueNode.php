<?php declare(strict_types=1);

namespace Rector\DoctrinePhpDocParser\Ast\PhpDoc\Property_;

use Nette\Utils\Json;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Rector\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Rector\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;
use Rector\DoctrinePhpDocParser\Array_\ArrayItemStaticHelper;
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\DoctrineTagNodeInterface;
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\MappedByNodeInterface;
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\ToManyTagNodeInterface;

final class OneToManyTagValueNode implements PhpDocTagValueNode, AttributeAwareNodeInterface, DoctrineTagNodeInterface, ToManyTagNodeInterface, MappedByNodeInterface
{
    use AttributeTrait;

    /**
     * @var string|null
     */
    private $mappedBy;

    /**
     * @var string
     */
    private $targetEntity;

    /**
     * @var mixed[]|null
     */
    private $cascade;

    /**
     * @var string
     */
    private $fetch;

    /**
     * @var bool
     */
    private $orphanRemoval = false;

    /**
     * @var string|null
     */
    private $indexBy;

    /**
     * @var string[]
     */
    private $orderedVisibleItems = [];

    /**
     * @var string
     */
    private $fqnTargetEntity;

    /**
     * @param string[] $orderedVisibleItems
     */
    public function __construct(
        ?string $mappedBy,
        string $targetEntity,
        ?array $cascade,
        string $fetch,
        bool $orphanRemoval,
        ?string $indexBy,
        array $orderedVisibleItems,
        string $fqnTargetEntity
    ) {
        $this->orderedVisibleItems = $orderedVisibleItems;
        $this->mappedBy = $mappedBy;
        $this->targetEntity = $targetEntity;
        $this->cascade = $cascade;
        $this->fetch = $fetch;
        $this->orphanRemoval = $orphanRemoval;
        $this->indexBy = $indexBy;
        $this->fqnTargetEntity = $fqnTargetEntity;
    }

    public function __toString(): string
    {
        $contentItems = [];

        $contentItems['mappedBy'] = sprintf('mappedBy="%s"', $this->mappedBy);
        $contentItems['targetEntity'] = sprintf('targetEntity="%s"', $this->targetEntity);

        if ($this->cascade) {
            $json = Json::encode($this->cascade);
            $json = Strings::replace($json, '#,#', ', ');
            $json = Strings::replace($json, '#\[(.*?)\]#', '{$1}');
            $contentItems['cascade'] = sprintf('cascade=%s', $json);
        }
        $contentItems['fetch'] = sprintf('fetch="%s"', $this->fetch);
        $contentItems['orphanRemoval'] = sprintf('orphanRemoval=%s', $this->orphanRemoval ? 'true' : 'false');
        $contentItems['indexBy'] = sprintf('indexBy="%s"', $this->indexBy);

        $contentItems = ArrayItemStaticHelper::filterAndSortVisibleItems($contentItems, $this->orderedVisibleItems);
        if ($contentItems === []) {
            return '';
        }

        return '(' . implode(', ', $contentItems) . ')';
    }

    public function getTargetEntity(): string
    {
        return $this->targetEntity;
    }

    public function getFqnTargetEntity(): string
    {
        return $this->fqnTargetEntity;
    }

    public function getMappedBy(): ?string
    {
        return $this->mappedBy;
    }

    public function removeMappedBy(): void
    {
        $this->orderedVisibleItems = ArrayItemStaticHelper::removeItemFromArray(
            $this->orderedVisibleItems,
            'mappedBy'
        );

        $this->mappedBy = null;
    }
}
