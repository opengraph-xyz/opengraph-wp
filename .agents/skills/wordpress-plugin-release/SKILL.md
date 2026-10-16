---
name: wordpress-plugin-release
description: "OpenGraph.xyz WordPress plugin release and packaging rules. Use when adding runtime assets, changing deploy workflows, bumping versions, tagging releases, or verifying WordPress.org SVN contents."
---

# WordPress Plugin Release

## Directory Rules

- Treat the repository-level `assets/` directory as WordPress.org listing media only. It may contain banners, icons, and screenshots.
- Never place runtime PHP, CSS, JavaScript, fonts, or other files required by the installed plugin in repository-level `assets/`.
- Place runtime code and browser assets under `src/`. Use `src/assets/css/` and `src/assets/js/` for runtime stylesheets and scripts.
- Enqueue runtime files with paths relative to the PHP file passed to `plugins_url()`. For example, from `src/Admin.php`:

```php
plugins_url('assets/css/admin-filters.css', __FILE__)
plugins_url('assets/js/admin-filters.js', __FILE__)
```

WordPress permits public plugin assets in any directory included in the installed plugin. `plugins_url($path, __FILE__)` resolves `$path` relative to the directory containing that PHP file.

## Why This Matters

The deploy workflow sets `ASSETS_DIR: assets` for `10up/action-wordpress-plugin-deploy`. That directory is copied to the WordPress.org SVN top-level `assets/` directory beside `trunk`; it is not included in installable plugin tags. Putting runtime files there causes them to be absent from downloaded plugin releases.

WordPress.org documentation defines its top-level `assets/` directory as listing media shared across plugin versions, not plugin runtime content.

## Release Checks

Before tagging a release:

1. Search runtime references and confirm every referenced file exists outside repository-level `assets/`.
2. Confirm repository-level `assets/` contains only banners, icons, and screenshots.
3. Verify enqueue paths resolve relative to the PHP file passed to `plugins_url()`.
4. After deployment, inspect `https://plugins.svn.wordpress.org/opengraph-xyz/tags/<version>/` and confirm all required runtime files are present.
5. Confirm listing media appears under `https://plugins.svn.wordpress.org/opengraph-xyz/assets/`.
6. Open the installed plugin admin UI and verify runtime CSS and JavaScript requests return HTTP 200.

## References

- WordPress `plugins_url()`: https://developer.wordpress.org/reference/functions/plugins_url/
- WordPress.org plugin assets: https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
- Deploy action `ASSETS_DIR`: https://github.com/10up/action-wordpress-plugin-deploy
