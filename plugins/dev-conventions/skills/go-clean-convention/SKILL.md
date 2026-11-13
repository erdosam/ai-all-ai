---
name: go-clean-convention
description: This skill should be used when creating, locating, or restructuring files in a Go service, or scaffolding a new one — for example when the user asks to "start a new Go service", "add a use case", "add a repository", "where does this file belong", "scaffold a Go project", or "follow our Go clean architecture convention" — or when generating code under `internal/entity`, `internal/usecase`, `internal/repo`, `internal/controller`, or `pkg/`. Covers the layer layout, dependency direction, contracts and constructors, multi-binary repositories, and the Makefile workflow.
version: 0.1.0
---

# Go Clean Architecture Convention

Every Go service in the stack is built on [evrone/go-clean-template](https://github.com/evrone/go-clean-template). Defines where a file belongs, what it is named, and which direction imports may point.

**Start from an existing in-house service, not from upstream.** The template ships four transports (REST, gRPC, RabbitMQ RPC, NATS RPC), a demo domain, and proto/nginx tooling that most services delete on day one. Copy the scaffolding — `Makefile`, `.golangci.yml`, `Dockerfile`, `docker-compose.yml`, `config/`, `pkg/`, `migrations/README.md` — from the nearest existing service, then clone upstream into a scratch directory for reference only. Never clone it into the project.

Add a transport only when a spec calls for one. Deleting an unused server is routine; re-adding one is a decision.

## Core Concepts

### Layout

```
cmd/<binary>/         entry point; one directory per binary
config/               environment-only configuration
internal/
  app/                composition root — all wiring happens here, once
  entity/             domain types and sentinel errors
  usecase/            business logic; contracts.go + one package per domain
  repo/
    contracts.go      the interfaces use cases call
    persistent/       database repositories
    webapi/           outbound HTTP APIs and cloud SDKs
  controller/
    restapi/          handlers, middleware, versioned subpackage
migrations/           SQL migrations, up and down
pkg/                  transport-agnostic infrastructure
```

### Dependency direction

Imports point inward only. This is the one rule the whole convention exists to protect.

- `internal/entity` imports nothing from the module. Standard library only.
- A use case never imports a repository implementation, a controller, or a transport type — it calls outward through an interface declared in `internal/repo/contracts.go`.
- A controller never imports a repository.
- `pkg/` must not import `internal/`. That is what makes a package reusable, and it is why infrastructure shared between middleware and handlers lives in `pkg/` rather than in a controller package.

### Contracts and constructors

Two files declare every interface that crosses a layer: `internal/usecase/contracts.go` (what controllers call) and `internal/repo/contracts.go` (what use cases call). Both carry `go:generate` directives, so mocks are generated rather than written.

Constructors are always named `New`, take their dependencies as arguments, and **return the interface from `contracts.go`, never the concrete struct**. No package-level state — `gochecknoglobals` and `gochecknoinits` are on, so cross-cutting values such as a tenant or company identifier reach a repository by constructor injection, not a global and not a context value.

See `examples/contracts.go` and `examples/usecase.go`.

### Tracing decorators

Each `usecase/<domain>/` and `repo/persistent/<domain>/` package carries a `tracing.go` holding a decorator that wraps the implementation: `func New(...) usecase.X { return newTraced(&UseCase{...}) }`.

Adding a method to a contract therefore means implementing it in **three** places — the interface, the struct, and the decorator. Skipping the decorator surfaces as a compile error about an unsatisfied interface, which reads as unrelated. See `examples/tracing.go`.

### Multi-binary repositories

When one repository ships more than one deployable, **nest a full set of layers under each component** rather than splitting a single layer set by filename:

```
cmd/<binary-a>/  cmd/<binary-b>/
internal/
  <component-a>/   app/ entity/ usecase/ repo/ controller/
  <component-b>/   app/ entity/ usecase/ repo/ controller/
pkg/               shared infrastructure
```

Enforce the boundary with a `depguard` rule in `.golangci.yml`, not with review: each component's package tree is banned from importing any other's, and `pkg/` is banned from importing `internal/`. The components then talk to each other over their published interface — HTTP, or a separately versioned module — exactly as they would from separate repositories.

A package meant for consumption by *other* modules cannot live under `internal/`, which Go forbids outsiders to import. Give it its own `go.mod` at the repository root and its own version tags.

## Where code goes

Decide by asking what the code knows about.

| The code knows about… | It belongs in | Shape |
| --- | --- | --- |
| nothing but the domain | `internal/entity/<name>.go` | struct + methods |
| a domain rule spanning repositories | `internal/usecase/<domain>/<domain>.go` | method on `UseCase` |
| SQL, a table, a driver error code | `internal/repo/persistent/<domain>/<domain>.go` | method on `Repo` |
| an outbound HTTP API or cloud SDK | `internal/repo/webapi/<name>/` | method on that struct |
| an HTTP status, a route, a response envelope | `internal/controller/restapi/<version>/<domain>.go` | handler |
| a server, pool, client or middleware with no domain knowledge | `pkg/<name>/` | reusable package |

Naming: one file per domain per layer, named after the domain; package name matches the directory name; unexported package constants are prefixed with an underscore (`_tracerName`).

## Adding a domain

Work outward from the entity. Copy the shape from the most complete existing domain.

1. **Entity** — `internal/entity/<domain>.go`; new sentinels in `errors.go`
2. **Migration** — `make migrate-create <name>`, fill both `.up.sql` and `.down.sql`
3. **Repo contract** — add the interface to `internal/repo/contracts.go`
4. **Repo impl** — `internal/repo/persistent/<domain>/` with `<domain>.go` and `tracing.go`
5. **Use-case contract** — `internal/usecase/contracts.go`
6. **Use-case impl** — `internal/usecase/<domain>/` with `<domain>.go` and `tracing.go`
7. **`make mock`**, then a table-driven test using gomock and `t.Parallel()`
8. **Wiring** — a field and constructor call in `internal/app/app.go`
9. **Transport** — handler, a `mount<Domain>Routes` helper plus one call line in `NewRoutes`

Adding a use case to a domain that already exists is steps 1, 5, 6, 7 and 9 only.

Keep `NewRoutes` small by delegating to one `mount<Domain>Routes` helper per domain, so each new domain adds a single line and the function never approaches `funlen`'s cap.

## Error style

Domain failures are sentinels in `internal/entity/errors.go`. Everything else is wrapped with its layer path:

```go
fmt.Errorf("AccountUseCase - Create - uc.repo.Store: %w", err)
```

`err113` rejects `errors.New` and `fmt.Errorf` for new dynamic errors — add a sentinel instead. **Controllers are the only layer that turns a sentinel into a transport status**; a use case that returns an HTTP status has reached across a boundary.

## Commands — drive everything through the Makefile

| Task | Use this |
| --- | --- |
| Install tool binaries | `make bin-deps` |
| Unit tests (`-race`) | `make test` |
| Lint | `make linter-golangci` |
| Format (`go fix`, `gofumpt`, `gci`) | `make format` |
| Regenerate mocks | `make mock` |
| Tidy / verify modules | `make deps` |
| Start dependencies only | `make compose-up` |
| Full stack with hot reload | `make compose-up-all` |
| Create a migration | `make migrate-create <name>` |
| Full check before pushing | `make pre-commit` |

Never report a change as done without `make format`, `make linter-golangci` and `make test` passing.

Generated output — mocks, Swagger — is tracked in git and regenerated by `make pre-commit`, so editing it by hand is silently overwritten. Change the contract or the annotation, not the output.

Configuration is **environment-only**: a new setting needs a field in `config/config.go`, an entry in `.env.example`, and, if a container needs it, the `environment` block in `docker-compose.yml`. Fields marked `required` fail at startup rather than at first use.

## Additional Resources

- `examples/contracts.go` — layer-crossing interfaces with `go:generate` mockgen directives.
- `examples/usecase.go` — use-case implementation whose constructor returns the interface.
- `examples/tracing.go` — the traced decorator every domain package carries.
