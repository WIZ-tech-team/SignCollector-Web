export interface PaginatedData<T> {
    current_page: number;
    data: T[] | any[];
    first_page_url: string;
    from: number;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number;
    total: number;
}

export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginatedDataStyle2<T> {
    "data": T[];
    "links": {
        "first": string | null;
        "last": string | null;
        "prev": string | null;
        "next": string | null;
    },
    "meta": {
        "current_page": number;
        "from": number;
        "last_page": number;
        "links": PaginationLink[];
        "path": string | null;
        "per_page": number;
        "to": number;
        "total": number;
    };
    "status": string;
}