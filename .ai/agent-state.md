# WP Rig Toolkit Agent State

This file tracks the onboarding and setup status of AI agents in this workspace to prevent redundant setup execution and unnecessary resource usage.

> [!NOTE]
> AI Agents: If `Onboarding Status` is marked as **Completed**, do NOT run `npm run ai:setup` or repeat initial exploration tasks. Proceed directly to the user's requested task.

## Onboarding Status

- **Status**: Incomplete
- **Last Agent**:
- **Last Updated**:

## Completed Steps

- [] Initial environment check
- [] AI Setup (`npm run ai:setup`)
- [] Codebase architectural mapping
- [] Read Developer Directions (`.ai/developer-directions.md`)

## Agent Log

### 2026-09-14 - Migration to wprig-master 3.4.2 architecture

Synced this boilerplate's build tooling and PHP architecture (component
auto-discovery, `Asset_Provider`, `Versioning_Trait`) with upstream WP Rig
3.4.2, and adopted this `.ai/` knowledge base, `AGENTS.md`/`CLAUDE.md`,
`.rig-config.json`, the Playwright/PHPUnit/Lighthouse test stack, the MCP
doc server, and the Open Component Registry CLI. See `BUILD-MIGRATION.md`
for the full record of what was adopted, adapted, or deliberately skipped.
A filesystem incident during the final move-to-live-location step destroyed
this repo's incremental git commit history (not the working-tree content) -
see the note at the end of `BUILD-MIGRATION.md`.
