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

export interface Medal {
    value: number;
    name: string;
    color: string;
    project_id: number;
}
