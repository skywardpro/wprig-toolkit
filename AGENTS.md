# WP Rig Toolkit — AI Agents Guide

Welcome, AI Agent! This is **wprig-toolkit**, a personal WP Rig boilerplate — a base
for forking new client themes, not a finished site. It follows upstream WP Rig's
conventions plus a set of project-specific extensions listed below. Follow these
core pillars.

---

### 1. ONBOARDING & STATE PROTOCOL

Before starting, check `.ai/agent-state.md` for your status:

- **If Pending:** Follow [**The Onboarding Guide**](.ai/ONBOARDING.md) (run `npm run ai:setup` once, read [**Developer Directions**](.ai/developer-directions.md), read/initialize [**Project Rules**](.ai/PROJECT_RULES.md), and mark state as Completed).
- **If Completed:** Read [**Project Rules**](.ai/PROJECT_RULES.md) for any dynamic, self-learned theme conventions, and keep it updated with new architectural decisions. Proceed directly to the user's task. Do NOT re-run setup.

### 2. THIS IS A BOILERPLATE, NOT A SITE

- Do not add client-specific content, branding, or one-off business logic here.
- A new client theme is created by running `npm run rig-init` (renames the
  namespace/slug/text-domain) and/or `npm run setup-child`, not by editing this
  checkout in place to look like a finished site.

### 3. PROTECTED PROJECT CUSTOMIZATIONS

This fork diverged from upstream WP Rig around tag `v3.1` and has since built up its
own conventions. **Never replace these with a raw upstream file** — reconcile by hand
if upstream has a relevant fix:

- `functions.php` (the `DISABLE_*`/`ENABLE_*` constants block) and every
  `functions/_functions-*.php` **except** `_functions-rig-init.php` (that one is
  upstream's own bootstrap, relocated here — safe to sync with upstream's
  `functions.php` bootstrap logic).
- `inc/Blocks/Component.php` and all of `inc/WP_Rig_Toolkit/*` (12 wrapper components
  for third-party JS libraries — GSAP, Swiper, iMask, Validate.js, etc. — toggled via
  the `ENABLE_*` constants and registered through the `wprig_theme_components` filter
  in `functions/_functions-toolkit-components.php`, since they live one directory
  level too deep for `Theme.php`'s component auto-discovery to find them).
- The SVG sprite methods on `inc/Template_Tags.php` (`get_svg_icon*`, `get_svg_sprite*`).
- `front-page.php`, `template-parts/forms/*`, `template-parts/sections/*`,
  `template-parts/header/mobile-menu-toggle.php`, `optional/dev/dev-proxy-livereload.php`.
- `assets/data/`, `assets/fonts/`, the spacing-system/typography-system/framework
  (Bulma-barebone) Sass sources under `assets/css/src/`, and all of
  `assets/{css,js}/vendor/*`.
- The toolkit-only build scripts: `scripts/build-spacing.js`, `scripts/build-typography.js`,
  `scripts/build-svg-sprite.js`, and their `npm run build:*`/`dev:*` script entries.
- `.git-prehooks/`, `.prettierrc`, `.prettierignore`.

### 4. ARCHITECTURE & BUILD PIPELINE

- **Source Files Only:** NEVER edit compiled artifacts (`.min.css`, `.min.js`, generated
  spacing/typography CSS, the SVG sprite). Edit source files, then run the relevant
  `npm run build:*` command. See [**Architecture Skill**](.ai/skills/architecture/SKILL.md).
- **Scaffolding Tooling:** Use `npm run create-rig-component` for new components (stock
  or `WP_Rig_Toolkit/*`-style) and the `block:*` commands for Gutenberg blocks, rather
  than manually bootstrapping files. See [**Component Registry**](.ai/skills/component-registry/SKILL.md).
- **Mixed CSS pipeline:** the main stylesheet chain is PostCSS/LightningCSS, but
  spacing/typography/framework partials are Sass-sourced and built separately — see
  `.ai/PROJECT_RULES.md` before assuming one pipeline for a given file.

### 5. CONTRACT-FIRST DEVELOPMENT

- Do not modify source files without an approved plan. You must author a `SPEC.md` in `.ai/plans/` and ask clarifying questions first to reach a >95% confidence score. See [**Feature Planning Skill**](.ai/skills/feature-planning/SKILL.md).

### 6. CONFIGURATION FIRST

- Reference `config/config.default.json` / `config/config.json` before making build or architectural changes. `theme.slug`/`theme.PHPNamespace` are intentionally left at stock `wp-rig`/`WP_Rig\WP_Rig` values on this checkout — see Pillar 2.

### 7. PRE-FLIGHT QUALITY CHECK

- Run `npm run ai:check` before submitting. On this checkout it runs `lint:css && lint:js` (the E2E/screenshot legs from upstream's version are omitted until the Playwright suite under `tests/e2e/` has toolkit-specific specs — see [**Code Quality skill**](.ai/skills/code-quality-standards/SKILL.md)).

---

## AI Agent Skill Directory

Refer to [**.ai/SKILLS.md**](.ai/SKILLS.md) for a comprehensive directory of specialized skills, including:

- **Foundational Pillars:** Architecture, Feature Planning, Code Quality.
- **Design & UI:** Styles, Typography, Gutenberg Blocks.
- **Logic & Backend:** Advanced Templating, WP-CLI, PHP Filters.
- **Quality & Workflow:** Testing (PHPUnit/E2E), Modern Dev Workflow, Child Theme Development.

## Capabilities & Tooling

- **WP Rig Docs (MCP)**: Run `npm run mcp` to access the Model Context Protocol server.
- **Verification**: Run `npm run ai:check` for linting (see Pillar 7 for what's actually wired up on this checkout).
- **Migration record**: See `BUILD-MIGRATION.md` for the 2026-09-14 sync with upstream WP Rig 3.4.2 — what was adopted, adapted, or skipped, and why.
