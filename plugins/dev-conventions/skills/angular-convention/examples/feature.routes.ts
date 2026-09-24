/**
 * A feature owns one routes file, named after the feature. The constant is
 * screaming snake case, and every page is lazy-loaded with `loadComponent`.
 * A feature group's routes file has the same shape but uses `loadChildren`.
 */
import { Routes } from '@angular/router';

export const VOUCHERS_ROUTES: Routes = [
  {
    path: '',
    loadComponent: () => import('./pages/voucher-list/voucher-list').then((m) => m.VoucherList),
  },
  {
    path: 'detail',
    loadComponent: () => import('./pages/voucher-detail/voucher-detail').then((m) => m.VoucherDetail),
  },
];
