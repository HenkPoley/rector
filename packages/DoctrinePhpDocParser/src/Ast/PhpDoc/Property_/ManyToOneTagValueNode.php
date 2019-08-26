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
use Rector\DoctrinePhpDocParser\Contract\Ast\PhpDoc\ToOneTagNodeInterface;

final class ManyToOneTagValueNode implements PhpDocTagValueNode, AttributeAwareNodeInterface, DoctrineTagNodeInterface, ToOneTagNodeInterface, InversedByNodeInterface
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
     * @var array|null
     */
    private $cascade;

    /**
     * @var string
     */
    private $fetch;

    /**
     * @var string|null
     */
    private $inversedBy;

    /**
     * @var string
     */
    private $fqnTargetEntity;

    /**
     * @param string[] $orderedVisibleItems
     */
    public function __construct(
        string $targetEntity,
        ?array $cascade,
        string $fetch,
        ?string $inversedBy,
        array $orderedVisibleItems,
        string $fqnTargetEntity
    ) {
        $this->orderedVisibleItems = $orderedVisibleItems;
        $this->targetEntity = $targetEntity;
        $this->cascade = $cascade;
        $this->fetch = $fetch;
        $this->inversedBy = $inversedBy;
        $this->fqnTargetEntity = $fqnTargetEntity;
    }

    public function __toString(): string
    {
        $contentItems = [];

        $contentItems['targetEntity'] = sprintf('targetEntity="%s"', $this->targetEntity);
        if ($this->cascade) {
            $json = Json::encode($this->cascade);
            $json = Strings::replace($json, '#,#', ', ');
            $json = Strings::replace($json, '#\[(.*?)\]#', '{$1}');
            $contentItems['cascade'] = sprintf('cascade=%s', $json);
        }
        $contentItems['fetch'] = sprintf('fetch="%s"', $this->fetch);
        $contentItems['inversedBy'] = sprintf('inversedBy="%s"', $this->inversedBy);

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

    public function removeInversedBy(): void
    {
        $this->orderedVisibleItems = ArrayItemStaticHelper::removeItemFromArray(
            $this->orderedVisibleItems,
            'inversedBy'
        );

        $this->inversedBy = null;
    }
}
