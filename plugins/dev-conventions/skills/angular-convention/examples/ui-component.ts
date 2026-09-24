/**
 * A shared presentational component lives under `shared/components/`, holds no
 * business logic, and knows nothing about any feature. It stays small enough
 * for an inline template and inline styles. Inputs and outputs use the
 * `input()` and `output()` functions rather than decorators.
 */
import { Component, input, output } from '@angular/core';

@Component({
  selector: 'app-badge',
  template: `
    <span class="badge">
      {{ label() }}
      <button type="button" (click)="dismissed.emit()" aria-label="Dismiss">&times;</button>
    </span>
  `,
  styles: `
    .badge {
      display: inline-flex;
    }
  `,
})
export class Badge {
  readonly label = input('');

  readonly dismissed = output<void>();
}
