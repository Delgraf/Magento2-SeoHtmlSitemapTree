<?php
/**
 * Copyright © Delgraf. All rights reserved.
 * Licensed under the Open Software License version 3.0 (OSL-3.0). See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Delgraf\SeoHtmlSitemapTree\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Removes the data the module stores when it is uninstalled with
 * "bin/magento module:uninstall Delgraf_SeoHtmlSitemapTree --remove-data".
 *
 * The module has no tables of its own: the only data is the configuration
 * saved in the admin under "seohtmlsitemap/tree_view/", for every scope.
 * The settings of the Swissup HTML Sitemap module are not touched.
 */
class Uninstall implements UninstallInterface
{
    private const CONFIG_PATH_PREFIX = 'seohtmlsitemap/tree_view/';

    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $connection->delete(
            $setup->getTable('core_config_data'),
            ['path LIKE ?' => self::CONFIG_PATH_PREFIX . '%']
        );

        $setup->endSetup();
    }
}
