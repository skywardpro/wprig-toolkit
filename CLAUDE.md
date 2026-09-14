# CLAUDE.md — wprig-toolkit

`AGENTS.md` is canonical. This file expands on it with the practical details a Claude
Code session needs day to day.

## Commands

```
npm install && composer install   # first-time setup
npm run dev                       # watch mode (build-css/build-js + BrowserSync)
npm run dev:modern                # alternate dev server (scripts/dev-modern.js)
npm run build                     # one-shot production-mode build
npm run bundle                    # build + copy/zip an exportable theme
npm run build:css / dev:css       # LightningCSS pipeline (main global.css chain)
npm run build:js / dev:js         # esbuild pipeline
npm run build:spacing / dev:spacing       # toolkit-only: Sass -> spacing-system CSS
npm run build:typography / dev:typography # toolkit-only: Sass -> typography-system CSS
npm run build:svg-sprite / dev:svg-sprite # toolkit-only: builds assets/images/icons/sprite-svg/sprite.svg
npm run block:new / block:list / block:remove / block:promote-plugin
npm run create-rig-component      # scaffold a new inc/{Name}/Component.php (or WP_Rig_Toolkit/{Name})
npm run childify / setup-child    # convert into a child theme for a new client project
npm run rig-init                  # rename namespace/slug/text-domain for a new theme fork
npm run lint:css / lint:js  (+ :fix)
npm run ai:check                  # lint:css && lint:js (see AGENTS.md Pillar 7)
npm run mcp                       # MCP doc server
composer run-phpcs / phpcbf-dev / phpunit-dev / phpunit-integration-dev
```

`npm run test:e2e*`, `npm run test:perf*`, and the `npm run rig:*` Open Component
Registry commands are wired up (configs and scripts are present, copied from upstream
WP Rig 3.4.2), but there are no toolkit-specific Playwright specs or a published
component registry entry yet — treat them as available infrastructure, not as
something with existing project-specific coverage. Don't assume a green run of one of
these proves anything about this theme's actual features until specs are written for
them.

## Two-layer PHP architecture

1. **WP Rig component system** — `inc/`, namespace `WP_Rig\WP_Rig`, PSR-4
   autoloaded via Composer. `inc/Theme.php` auto-discovers components by scanning
   `inc/*/Component.php` one directory level deep (falls back to
   `inc/components-manifest.json` if present, and respects an optional static
   `is_active()` method per component, plus the `wprig_theme_components` filter).
   Stock components (`Base_Support`, `Editor`, `Accessibility`, `Nav_Menus`,
   `EZ_Customizer`, `Styles`, `Scripts`, etc.) and `inc/Blocks/Component.php` are all
   found this way automatically — no registration needed for a new one at that depth.
2. **`WP_Rig_Toolkit/*` wrapper components** — `inc/WP_Rig_Toolkit/{Name}/Component.php`,
   one level too deep for auto-discovery on purpose. Toggled via `ENABLE_*` constants in
   root `functions.php`, wired in through the `wprig_theme_components` filter in
   `functions/_functions-toolkit-components.php`. Only `iMask` and `Validatejs` are
   enabled by default. Add a new wrapper with `npm run create-rig-component`, then add
   its `ENABLE_*` constant and a line in the filter callback.
3. **Project procedural layer** — `functions/_functions-*.php`, required directly from
   root `functions.php` (not part of the component system): menu registration, ACF
   fields, custom shortcodes, asset enqueue registry, WP/plugin customizations,
   optimization tweaks. `functions.php` itself is mostly the `DISABLE_*`/`ENABLE_*`
   constant block plus the `require`s — don't add ad-hoc hooks directly in its body.

`inc/Asset_Provider.php` lets any component declare CSS/JS in `get_asset_manifest()`;
`Theme::get_asset_manifests()` aggregates these and `Styles`/`Scripts` components merge
them in automatically. No `WP_Rig_Toolkit/*` wrapper uses this yet (they enqueue
directly) — it's wired up and available for new components, not a required migration
for existing ones.

## Asset pipeline details

- Main CSS entry point is `assets/css/src/global.css`'s `@import` manifest
  (PostCSS/LightningCSS — custom properties, custom media queries, autoprefixer).
- Spacing scale, typography scale, and the vendored `_bulma-barebone` layout-helper
  framework are **Sass-sourced**, built by dedicated `npm run build:*` commands, and the
  generated `.css` is then pulled into the main chain — don't assume the whole `assets/css/`
  tree is PostCSS-only.
- SVG icons: individual files via `wp_rig()->get_svg_icon()`, or the generated sprite
  (`npm run build:svg-sprite`) via `get_svg_icon_from_sprite()`/`the_svg_icon_from_sprite()`.
- JS code-splitting: `elements-configuration.ts` is a dynamic-import hub guarded by DOM
  selector checks — add new page-specific JS there rather than a new global bundle.

## Change rules

- Never replace a file in the "Protected project customizations" list in `AGENTS.md`
  with a raw copy from upstream WP Rig — reconcile logic changes by hand.
- Verify narrow-command first (`npm run build:css`, `build:js`, etc.), then the full
  `npm run build`/`bundle`, before treating a change as done.
- `.git-prehooks/pre-commit` runs `lint:css` + `prettier:fix` + `git add -u`
  automatically on every commit — don't hand-format PHP/CSS/JS expecting it to stick if
  Prettier disagrees. One known formatter gotcha: `@prettier/plugin-php` strips the
  parentheses PHP <8.4 needs around `new ClassName()` when a fluent chain follows
  (`new Foo()->bar()` only parses on 8.4+) — avoid that pattern entirely (assign to a
  variable first) rather than relying on parens surviving a reformat.
- Don't run `npm run rig-init` on this checkout casually — it's meant to be run once,
  on a copy, to spin up a new client theme, not to rename the boilerplate itself.
