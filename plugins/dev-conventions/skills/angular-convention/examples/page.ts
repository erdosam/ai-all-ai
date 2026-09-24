/**
 * A routed page lives under its feature's `pages/` folder, in a folder of its
 * own, and uses external template and style files because pages grow. The file
 * carries no `.component.ts` suffix, no `standalone: true`, and no explicit
 * `changeDetection` — the latter two are defaults in current Angular.
 */
import { Component, inject, signal } from '@angular/core';
import { VoucherService } from '../../services/voucher-service';

@Component({
  selector: 'app-voucher-list',
  templateUrl: './voucher-list.html',
  styleUrl: './voucher-list.scss',
})
export class VoucherList {
  readonly vouchers = signal<string[]>([]);

  private readonly service = inject(VoucherService);
}
