<?php
/**
 * Copyright © Delgraf. All rights reserved.
 * Licensed under the Open Software License version 3.0 (OSL-3.0). See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Delgraf\SeoHtmlSitemapTree\Model\Tree;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\View\Element\AbstractBlock;
use Swissup\SeoHtmlSitemap\Block\Categories as CategoriesBlock;
use Swissup\SeoHtmlSitemap\Block\Products as ProductsBlock;

/**
 * Builds a category tree (optionally with products) reusing the
 * collections and URL logic of the Swissup sitemap blocks, so that all
 * Swissup filters (depth, sort, stock, visibility...) keep working.
 */
class TreeBuilder
{
    private const PRODUCT_PAGE_SIZE = 500;

    /**
     * Builds the category tree.
     *
     * @return Node[] Top-level nodes
     */
    public function buildCategoryTree(CategoriesBlock $block): array
    {
        $collection = $block->getCollection();
        if (!$collection) {
            return [];
        }

        /** @var Node[] $nodes */
        $nodes = [];
        /** @var Category $category */
        foreach ($collection as $category) {
            $id = (int) $category->getId();
            $nodes[$id] = new Node(
                $id,
                (int) $category->getParentId(),
                (string) $block->getItemName($category),
                (string) $block->getItemUrl($category),
                array_map('intval', explode('/', (string) $category->getPath()))
            );
        }

        // Second pass: link children to parents, keeping collection sort order.
        $roots = [];
        foreach ($nodes as $node) {
            $parentId = $node->getParentId();
            if (isset($nodes[$parentId])) {
                $nodes[$parentId]->addChild($node);
            } else {
                $roots[] = $node;
            }
        }

        return $roots;
    }

    /**
     * Distributes products to the deepest assigned categories of the tree.
     *
     * @param Node[] $roots
     * @return array<int, array{name: string, url: string}> Products not placed in any node
     */
    public function attachProducts(array $roots, ProductsBlock $block, bool $collectOrphans): array
    {
        $collection = $block->getCollection();
        if (!$collection instanceof ProductCollection) {
            return [];
        }

        $index = $this->indexNodes($roots);
        $orphans = [];

        $collection = clone $collection;
        $collection->setPageSize(self::PRODUCT_PAGE_SIZE);
        $lastPage = (int) $collection->getLastPageNumber();

        for ($page = 1; $page <= $lastPage; $page++) {
            $collection->clear();
            $collection->setCurPage($page);
            $collection->setFlag('category_ids_added', false);
            $collection->load();
            $collection->addCategoryIds();

            /** @var Product $product */
            foreach ($collection as $product) {
                $name = (string) $block->getItemName($product);
                $url = (string) $block->getItemUrl($product);
                $targets = $this->resolveTargets((array) $product->getCategoryIds(), $index);

                if ($targets === []) {
                    if ($collectOrphans) {
                        $orphans[$url] = ['name' => $name, 'url' => $url];
                    }
                    continue;
                }

                foreach ($targets as $node) {
                    $node->addProduct($name, $url);
                }
            }
        }

        return array_values($orphans);
    }

    /**
     * Keeps only the deepest matching categories (drops ancestors of other matches).
     *
     * @param array $categoryIds
     * @param array<int, Node> $index
     * @return Node[]
     */
    private function resolveTargets(array $categoryIds, array $index): array
    {
        $matches = [];
        foreach ($categoryIds as $categoryId) {
            $categoryId = (int) $categoryId;
            if (isset($index[$categoryId])) {
                $matches[$categoryId] = $index[$categoryId];
            }
        }

        if (count($matches) < 2) {
            return array_values($matches);
        }

        $ancestors = [];
        foreach ($matches as $id => $node) {
            foreach ($node->getPathIds() as $pathId) {
                if ($pathId !== $id) {
                    $ancestors[$pathId] = true;
                }
            }
        }

        return array_values(array_diff_key($matches, $ancestors));
    }

    /**
     * Flattens the tree into an id => node map.
     *
     * @param Node[] $nodes
     * @return array<int, Node>
     */
    private function indexNodes(array $nodes): array
    {
        $index = [];
        $stack = $nodes;
        while ($stack) {
            $node = array_pop($stack);
            $index[$node->getId()] = $node;
            foreach ($node->getChildren() as $child) {
                $stack[] = $child;
            }
        }

        return $index;
    }

    /**
     * Fetches a sibling block from the layout if it is of the expected type.
     */
    public function getProductsBlock(AbstractBlock $block, string $name): ?ProductsBlock
    {
        $layout = $block->getLayout();
        $productsBlock = $layout ? $layout->getBlock($name) : false;

        return $productsBlock instanceof ProductsBlock ? $productsBlock : null;
    }
}
