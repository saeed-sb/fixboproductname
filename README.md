# Fix Back Office Product Name

A focused PrestaShop maintenance module that keeps Back Office product names available when one or more shop languages are disabled.

| Property | Value |
| --- | --- |
| Current version | `1.0.1` |
| PrestaShop compatibility | `1.6+` |
| Module directory | `fixboproductname` |
| Author | Saeed Sattar Beglou |
| License | AFL-3.0 (declared in the source) |

## Problem addressed

PrestaShop may retain rows for disabled languages in `product_lang`. Those rows can contain an empty or stale product name, which may cause unusable labels in parts of the Back Office.

The module copies the product name from the shop's default language into the disabled-language rows for the same product and shop.

## Features

- Repairs existing catalog rows during module installation.
- Reapplies the repair whenever a product is updated through `actionProductUpdate`.
- Limits writes to disabled languages.
- Scopes queries to the current shop ID.
- Safely skips work when the shop has no disabled languages.
- Requires no configuration page and creates no custom database tables.

## Important data behavior

This module intentionally updates the `name` column in existing `product_lang` rows for disabled languages. It does not translate product names; it copies the current shop default-language name.

Create a database backup before installation, especially when:

- the catalog is large;
- disabled-language names contain content you may want to preserve;
- the module is being introduced directly on a production store.

## Installation

1. Back up the PrestaShop database.
2. Download the `v1.0.1` package from [GitHub Releases](https://github.com/saeed-sb/fixboproductname/releases).
3. Make sure the archive contains one top-level directory named `fixboproductname/`.
4. In the Back Office, open **Modules > Module Manager**.
5. Select **Upload a module**, upload the archive and install it.

The source may also be copied manually to:

```text
modules/fixboproductname/
```

Installation immediately processes existing products in the current shop.

## Runtime behavior

After installation, every product update triggers the following scoped operation:

1. Resolve the current shop's default language.
2. Find the shop's disabled languages.
3. Read the product name in the default language.
4. Copy that name into the same product's disabled-language rows.

When there are no disabled languages, the hook returns without changing data.

## Multi-shop notes

Queries are constrained by `id_shop`, and the module uses the active shop context. Install and validate it separately in each relevant shop context when using a multi-shop installation.

## Performance notes

Installation iterates through the existing catalog. On a large store, run installation during a maintenance window and monitor database load. Normal runtime processing is limited to the product being updated.

## Configuration

No configuration is required. Installing the module enables its product-update hook; uninstalling it removes the hook but does not revert names that were already copied.

## Version 1.0.1

This release establishes the current maintained baseline with shop-scoped updates, safe disabled-language detection and automatic repair on installation and product update.
