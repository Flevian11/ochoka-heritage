import { usePage } from '@inertiajs/react';

export function usePublicData<T extends Record<string, unknown>>(key: string, fallback: T): T {
    const props = usePage().props as Record<string, unknown>;
    const value = props[key];

    if (!value || typeof value !== 'object' || Array.isArray(value)) {
        return fallback;
    }

    return { ...fallback, ...(value as Partial<T>) };
}
