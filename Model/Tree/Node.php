<?php
/**
 * Copyright © Delgraf. All rights reserved.
 * Licensed under the Open Software License version 3.0 (OSL-3.0). See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Delgraf\SeoHtmlSitemapTree\Model\Tree;

/**
 * Single category node of the sitemap tree.
 */
class Node
{
    /** @var Node[] */
    private array $children = [];

    /** @var array<int, array{name: string, url: string}> */
    private array $products = [];

    public function __construct(
        private readonly int $id,
        private readonly int $parentId,
        private readonly string $name,
        private readonly string $url,
        private readonly array $pathIds = []
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getParentId(): int
    {
        return $this->parentId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Category ids from the catalog root down to this node (inclusive).
     *
     * @return int[]
     */
    public function getPathIds(): array
    {
        return $this->pathIds;
    }

    public function addChild(Node $child): void
    {
        $this->children[] = $child;
    }

    /**
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function hasChildren(): bool
    {
        return $this->children !== [];
    }

    public function addProduct(string $name, string $url): void
    {
        $this->products[$url] = ['name' => $name, 'url' => $url];
    }

    /**
     * @return array<int, array{name: string, url: string}>
     */
    public function getProducts(): array
    {
        return array_values($this->products);
    }

    public function hasProducts(): bool
    {
        return $this->products !== [];
    }

    /**
     * Whether the node has anything to expand.
     */
    public function isBranch(): bool
    {
        return $this->hasChildren() || $this->hasProducts();
    }

    /**
     * Number of direct sub-items (sub-categories + products).
     */
    public function getItemCount(): int
    {
        return count($this->children) + count($this->products);
    }
}
