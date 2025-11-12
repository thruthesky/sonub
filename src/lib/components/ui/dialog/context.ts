import type { Writable } from 'svelte/store';

export type DialogContext = {
  openStore: Writable<boolean>;
  setOpen(value: boolean): void;
};

export const dialogContextKey = Symbol('ui-dialog-context');
