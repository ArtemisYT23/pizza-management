import { ApiError } from '@/types/api';

function getCookie(name: string): string | undefined {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);

    if (parts.length !== 2) {
        return undefined;
    }

    return decodeURIComponent(parts.pop()?.split(';').shift() ?? '');
}

type FetchOptions = Omit<RequestInit, 'body'> & {
    body?: Record<string, unknown> | FormData;
};

export async function apiFetch<T>(
    path: string,
    options: FetchOptions = {},
): Promise<T> {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(options.headers as Record<string, string>),
    };

    const xsrf = getCookie('XSRF-TOKEN');

    if (xsrf) {
        headers['X-XSRF-TOKEN'] = xsrf;
    }

    let body: string | FormData | undefined;

    if (options.body instanceof FormData) {
        body = options.body;
        delete headers['Content-Type'];
    } else if (options.body !== undefined) {
        headers['Content-Type'] = 'application/json';
        body = JSON.stringify(options.body);
    }

    const res = await fetch(`/api${path}`, {
        ...options,
        credentials: 'same-origin',
        headers,
        body,
    });

    if (res.status === 204) {
        return undefined as T;
    }

    const data: unknown = await res.json().catch(() => ({}));

    if (!res.ok) {
        const obj = data as {
            message?: string;
            errors?: Record<string, string[]>;
        };

        throw new ApiError(
            obj.message ?? res.statusText,
            res.status,
            obj.errors,
        );
    }

    return data as T;
}
