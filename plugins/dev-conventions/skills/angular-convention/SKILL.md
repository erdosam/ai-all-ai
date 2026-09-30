---
name: angular-convention
description: This skill should be used when creating, locating, or restructuring files in an Angular application, or writing any UI — for example when the user asks to "add a feature", "create a page", "add a shared component", "style a component", "use Angular Material", "where does this file belong", or "follow our Angular convention" — or when generating code under `src/app/core`, `src/app/features`, or `src/app/shared`. Covers the core/features/shared layout, feature grouping, import-direction rules, file naming, and the Angular Material UI requirement.
version: 0.3.0
---

# Angular Application Convention

Defines where a file belongs in an Angular application and what it is named. Applies to every Angular project in the stack; assumes standalone components, which have been the default since Angular v20.

## Core Concepts

### Core
App-wide singletons, wired once at bootstrap and never imported by `shared/`. Holds `guards/`, `interceptors/`, `services/`, `components/`, `constants/`, `enums/`, `models/`, and `utils/`. Core is the stateful, app-wide layer — authentication, layout chrome, HTTP interceptors.

### Feature
One business capability, owning its own `<feature>.routes.ts` plus `pages/`, `components/`, `models/`, and `services/`.

- Place routed components in `pages/`; place pieces used only by that feature in `components/`.
- Promote a component used by two features to `shared/` — do not import it across features.

### Feature group
An optional level above features, grouping features that share a domain (e.g. every voucher feature under a `loyalty` group). The group is **routed, not merely a folder**: `<group>.routes.ts` lazy-loads each child feature, so `app.routes.ts` gains one entry per group rather than one per feature, and each group becomes a real code-splitting boundary. Nest at most one group level; deeper nesting buys nothing and complicates route registration.

### Shared
Reusable and free of business logic: `components/`, `directives/`, `pipes/`. Shared code is stateless and knows nothing about any feature. Do not introduce a `SharedModule` barrel — it defeats tree-shaking by dragging every export into each consumer; standalone components import exactly what they use.

### UI (Angular Material)
All UI/UX — pages and shared presentational components alike — is built on [Angular Material](https://material.angular.io), not raw HTML/CSS primitives built from scratch. Import only the specific `Mat*Module`s a component actually uses (e.g. `MatButtonModule`, `MatChipsModule`), directly into that component's own `imports` array. Never introduce an aggregating `MaterialModule` barrel — that reintroduces the exact tree-shaking problem the `Shared` section rules out for a `SharedModule` barrel. Theme setup (palette, typography, density) lives in `styles/_theme.scss`.

Form fields default to floating labels. Set this once, app-wide, via `MAT_FORM_FIELD_DEFAULT_OPTIONS` in `app.config.ts` — never by repeating `floatLabel` on each `<mat-form-field>` instance:

```ts
import { MAT_FORM_FIELD_DEFAULT_OPTIONS } from '@angular/material/form-field';

export const appConfig: ApplicationConfig = {
  providers: [
    // ...other providers
    { provide: MAT_FORM_FIELD_DEFAULT_OPTIONS, useValue: { floatLabel: 'always' } },
  ],
};
```

## Layout

```
src/
  app/
    core/          guards/ interceptors/ services/ components/
                   constants/ enums/ models/ utils/
    features/
      <group>/     <group>.routes.ts — lazy-loads each child feature
        <feature>/ <feature>.routes.ts
          pages/       <page>/ — routed components
          components/  feature-scoped only
          models/  services/
    shared/        components/ directives/ pipes/
    app.ts  app.config.ts  app.routes.ts
  styles/          _theme.scss, _variables.scss, _mixins.scss — Material theme, tokens, and helpers
  styles.scss      imports the partials; keep it thin
public/            i18n/ images/ icons/ static/
```

## Import rules

These bind while **editing** an existing file, not only while adding a new one:

- `shared/` MUST NOT import from `features/` or `core/`.
- `core/` MUST NOT import from `features/`.
- A feature MUST NOT import from a sibling feature — promote the shared code to `shared/` or `core/` instead.

## Naming and lazy loading

- Drop the `.component.ts` / `.service.ts` suffixes — Angular v20+ names the file after the thing itself (`voucher-list.ts`). Guards, pipes, and interceptors keep a hyphenated suffix (`auth-guard.ts`).
- Name route constants in screaming snake case, after the feature or group: `VOUCHERS_ROUTES`.
- Lazy-load everywhere: `loadChildren` for groups and features, `loadComponent` for pages.
- Give pages external `.html` and `.scss` files, since they grow; keep shared presentational components inline, since they stay small.

## Workflow

1. Identify the group the capability belongs to, or decide it needs none.
2. Create the feature folder under `features/<group>/`, with its `pages/`, `components/`, `models/`, and `services/` subfolders.
3. Create `<feature>.routes.ts`, following `examples/feature.routes.ts`.
4. Add the routed component under `pages/<page>/`, following `examples/page.ts`.
5. Register the feature in `<group>.routes.ts` with `loadChildren` — creating that file and its `app.routes.ts` entry if this is the group's first feature.

If the project provides its own generators for these folders, prefer them over creating the tree by hand.

## Additional Resources

- `examples/feature.routes.ts` — a feature's routes file with a lazy-loaded page.
- `examples/page.ts` — routed page template with external template and styles.
- `examples/ui-component.ts` — shared presentational component with inline template and styles, built on Angular Material.
