<?php
/**
 * Copyright © Delgraf. All rights reserved.
 * Licensed under the Open Software License version 3.0 (OSL-3.0). See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Delgraf\SeoHtmlSitemapTree\ViewModel;

use Delgraf\SeoHtmlSitemapTree\Model\Config\TreeConfig;
use Delgraf\SeoHtmlSitemapTree\Model\Tree\Node;
use Delgraf\SeoHtmlSitemapTree\Model\Tree\TreeBuilder;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Swissup\SeoHtmlSitemap\Block\Categories as CategoriesBlock;

/**
 * View model for the hierarchical sitemap template.
 */
class CategoryTree implements ArgumentInterface
{
    public const PRODUCTS_BLOCK_NAME = 'seohtmlsitemap.products';

    /** @var Node[]|null */
    private ?array $roots = null;

    /** @var array<int, array{name: string, url: string}> */
    private array $orphans = [];

    public function __construct(
        private readonly TreeConfig $config,
        private readonly TreeBuilder $treeBuilder
    ) {
    }

    /**
     * @return Node[]
     */
    public function getRoots(CategoriesBlock $block): array
    {
        if ($this->roots === null) {
            $this->roots = $this->treeBuilder->buildCategoryTree($block);

            if ($this->config->showProducts()) {
                $productsBlock = $this->treeBuilder->getProductsBlock($block, self::PRODUCTS_BLOCK_NAME);
                if ($productsBlock) {
                    $this->orphans = $this->treeBuilder->attachProducts(
                        $this->roots,
                        $productsBlock,
                        $this->config->showUncategorized()
                    );
                }
            }
        }

        return $this->roots;
    }

    /**
     * Products that could not be placed in any visible category.
     * Call after getRoots().
     *
     * @return array<int, array{name: string, url: string}>
     */
    public function getOrphanProducts(): array
    {
        return $this->orphans;
    }

    public function isCollapsible(): bool
    {
        return $this->config->isCollapsible();
    }

    public function showToolbar(): bool
    {
        return $this->config->showToolbar();
    }

    public function showCounts(): bool
    {
        return $this->config->showCounts();
    }

    public function getColumnsNumber(): int
    {
        return $this->config->getColumnsNumber();
    }

    /**
     * Whether a branch at the given depth (1 = top level) starts open.
     */
    public function isExpanded(int $depth): bool
    {
        return !$this->config->isCollapsible() || $depth <= $this->config->getExpandedLevels();
    }
}
