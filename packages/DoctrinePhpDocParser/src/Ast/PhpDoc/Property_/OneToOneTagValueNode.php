<?php declare(strict_types=1);

namespace Rector\DoctrinePhpDocParser\Ast\PhpDoc\Property_;

use Nette\Utils\Json;
use Nette\Utils\Strings;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagValueNode;
use Rector\BetterPhpDocParser\Attributes\Attribute\AttributeTrait;
use Rector\BetterPhpDocParser\Attributes\Contract\Ast\AttributeAwareNodeInterface;
use Rector\DoctrinePhpDocParser\Array_\ArrayItemStaticHelper;
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\DoctrineTagNodeInterface;
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\InversedByNodeInterface;
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\MappedByNodeInterface;
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\ToOneTagNodeInterface;

final class OneToOneTagValueNode implements PhpDocTagValueNode, AttributeAwareNodeInterface, DoctrineTagNodeInterface, ToOneTagNodeInterface, MappedByNodeInterface, InversedByNodeInterface
{
    use AttributeTrait;

    /**
     * @var string[]
     */
    private $orderedVisibleItems = [];

    /**
     * @var string
     */
    private $targetEntity;

    /**
     * @var string|null
     */
    private $mappedBy;

    /**
     * @var string|null
     */
    private $inversedBy;

    /**
     * @var mixed[]|null
     */
    private $cascade;

    /**
     * @var string|null
     */
    private $fetch;

    /**
     * @var bool
     */
    private $orphanRemoval = false;

    /**
     * @var string
     */
    private $fqnTargetEntity;

    /**
     * @param mixed[]|null $cascade
     * @param string[] $orderedVisibleItems
     */
    public function __construct(
        string $targetEntity,
        ?string $mappedBy,
        ?string $inversedBy,
        ?array $cascade,
        ?string $fetch,
        bool $orphanRemoval,
        array $orderedVisibleItems,
        string $fqnTargetEntity
    ) {
        $this->orderedVisibleItems = $orderedVisibleItems;
        $this->targetEntity = $targetEntity;
        $this->mappedBy = $mappedBy;
        $this->inversedBy = $inversedBy;
        $this->cascade = $cascade;
        $this->fetch = $fetch;
        $this->orphanRemoval = $orphanRemoval;
        $this->fqnTargetEntity = $fqnTargetEntity;
    }

    public function __toString(): string
    {
        $contentItems = [];

        $contentItems['targetEntity'] = sprintf('targetEntity="%s"', $this->targetEntity);
        $contentItems['mappedBy'] = sprintf('mappedBy="%s"', $this->mappedBy);
        $contentItems['inversedBy'] = sprintf('inversedBy="%s"', $this->inversedBy);
        if ($this->cascade) {
            $json = Json::encode($this->cascade);
            $json = Strings::replace($json, '#,#', ', ');
            $json = Strings::replace($json, '#\[(.*?)\]#', '{$1}');
            $contentItems['cascade'] = sprintf('cascade=%s', $json);
        }
        $contentItems['fetch'] = sprintf('fetch="%s"', $this->fetch);
        $contentItems['orphanRemoval'] = sprintf('orphanRemoval=%s', $this->orphanRemoval ? 'true' : 'false');

        $contentItems = ArrayItemStaticHelper::filterAndSortVisibleItems($contentItems, $this->orderedVisibleItems);
        if ($contentItems === []) {
            return '';
        }

        return '(' . implode(', ', $contentItems) . ')';
    }

    public function getTargetEntity(): ?string
    {
        return $this->targetEntity;
    }

    public function getFqnTargetEntity(): string
    {
        return $this->fqnTargetEntity;
    }

    public function getInversedBy(): ?string
    {
        return $this->inversedBy;
    }

    public function getMappedBy(): ?string
    {
        return $this->mappedBy;
    }

    public function removeInversedBy(): void
    {
        $this->orderedVisibleItems = ArrayItemStaticHelper::removeItemFromArray(
            $this->orderedVisibleItems,
            'inversedBy'
        );

        $this->inversedBy = null;
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
