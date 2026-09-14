# Build & Architecture Migration — wprig-toolkit → WP Rig 3.4.2

**Date:** 2026-09-14
**Baseline:** `wprig-toolkit` v3.1.0, forked from upstream `wprig/wprig` at tag `v3.1` (the exact
commit where upstream itself replaced Gulp with a custom Node CLI). The checkout had no git
history before this migration — this document's own baseline commit is the rollback anchor.
**Sync target:** upstream `wprig/wprig` tag `v3.4.2` (a real git clone of this fork's own
upstream lineage was available locally at `D:\Local Sites\sovietstuff\app\public\wp-content\themes\wprig`,
with tags `v3.0.0`…`v3.4.2` — used as `git diff`/`git log -p` source of truth throughout,
rather than comparing two disconnected snapshots).

## Why

This boilerplate had drifted from upstream WP Rig for a while and had grown its own
conventions independently (feature-flag constants, `inc/WP_Rig_Toolkit/*` wrapper
components, SVG sprite system, forms/spacing/typography systems, its own `scripts/`
build CLI). A similar migration had already been done once, on a sibling production
theme (`wp-skyward`), documented in that theme's own `BUILD-MIGRATION.md`/`AGENTS.md`/
`CLAUDE.md` — that methodology (pin an exact upstream commit, replace generic build
concerns wholesale, protect project-specific PHP/templates, verify via build/bundle
rather than lint) was the starting point here, but deliberately not copied 1:1: that
migration scoped itself to "build tooling only" because it was a finished production
site with no ongoing need to keep forking new themes from it. `wprig-toolkit` is the
opposite — it exists specifically to be forked into new client themes — so this
migration also adopted architecture and tooling that makes future forking easier.

## Decisions made (with the project owner)

1. **`inc/Theme.php` adopted wholesale**, including component auto-discovery (scanning
   `inc/*/Component.php` one directory level deep), `Asset_Provider`, `Versioning_Trait`,
   `get_config()`/`get_asset_manifests()`. Verified safe: `inc/WP_Rig_Toolkit/{Name}/Component.php`
   lives one level too deep for the scan to find, so those 12 wrapper components needed
   an explicit registration path anyway — they're now toggled via `ENABLE_*` constants
   in root `functions.php` and registered through the new `wprig_theme_components`
   filter in `functions/_functions-toolkit-components.php`, replacing the old
   commented-out-array-line toggle convention. Default behavior preserved exactly
   (only `iMask` and `Validatejs` active by default).
2. **AI-agent tooling adopted at maximum extent, but rewritten for this repo**: `.ai/`
   knowledge base, `AGENTS.md`/`CLAUDE.md`, `.rig-config.json`, `bin/mcp-server/`, the
   Open Component Registry CLI (`scripts/rig.js` + `scripts/lib/{auth,registry,rig-utils,
   cli-utils,block-utils}.js`), and the Playwright E2E + PHPUnit + Lighthouse CI test
   stack. Content was rewritten, not copied verbatim — `.ai/PROJECT_RULES.md` and
   `.ai/developer-directions.md` now describe this repo's actual conventions (notably
   correcting a stock claim that "WP Rig uses PostCSS, not Sass" — this boilerplate's
   spacing/typography/framework systems are Sass-sourced). `.ai/plans/SPEC-*.md` entries
   describing *upstream's own* feature history were removed (not this repo's history).
3. **PHP minimum bumped to 8.1** (`WP_RIG_MINIMUM_PHP_VERSION`, `composer.json`,
   `phpcs.xml.dist` testVersion) — this is a boilerplate for new projects, not a
   version-pinned production site.

## What was migrated, file by file

