# ai-all-ai

This repository is a Claude Code plugin marketplace. It hosts the `dev-conventions` plugin, which packages shared coding-convention skills for PHP8, Go, Flutter, TypeScript, and Angular projects.

## Plugin contents

- `plugins/dev-conventions/skills/general-convention/` — class/struct member ordering convention; applies to every language in the stack.
- `plugins/dev-conventions/skills/yii2-convention/` — Yii2 (advanced template) application architecture: modules, services, use cases (business logic models), and controllers.
- `plugins/dev-conventions/skills/angular-convention/` — Angular application file structure: the core/features/shared layout, feature grouping, import-direction rules, and file naming.

## Installing in another repo (on-demand skills)

In the target project, from its own Claude Code session:

```
claude plugin marketplace add <this-repo's-git-url>
claude plugin install dev-conventions@ai-all-ai-conventions
```

All skills then load automatically whenever Claude judges the task matches their description (e.g. "create a use case", "add a Yii2 controller action", "add an Angular feature").

## Force-loading `general-convention` in another repo's CLAUDE.md

Marketplace-installed plugin files live under a versioned cache path that shifts on every update, so a project's CLAUDE.md cannot reliably `@import` a marketplace-installed skill file directly. To force-load `general-convention` into every session of a target repo — rather than relying on Claude to trigger it on demand — copy the file into that repo and import the local copy:

1. Copy `plugins/dev-conventions/skills/general-convention/SKILL.md` from this repo into the target repo, e.g. as `.claude/skills/general-convention/SKILL.md`.
2. Add this line to the target repo's own CLAUDE.md:

```
@.claude/skills/general-convention/SKILL.md
```

`yii2-convention` and `angular-convention` are left as on-demand only (via the installed plugin), since each applies to one framework's code and doesn't need to be force-loaded every session.
