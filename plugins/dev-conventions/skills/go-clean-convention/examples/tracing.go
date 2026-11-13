// Every usecase/<domain>/ and repo/persistent/<domain>/ package carries a
// tracing.go like this one. Adding a method to a contract means implementing
// it in THREE places -- the interface, the struct, and this decorator.
// Omitting it fails to compile as an unsatisfied interface, which reads as an
// unrelated error, so mirror the method here in the same commit.
package account

import (
	"context"

	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/trace"

	"example.com/service/internal/entity"
	"example.com/service/internal/usecase"
)

// Unexported package constants are prefixed with an underscore.
const _tracerName = "usecase/account"

type traced struct {
	next   usecase.Account
	tracer trace.Tracer
}

func newTraced(next usecase.Account) usecase.Account {
	return &traced{next: next, tracer: otel.Tracer(_tracerName)}
}

func (t *traced) Create(ctx context.Context, a entity.Account) (entity.Account, error) {
	ctx, span := t.tracer.Start(ctx, "Account.Create")
	defer span.End()

	return t.next.Create(ctx, a)
}

func (t *traced) GetByID(ctx context.Context, id string) (entity.Account, error) {
	ctx, span := t.tracer.Start(ctx, "Account.GetByID")
	defer span.End()

	return t.next.GetByID(ctx, id)
}
