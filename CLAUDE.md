# ai-all-ai

This repository is a Claude Code plugin marketplace. It hosts the `dev-conventions` plugin, which packages shared coding-convention skills for PHP8, Go, Flutter, TypeScript, and Angular projects.

See [README.md](README.md) for how another repo installs and uses this plugin (marketplace add/install, automatic vs. explicit skill invocation, and force-loading `general-convention` via CLAUDE.md).

## Plugin contents

- `plugins/dev-conventions/skills/general-convention/` — class/struct member ordering convention; applies to every language in the stack.
- `plugins/dev-conventions/skills/yii2-convention/` — Yii2 (advanced template) application architecture: modules, services, use cases (business logic models), and controllers.
- `plugins/dev-conventions/skills/angular-convention/` — Angular application file structure: the core/features/shared layout, feature grouping, import-direction rules, and file naming.
