# EPL code reference

Builds https://docs.easypropertylistings.com.au/, the reference for every class, function and hook in core.

## Build

From the plugin folder:

```bash
sh apigen/build.sh
```

The output goes to `epl-apidocs/`, which git ignores. It takes a few seconds. You don't edit anything for a release, because the version in the title is read from the plugin header.

The first run installs ApiGen 7 (`7.0.0-alpha.6`, pinned in `build.sh`) into `~/.epl-apigen`. It lives outside the plugin, so it never reaches SVN or the release zip. It needs PHP 8.1 or later and Composer.

For a local preview, pass the preview URL so links and styles load from there:

```bash
sh apigen/build.sh http://localhost:8888/epl-docs-preview/
```

## Publish

1. Copy into the `epl-api-docs` repo (`~/Sites/localhost/epl-api-docs`, GitHub `easypropertylistings/epl-api-docs`). Keep the search engine verification files:
   `rsync -a --delete --exclude .git --exclude 'google*.html' --exclude 'yandex_*.html' epl-apidocs/ ~/Sites/localhost/epl-api-docs/`
2. Commit there with the version as the message (e.g. `3.6.2`) and push.
3. Upload to the docs site (cPanel). Include `.htaccess`, which redirects the old ApiGen 2 URLs.

## Files

- `../apigen.neon`: ApiGen settings (what is scanned, output folder, theme, site URL).
- `build.sh`: installs ApiGen if needed and runs every step below.
- `theme/`: the EPL theme. It holds only the templates that differ from ApiGen's own:
  - `blocks/classLikeDescription.latte` and `blocks/method.latte`: the full `@since` list on classes, functions and methods (ApiGen 7 doesn't show `@since`).
  - `blocks/menu.latte`, `pages/index.latte`, `blocks/navbar.latte`: list classes and functions directly. Core has no PHP namespaces, so ApiGen's own home page shows a single "none" namespace. They also add the Hooks link.
  - `blocks/head.latte`, `blocks/layout.latte`: the EPL stylesheet, Google Analytics 4 and the page title.
  - `assets/epl.css`: styling from the easypropertylistings.com.au "What's New" page. Colours are custom properties at the top, so a site redesign means changing those values.
- `hook-docs.php`: writes `hook-docs.html`, every EPL action and filter linked to the line that fires it.
- `htaccess`: copied to `epl-apidocs/.htaccess`. It redirects old URLs: `class-X.html` to `X.html`, `source-function-X.html` to `function-X.html`, `source-class-X.html` to `X.html`, and package and todo pages to the home page. Function pages kept their URLs.

## Why ApiGen 7

The old ApiGen 2 needed PHP 5.6 and couldn't parse PHP 7 return types, so the Elementor code was left out. In November 2026 ApiGen 7, phpDocumentor 3 and Doctum were compared on 3.6.1. ApiGen 7 was the only one that keeps one page per function, with the same URLs as before. phpDocumentor puts all 600+ functions on one page, and Doctum shows only the first `@since` line.
