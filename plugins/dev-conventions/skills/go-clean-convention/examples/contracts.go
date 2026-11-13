// Every interface that crosses a layer is declared in a contracts.go, never
// beside its implementation: internal/usecase/contracts.go holds what
// controllers call, internal/repo/contracts.go holds what use cases call.
// Mocks are generated from these directives -- run `make mock` after a change.
package usecase

import (
	"context"

	"example.com/service/internal/entity"
)

//go:generate mockgen -source=contracts.go -destination=mocks_usecase_test.go -package=usecase_test

type (
	// Account is the interface controllers call. It speaks in entity types
	// only -- no transport DTOs, no SQL rows.
	Account interface {
		Create(ctx context.Context, a entity.Account) (entity.Account, error)
		GetByID(ctx context.Context, id string) (entity.Account, error)
	}
)
