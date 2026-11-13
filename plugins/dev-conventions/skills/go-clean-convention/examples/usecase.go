// A use case orchestrates repositories through the interfaces in
// internal/repo/contracts.go and knows nothing about transports.
// The constructor is named New, takes its dependencies as arguments
// (including cross-cutting values such as a company identifier, which must
// never be a global or a context value), and returns the INTERFACE from
// contracts.go wrapped in its traced decorator.
package account

import (
	"context"
	"fmt"

	"example.com/service/internal/entity"
	"example.com/service/internal/repo"
	"example.com/service/internal/usecase"
)

type UseCase struct {
	repo      repo.AccountRepo
	companyID string
}

func New(r repo.AccountRepo, companyID string) usecase.Account {
	return newTraced(&UseCase{repo: r, companyID: companyID})
}

func (uc *UseCase) Create(ctx context.Context, a entity.Account) (entity.Account, error) {
	if err := a.Validate(); err != nil {
		// A domain failure is a sentinel from internal/entity/errors.go,
		// returned unwrapped so callers can errors.Is against it.
		return entity.Account{}, err
	}

	created, err := uc.repo.Store(ctx, a)
	if err != nil {
		// Everything else is wrapped with its layer path.
		return entity.Account{}, fmt.Errorf("AccountUseCase - Create - uc.repo.Store: %w", err)
	}

	return created, nil
}