| Area | What changed |
|---|---|
| `inc/Theme.php`, `inc/Asset_Provider.php` (new), `inc/Versioning_Trait.php` (new) | Adopted wholesale from wprig-master, formatted to match this repo's existing style |
| `inc/Template_Tags.php` | Uses `Versioning_Trait` now (removed duplicate `get_version()`/`get_asset_version()`); `get_theme_asset()` rewritten to be child-theme-aware (checks `get_stylesheet_directory()` first, falls back to `get_template_directory()`) with a static cache, using the new `get_asset_content()` helper. SVG-sprite methods (toolkit-specific) preserved as-is. |
| `inc/functions.php` | Added `wp_rig_theme()`, `get_asset_content()`, `get_config_content()`, `get_config()` |
| `inc/Styles/Component.php`, `inc/Scripts/Component.php` | Added `Asset_Provider` manifest aggregation (`wp_rig_theme()->get_asset_manifests(...)`). Deliberately did **not** adopt master's Performance/critical-CSS integration in the same files — that drags in a whole `inc/Performance/*` subsystem this migration didn't decide to adopt (see "Deliberately skipped" below). Fixed a pre-existing bug in `Scripts/Component.php` where `wp-rig-authors` was pushed as a nested array under a numeric key instead of merged by handle, silently dropping it from enqueueing. |
| `inc/Localization/Component.php`, `inc/Versioning_Trait.php`, `inc/Template_Tags.php::get_theme_asset()` | Made child-theme-aware (`is_child_theme()`/`get_stylesheet_directory()` checks) — caught by adopting upstream's own `scripts/tests/childify.test.js` "Child Theme Regression & Static Analysis Guard" suite, which failed until these were fixed. All 5 of its tests pass now. |
| `functions.php` (root), `functions/_functions-rig-init.php` | Added `ENABLE_*` constants block + require for `_functions-toolkit-components.php`; bumped PHP minimum in `_functions-rig-init.php`; removed a stale "must be parseable by PHP 5.2" comment; added an AGENTS.md-pointing banner. |
| `functions/_functions-toolkit-components.php` (new) | `wprig_theme_components` filter callback registering the enabled `WP_Rig_Toolkit/*` components |
| `eslint.config.js`, `phpcs.xml.dist` | Fixed stale `gulp/`/`gulpfile.js` references (toolkit replaced gulp with `scripts/` a long time ago but never updated these configs — `scripts/**/*.js` had no node-globals/console override, and `scripts/tests/prod-build/` wasn't excluded from phpcs). Added `ignore_warnings_on_exit`, PSR12/Squiz docblock severity overrides, `testVersion` bump, `text_domain` element syntax. |
| `.php-cs-fixer.php` | Fixed a real bug: chaining `->setRules()` directly on `new PhpCsFixer\Config()` without wrapping parens only parses on PHP 8.4+, so `composer fix` was broken on this project's actual PHP 8.1/8.2 runtime. Rewritten without the fluent-on-`new` chain (assign to a variable first) so it survives this project's automatic `prettier:fix` reformatting, which was found to silently strip the parens back out when they were added directly. |
| `lint-css.js` | Fixed a real behavior bug: this script always ran Stylelint with `--fix` hardcoded, regardless of intent — `npm run lint:css` was silently auto-fixing files. Adopted upstream's `--fix`-flag-gated version; added a new `fix:css` script for the explicit-fix case, and updated `.git-prehooks/pre-commit.js` (which relied on `lint:css`'s old auto-fix side effect) to call `fix:css` instead, preserving the existing commit-time auto-fix workflow. |
| `.stylelintrc` | Added a `first-nested` exception to `at-rule-empty-line-before`, mirroring the exception `rule-empty-line-before` already had — fixes a fix-fight where Stylelint wanted a blank line before a first-nested `@media` in `_header.css` but this project's own `prettier:fix` step strips blank lines right after an opening brace, so the two configs fought each other on every commit until the rule's exception matched Prettier's actual output. |
| `scripts/lib/utils.js` | Added `getAssetPath()`, `escapeRegExp()`, `getReplacements()` (extracted, with a smarter `slug` regex using `(?<!wp-block-)…(?!/)` so theme-renaming doesn't corrupt `wp-block-{slug}` block names or namespaced paths); applied `escapeRegExp` to `replaceInlineCSS`/`replaceInlineJS`; removed the dead, unused `gulpRelativeDest()`. |
| `scripts/lib/constants.js` | Added `paths.blocks` (srcDir/dest + prod override); replaced the unconditional `wp-cli/` exclusion with config-driven logic reading the new `export.includeWpCli` key; auto-adds `assets/blocks/**/*` to the export list when that directory exists. |
| `scripts/lib/filepipe.js` | JSDoc-only change, no functional diff — confirmed via real upstream diff, not touched. |
| `build-css.js` | Exported `processThemeUrls`/`insertAfterTopImports`/`ensureVirtualImportInserted` (needed by the newly-adopted `scripts/tests/build-css.test.js`). Did **not** adopt master's `paths.blocks`-based block-output-path change in `processDirectory()` — low value since `assets/blocks/` doesn't exist yet in this checkout, and the rest of this file's extensive custom SCSS-import-order-discovery logic (toolkit's own addition, ~270 extra lines vs. master) was left untouched. `build-js.js` needed **no changes at all** — confirmed unchanged between upstream `v3.1` and `v3.4.2`. |
| `node/childify.js`, `node/create-rig-component.mjs`, `node/editorSupport.js` | Adopted wholesale from wprig-master (toolkit's copies were confirmed logic-identical to the `v3.1` baseline, pure-Prettier-formatting diffs only — real refactor happened upstream between `v3.1` and `v3.4.2`). `node/create-component-wrapper.js` confirmed unchanged upstream, left as-is. |
| `composer.json` | Bumped `wp-coding-standards/wpcs` to `^3.4.1`, `dealerdirect/phpcodesniffer-composer-installer` to `1.2.1`, `yoast/phpunit-polyfills` to `^4.0`, `phpunit/phpunit` to `9.*`; added `phpstan/phpstan`, `phpstan/extension-installer`, `szepeviktor/phpstan-wordpress` + `phpstan`/`phpstan:baseline` scripts. |
| `phpstan.neon.dist`, `phpstan-baseline.neon` (new) | Adopted from wprig-master (level 0, excludes `scripts/optional/node/wp-cli/tests/assets/docs/languages`) |
| `package.json` | Merged scripts/devDependencies: kept every toolkit-only script (`build:spacing`, `build:typography`, `build:svg-sprite`, `childify`, `setup-child`, `prepare` git-hook, etc.), added upstream's new scripts (`fix:css`, `mcp`, `ai:setup`, `ai:check`, `theme:enable-blocks`, `theme:setup-wporg`, `test:e2e*`, `test:perf*`, `test:scripts`, `test:prod-build`, `audit:*`, `rig:*`). Deliberately left out `lint:blocks`, `get-dev-url`, `bundle:wporg` — those need `scripts/cli.js` subcommands not ported in this migration (see "Deliberately skipped"). Did **not** switch the `prettier` engine to `npm:wp-prettier@3.0.3` (an unrelated formatting-engine swap that would reformat the whole codebase again for no benefit tied to this migration's goals). |
| `config/config.default.json` | Added `theme.themeType`/`theme.enableBlocks`, `export.includeWpCli`. Deliberately did **not** add master's new `performance.*` config block — it's read by `inc/Performance/Component.php`, which this migration didn't adopt (see below); adding the config with no component to consume it would be dead/misleading. Kept toolkit's own `dev.styles.features.mobile-nav-type`, `child.*`, extra `export.filesToCopy` entries as-is. Left `theme.slug`/`theme.PHPNamespace` at stock `wp-rig`/`WP_Rig\WP_Rig` deliberately — this boilerplate's own identity should stay generic; `npm run rig-init` is meant to be run once per new client theme, not on this checkout. |

