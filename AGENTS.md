# Agent Instructions

This repository contains the OpenGraph.xyz WordPress plugin.

## Repository Skills

Reusable agent workflows live in `.agents/skills/`. Read the relevant `SKILL.md` when its workflow applies:

- [WordPress plugin release](.agents/skills/wordpress-plugin-release/SKILL.md): Runtime asset placement, packaging rules, deployment workflows, and WordPress.org release checks.
- [Bump version](.agents/skills/bump-version/SKILL.md): Plugin version updates, readme stable tags, changelog entries, and release commands.

## GitHub Actions

Deployment workflows live in `.github/workflows/`. Keep GitHub Actions workflows there; agent instructions and reusable workflows belong in `AGENTS.md` and `.agents/skills/`.
