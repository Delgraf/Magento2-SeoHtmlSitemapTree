<?php
/**
 * Copyright © Delgraf. All rights reserved.
 * Licensed under the Open Software License version 3.0 (OSL-3.0). See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Delgraf\SeoHtmlSitemapTree\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Typed access to the "seohtmlsitemap/tree_view" configuration group.
 */
class TreeConfig
{
    private const XML_PATH_ENABLED = 'seohtmlsitemap/tree_view/enabled';
    private const XML_PATH_COLLAPSIBLE = 'seohtmlsitemap/tree_view/collapsible';
    private const XML_PATH_EXPANDED_LEVELS = 'seohtmlsitemap/tree_view/expanded_levels';
    private const XML_PATH_SHOW_TOOLBAR = 'seohtmlsitemap/tree_view/show_toolbar';
    private const XML_PATH_SHOW_PRODUCTS = 'seohtmlsitemap/tree_view/show_products';
    private const XML_PATH_SHOW_UNCATEGORIZED = 'seohtmlsitemap/tree_view/show_uncategorized';
    private const XML_PATH_SHOW_COUNTS = 'seohtmlsitemap/tree_view/show_counts';
    private const XML_PATH_COLUMNS_NUMBER = 'seohtmlsitemap/content_settings/columns_number';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->flag(self::XML_PATH_ENABLED);
    }

    public function isCollapsible(): bool
    {
        return $this->flag(self::XML_PATH_COLLAPSIBLE);
    }

    public function getExpandedLevels(): int
    {
        return max(0, (int) $this->value(self::XML_PATH_EXPANDED_LEVELS));
    }

    public function showToolbar(): bool
    {
        return $this->isCollapsible() && $this->flag(self::XML_PATH_SHOW_TOOLBAR);
    }

    public function showProducts(): bool
    {
        return $this->flag(self::XML_PATH_SHOW_PRODUCTS);
    }

    public function showUncategorized(): bool
    {
        return $this->flag(self::XML_PATH_SHOW_UNCATEGORIZED);
    }

    public function showCounts(): bool
    {
        return $this->flag(self::XML_PATH_SHOW_COUNTS);
    }

    public function getColumnsNumber(): int
    {
        return min(4, max(1, (int) $this->value(self::XML_PATH_COLUMNS_NUMBER)));
    }

    private function flag(string $path): bool
    {
        return $this->scopeConfig->isSetFlag($path, ScopeInterface::SCOPE_STORE);
    }

    private function value(string $path): mixed
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
}