## Deliberately skipped (not a gap — a scoped decision)

- **`inc/Performance/*`** (critical-CSS/cookie-based performance strategies) — a whole
  new subsystem, not part of any of the three decisions above. Adopting it would have
  meant also pulling in `Cookie_Strategy.php`, `Critical_Strategy_Interface.php`, and
  reworking `Styles/Component.php` far beyond the `Asset_Provider` hookup actually
  needed. Revisit if a future client theme genuinely needs critical-CSS inlining.
- **`inc/Icons/Component.php`, `inc/Layout/Component.php`, `inc/Block_Styles/Component.php`,
  `inc/Dev_Tools/Component.php`, `inc/Registry_Config/Component.php`** — new upstream
  components that would auto-activate the moment their files exist (no manifest is in
  use, so auto-discovery picks up anything at `inc/{Name}/Component.php` automatically).
  `Icons` in particular overlaps functionally with this boilerplate's own SVG icon/sprite
  system in `Template_Tags.php` (`wprig_icon()` vs. `get_svg_icon()`/`get_svg_icon_from_sprite()`)
  — adding it now would create two competing icon APIs. None of these were part of the
  three migration decisions; skipped rather than silently auto-activated.
- **`scripts/cli.js` full modular rewrite** — upstream refactored its `cli.js` from a
  monolith into a thin dispatcher over ~20 new `scripts/tasks/*.js` modules (`build.js`,
  `dev.js`, `bundle.js`, `init.js`, etc.) between `v3.1` and `v3.4.2`. Toolkit's own
  `cli.js` (546 lines) still has this logic inline and works correctly as-is; adopting
  the new modular layout would be a large, high-risk architectural rewrite with no
  behavior change, so it was left alone. This is why `lint:blocks`, `get-dev-url`, and
  `bundle:wporg` aren't wired into `package.json` yet — those specific upstream
  subcommands live in task modules that were never ported.
