# Developer Directions for AI Agents

This file is a dedicated space for the theme developer to define project-specific directions, guidelines, styling rules, or behavioral constraints for all AI agents working on this project.

> [!TIP]
> **Theme Developer:** Fill out the sections below to permanently guide any AI agent assisting you. Agents will automatically read this file during onboarding and must strictly adhere to these instructions.

---

## 🎯 What this repository is

This is **wprig-toolkit**, a personal WP Rig boilerplate — not a finished site.
It exists to be forked (via `npm run rig-init` / `npm run setup-child`) into
new client themes. Keep it generic and toggleable:

- Do not add client-specific business logic, content, or branding here.
- New optional features should be added as a toggleable component (see
  `.ai/skills/create-component/SKILL.md` and the `WP_Rig_Toolkit/*` pattern
  below), not hard-coded into a template.

---

## 🎨 Theme Identity & Aesthetic Guidelines

_Define the look and feel, color palettes, typography, spacing preferences, or design systems you want the agent to maintain._

- **Brand Colors:** _(not applicable at the boilerplate level — defined per client theme)_
- **Typography:** Base type rules live in `assets/css/src/typography/`; the generated
  `_typography-system.css` is built from a Sass source via `npm run build:typography`.
- **Spacing / Grid:** Generated utility scale in `assets/css/src/spacing-system/`, built
  from a Sass source via `npm run build:spacing`. Don't hand-edit the generated `.css`.
- **Design Tokens:** Check `config/config.default.json`/`config/themeConfig.js` first
  before hardcoding colors or spacing.

---

## 💻 Coding Conventions & Structural Overrides

_Specify PHP, CSS, or JS rules that are unique to this theme, or override standard WP Rig behaviors._

- **PHP Standards:** Feature flags are `define()`d as `DISABLE_*`/`ENABLE_*` constants at
  the top of root `functions.php` — see `AGENTS.md` for the full convention.
- **CSS Architecture:** Mixed, on purpose — the main pipeline is PostCSS/LightningCSS
  (custom properties, custom media), but the spacing/typography systems and the vendored
  `_bulma-barebone` framework are Sass-sourced and built separately. See
  `.ai/skills/styles/SKILL.md` before touching either.
- **JavaScript Rules:** TypeScript for new code where practical; `elements-configuration.ts`
  is the dynamic-import hub for page-specific JS, guarded by DOM selector checks.
- **Build Processes:** `npm run dev` / `npm run build` / `npm run bundle` — see `CLAUDE.md`
  for the full command list, including the toolkit-only `build:spacing`/`build:typography`/
  `build:svg-sprite` commands that don't exist in stock WP Rig.

---

## 🚀 Project Priorities & Roadmap

_List your current focus, high-priority features, planned refactoring, or specific do-not-touch areas._

- **Immediate Focus:** _(fill in per session)_
- **Planned Enhancements:** _(fill in per session)_
- **Strict Constraints:** Never replace `inc/WP_Rig_Toolkit/*`, `functions/_functions-*.php`
  (except `_functions-rig-init.php`), or the SVG sprite / forms / spacing-system code with
  upstream WP Rig files. See the "Protected project customizations" list in `AGENTS.md`.

---

## 📝 Custom Guidelines / Miscellaneous

_Any other specific requests, API keys to mock, testing setups, or special environment notes._

- This checkout was migrated from a plain, historyless file tree to a git repo with a
  full upstream remote during the 2026-09-14 architecture sync — see `BUILD-MIGRATION.md`.
