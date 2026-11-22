# Changelog

All notable changes to this module are listed here. The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [1.1.0] - 2026-11-22

### Added

- Uninstaller (`Setup/Uninstall.php`): `bin/magento module:uninstall Delgraf_SeoHtmlSitemapTree --remove-data` now deletes the module's settings (`seohtmlsitemap/tree_view/*`) from `core_config_data` for every scope. The Swissup HTML Sitemap settings are not touched.

## [1.0.0] - 2026-11-22

### Added

- Hierarchical view of the categories in the Swissup HTML Sitemap: nested tree that keeps the Swissup settings for maximum depth, sort order and number of columns.
- Collapsible branches with native `<details>`/`<summary>` elements, a configurable number of levels open by default and optional "Expand All / Collapse All" buttons.
- Optional products inside their categories, placed in the deepest category they are assigned to, with the flat "Products" block hidden and an optional "Other Products" list for products without a visible category.
- Optional item counts on branches.
- Translations for English, Italian, German, French and Spanish.

[1.1.0]: https://github.com/Delgraf/Magento2-SeoHtmlSitemapTree/releases/tag/v1.1.0
[1.0.0]: https://github.com/Delgraf/Magento2-SeoHtmlSitemapTree/releases/tag/v1.0.0