- **`build-js.js`** — confirmed byte-identical in behavior between upstream `v3.1` and
  `v3.4.2` (no upstream changes to port), so nothing to reconcile.
- **`.browserslistrc`, `.stylelintrc` (base rules), `phpunit.xml.dist`,
  `phpunit.integration.xml.dist`, `rector.php`** — confirmed unchanged between upstream
  `v3.1` and `v3.4.2` via real `git diff`, no action needed beyond the one `.stylelintrc`
  exception described above.

## How "spawn a new theme from this boilerplate" actually works (important finding)

Verified by reading and exercising `scripts/cli.js`'s `init` command and `node/childify.js`
directly, since the plan assumed `npm run rig-init` renames the PHP namespace/slug across the
codebase — it does not:

- **`npm run rig-init`** (`npm install && composer install && node scripts/cli.js init`) only
  writes local dev server settings (BrowserSync proxy URL/port/HTTPS) into `config/config.json`.
  It does not touch `theme.slug`/`theme.name`/`theme.PHPNamespace` at all.
- **The `theme.*` fields** (slug/name/PHPNamespace/etc., via `nameFieldDefaults` in
  `scripts/lib/constants.js` and `getReplacements()`/`getStringReplacementTasks()` in
  `scripts/lib/utils.js`) are only ever string-replaced into `style.css` and `languages/*.po`
  during `npm run bundle` (`paths.export.stringReplaceSrc`). **PHP files are never touched** —
  there is no automated PHP-namespace-renaming step anywhere in this codebase. Confirmed by
  grepping all of `scripts/`/`node/` for any `PHPNamespace`-driven file rewrite: none exists.
- **`npm run childify`** (`node/childify.js`) is a different, destructive, interactive tool: it
  converts the *current* theme in place into a lightweight WordPress child theme of some other
  parent theme (prompts for the parent's folder slug, writes a `Template:` header into
  `style.css`, trims `inc/` down to just `Styles`/`Scripts`/`Sidebars`, backs up removed
  content into `childify_backup/`). It does not create a new independent theme either — a
  child theme keeps running under this same `WP_Rig\WP_Rig` namespace via the parent's PHP.
  Exercising it end-to-end with piped stdin answers proved unreliable in this environment
  (inquirer's readline over piped, non-TTY stdin misbehaved on the "input"-type slug prompt);
  its own exported `getPhpFiles()` was verified directly instead — it correctly enumerates all
  106 PHP files including the toolkit-specific ones (`functions/_functions-toolkit-components.php`,
  `inc/WP_Rig_Toolkit/GSAP/Component.php`, `template-parts/forms/form-example.php`), and
  upstream's own `scripts/tests/childify.test.js` (5/5 tests, including the child-theme
  static-analysis guard that drove this migration's `Localization`/`Versioning_Trait`/
  `Template_Tags::get_theme_asset()` fixes) passes cleanly.
