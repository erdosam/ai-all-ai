# ai-all-ai

A Claude Code plugin marketplace hosting the `dev-conventions` plugin: shared coding-convention skills for PHP8, Go, Flutter, TypeScript, and Angular projects.

## What's included

The `dev-conventions` plugin bundles these skills:

- **`general-convention`** — applies to PHP8, Go, Flutter, and TypeScript. Class/struct member visibility and ordering: `public` → `protected` → `private`, constructor first.
- **`yii2-convention`** — applies to Yii2 (advanced template) PHP8 applications. Application/module layout, services, use cases (business logic models), controllers, HTTP-error ownership, and PHP8 constructor property promotion for injected dependencies.
- **`angular-convention`** — applies to Angular (v20+, standalone components). The core/features/shared file layout, feature grouping, import-direction rules, file naming, and the Angular Material UI requirement.

Each skill's `SKILL.md` (under `plugins/dev-conventions/skills/<name>/`) documents its convention in full, and ships an `examples/` folder with working code templates Claude follows when generating code.

## Installing in another repo

From the target project, in its own Claude Code session:

```
claude plugin marketplace add https://github.com/erdosam/ai-all-ai.git
claude plugin install dev-conventions@ai-all-ai-conventions
```

All three skills are then available in that project.

## Using the skills

**Automatic:** Claude loads a skill on its own whenever a request matches its description — e.g. "create a use case for X", "add an Angular feature", "add a shared component", "follow our coding convention".

**Explicit:** invoke a skill directly with its namespaced slash command (plugin skills are always namespaced as `<plugin-name>:<skill-name>`, to avoid colliding with other installed plugins' skills):

```
/dev-conventions:general-convention
/dev-conventions:yii2-convention
/dev-conventions:angular-convention
```

## Force-loading `general-convention` every session

Skills only load when Claude judges a task matches their description. To guarantee `general-convention` applies to *every* request in a target repo — rather than relying on automatic triggering — force-load it through that repo's own CLAUDE.md instead.

Marketplace-installed plugin files live under a versioned cache path that shifts on every plugin update, so a project's CLAUDE.md cannot reliably `@import` the installed copy directly. Vendor the file into the target repo and import that local copy instead:

1. Copy `plugins/dev-conventions/skills/general-convention/SKILL.md` from this repo into the target repo, e.g. as `.claude/skills/general-convention/SKILL.md`.
2. Add this line to the target repo's own CLAUDE.md:

```
@.claude/skills/general-convention/SKILL.md
```

`yii2-convention` and `angular-convention` are left as on-demand only, since each applies to one framework's code and doesn't need to be force-loaded every session.
