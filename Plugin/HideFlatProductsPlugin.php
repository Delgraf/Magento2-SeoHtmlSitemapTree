<?php
/**
 * Copyright © Delgraf. All rights reserved.
 * Licensed under the Open Software License version 3.0 (OSL-3.0). See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Delgraf\SeoHtmlSitemapTree\Plugin;

use Delgraf\SeoHtmlSitemapTree\Model\Config\TreeConfig;
use Swissup\SeoHtmlSitemap\Block\Products;

/**
 * Hides the flat "Products" sitemap block when products are already
 * rendered inside the category tree. The block stays in the layout so the
 * tree can reuse its collection and URL logic.
 */
class HideFlatProductsPlugin
{
    public function __construct(
        private readonly TreeConfig $config
    ) {
    }

    public function aroundToHtml(Products $subject, callable $proceed): string
    {
        if ($this->config->isEnabled() && $this->config->showProducts()) {
            return '';
        }

        return (string) $proceed();
    }
}