- **Practical implication:** today, "spawn a new independent client theme" from this
  boilerplate means copying the whole directory and deciding by hand whether to rename the
  `WP_Rig\WP_Rig` namespace (harmless to leave as-is if the resulting themes are never active
  on the same site at the same time as another `WP_Rig\WP_Rig`-namespaced theme; only one
  theme's PHP loads per request). `rig-init`/`bundle` handle the display name/slug/text-domain
  cosmetics; `childify` is for the child-theme case specifically. If true multi-theme
  independence (distinct namespaces) becomes a real need, that would be new tooling to build,
  not something this migration uncovered as broken — it was never automated upstream either.

## Verification performed

- `npm install && composer install` (then `composer update` once new dependency version
  constraints were added) — clean.
- `npm run build` — completes (`Build completed.`).
- `npm run bundle` — completes; spot-checked the exported theme contains the protected
  entries (`assets/data/countries.json`, `inc/EZ_Customizer/themeCustomizeSettings.json`).
- `NODE_OPTIONS=--experimental-vm-modules npx jest scripts/tests/` — **60/68 tests pass**.
  The 3 failing suites are pre-existing/expected, not regressions:
  - `scripts/tests/styles/styles.test.js` — imports `../../lib/styles`, which has never
    existed in this codebase (pre-existing gap, unrelated to this migration).
  - `scripts/tests/prod-build/prod-build.test.js` — needs a full `npm run bundle` run as
    setup (works when run via `npm run test:prod-build`, not standalone).
  - `scripts/tests/bundle-wporg-e2e.test.js` — needs the `bundle:wporg` command, which
    isn't wired up (see "Deliberately skipped" — depends on the un-ported `cli.js` refactor).
  - Notably, `scripts/tests/childify.test.js` (upstream's own child-theme static-analysis
    guard) initially failed 3/5 assertions and drove the child-theme-compatibility fixes
    listed above — all 5 pass now.
- Lint baseline recorded (accepted, not blocking, per the same approach the `wp-skyward`
  precedent used): `npm run lint:js` → 83 problems (27 errors, 56 warnings, mostly
  `no-console` and `jsdoc/check-line-alignment`). `npm run lint:css` → 0. The one
  pre-existing error (`at-rule-empty-line-before` on `_header.css`'s first-nested
  `@media`) turned out to be a genuine fix-fight between Stylelint's default rule and
  this project's own `prettier:fix` step (which strips a blank line right after an
  opening brace) — hand-editing the CSS got silently undone by the same pre-commit
  hook run, same pattern as the `.php-cs-fixer.php` bug above. Fixed at the root by
  adding a `first-nested` exception to `at-rule-empty-line-before` in `.stylelintrc`
  (mirroring the exception `rule-empty-line-before` already had). `composer run-phpcs` (WPCS) → 5858 errors /
  536 warnings across 114 files — this reflects the whole codebase's Prettier-based
  formatting convention conflicting with WordPress-Coding-Standards' brace/spacing
  rules, a pre-existing condition unrelated to this migration's own changes (verified:
  untouched files like `inc/PWA/Component.php` show the same class of violations).

## Rollback

Every phase of this migration landed as its own commit in this repo's git history. If
you're reading this after a fresh `git init` (see the note in this repo's commit log
around 2026-09-14 about a filesystem incident during the final move-to-live-location
step, which destroyed the incremental commit history but not the working-tree content),
the single baseline commit is the rollback anchor for everything from that point forward.
