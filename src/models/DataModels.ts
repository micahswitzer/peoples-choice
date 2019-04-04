export interface Project {
    id: number;
    name: string;
    isOpen: boolean;
    section_id: number;
    medals?: [];
}

export interface User {
    id: number;
    first_name: string;
    last_name: string;
    is_student: boolean;
    is_admin: boolean;
}

export interface Medal {
    value: number;
    name: string;
    color: string;
    project_id: number;
}
