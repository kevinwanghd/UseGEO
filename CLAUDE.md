# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

UseGEO is a WordPress-first MVP site for GEO / AI visibility education, assessment, and lead capture. The live runtime is a custom theme, not the prototype-oriented source directories.

## AI Context Entry Point

**Read `docs/CONTEXT.md` before making any code changes.** It contains the full organizational memory index, knowledge loading strategy, and reverse-retrieval anchors.

Key context files:

| File | What it constrains |
|------|--------------------|
| `docs/CONTEXT.md` | Three-level quality funnel + context loading strategy |
| `docs/architecture.md` | System layers, dependencies, high-risk modules |
| `docs/interfaces.md` | Form routes, hooks, parameter contracts, security constraints |
| `docs/data-model.md` | Post types, meta fields, ER diagram, integrity constraints |
| `docs/decisions.md` | Design decisions WHY, implicit knowledge, change log, mistake books |
| `docs/rules.md` | Executable constraints + gate levels + maturity + detection methods |
| `docs/verification.md` | Verification workflow + SpecGate gate + quality-report output |
| `docs/templates/acceptance-spec.md` | Acceptance spec template (Spec Agent output) |
| `docs/templates/quality-report.md` | Quality report template (Quality Agent output) |
| `docs/templates/mistake-entry.md` | Mistake entry template (Memory Agent output) |
| `docs/templates/requirement-change.md` | Lightweight template for informal requirement changes |

## Source of Truth

- Runtime code: `wordpress/theme/usegeo/`
- `src/` is upstream/prototype space — **not** the runtime source of truth.
- `dist/` contains packaging artifacts — not editable source.

## Development Commands

No repo-local build, lint, or test commands. Useful commands:

- `git status --short` — inspect local changes
- `ls wordpress/theme/usegeo` — inspect the active theme

Validation is manual in a local or staging WordPress site:

- Homepage funnel: Homepage → Scanner → Lead form
- Content funnel: Article page → Sidebar CTA → Homepage scanner → Lead form
- Backend: confirm records in WP admin under UseGEO Leads / Case Submissions

## gstack

Use /browse from gstack for all web browsing. Never use mcp__claude-in-chrome__* tools.
