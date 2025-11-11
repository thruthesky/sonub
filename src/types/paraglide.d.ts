declare module '$lib/paraglide/messages.js' {
  export type MessageFunction = (inputs?: Record<string, unknown>) => string;
  export const m: Record<string, MessageFunction>;
  const messages: Record<string, MessageFunction>;
  export default messages;
}

declare module '$lib/paraglide/messages' {
  export type MessageFunction = (inputs?: Record<string, unknown>) => string;
  export const m: Record<string, MessageFunction>;
  const messages: Record<string, MessageFunction>;
  export default messages;
}

declare module '$lib/paraglide/runtime' {
  export function getLocale(): string;
  export function setLocale(locale: string, options?: { reload?: boolean }): void;
  export const locales: string[];
  export function deLocalizeUrl(url: URL): URL;
}

declare module '$lib/paraglide/runtime.js' {
  export function getLocale(): string;
  export function setLocale(locale: string, options?: { reload?: boolean }): void;
  export const locales: string[];
}


declare module '$lib/paraglide/server' {
  export function paraglideMiddleware(
    request: Request,
    hook: (context: { request: Request; locale: string }) => Response | Promise<Response>
  ): Promise<Response>;
}
