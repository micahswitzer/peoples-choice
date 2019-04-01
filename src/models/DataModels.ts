export interface Project {
    id: number;
    name: string;
    status_open: boolean;
    section_id: number;
    medals?: [];
}

export interface User {
    id: number;
    name: string;
}
