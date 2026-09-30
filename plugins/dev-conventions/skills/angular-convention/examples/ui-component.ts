/**
 * A shared presentational component lives under `shared/components/`, holds no
 * business logic, and knows nothing about any feature. It stays small enough
 * for an inline template and inline styles. Inputs and outputs use the
 * `input()` and `output()` functions rather than decorators.
 *
 * UI is built on Angular Material: import only the specific `Mat*Module`s
 * this component uses, directly into its own `imports` array — never a
 * grouped `MaterialModule` barrel.
 */
import { Component, input, output } from '@angular/core';
import { MatChipsModule } from '@angular/material/chips';
import { MatIconModule } from '@angular/material/icon';

@Component({
  selector: 'app-badge',
  imports: [MatChipsModule, MatIconModule],
  template: `
    <mat-chip-set>
      <mat-chip (removed)="dismissed.emit()">
        {{ label() }}
        <button matChipRemove aria-label="Dismiss">
          <mat-icon>cancel</mat-icon>
        </button>
      </mat-chip>
    </mat-chip-set>
  `,
  styles: `
    :host {
      display: inline-flex;
    }
  `,
})
export class Badge {
  readonly label = input('');

  readonly dismissed = output<void>();
}
