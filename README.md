# fixboproductname

PrestaShop module that keeps Back Office product names usable when one or more languages are disabled.

## What it does

PrestaShop can still keep product rows for disabled languages in `product_lang`. This module copies each product name from the shop default language into disabled-language rows so the Back Office shows a valid product name instead of an empty or stale value.

The module runs in two situations:

- On installation, it processes existing products once.
- On product update, it listens to `actionProductUpdate` and updates that product's disabled-language names.

## Compatibility

- Minimum PrestaShop version: 1.6
- Maximum PrestaShop version: current installed `_PS_VERSION_`
- Multi-shop aware: updates are scoped to the current shop ID.

## Installation

1. Back up your database before installing, especially on large catalogs.
2. Copy the `fixboproductname` folder into the PrestaShop `modules` directory.
3. Install the module from the Back Office modules page.

## Notes and limitations

- Installation updates existing products immediately. On very large catalogs this may take time, so install during a maintenance window if needed.
- If there are no disabled languages for the current shop, the module skips the update safely.
- The module has no configuration screen.
