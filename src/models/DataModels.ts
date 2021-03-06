export const UrlRoot: string = process.env.VUE_APP_ROOT_URL;

export interface Project {
    id: number;
    name: string;
    isOpen: number;
}

export interface User {
    first_name: string;
    last_name: string;
    linux_name: string;
    is_student: boolean;
    is_admin: boolean;
}

export interface SystemUser {
    id: string | null;
    full_name: string;
    is_student: boolean;
    is_admin: boolean;
}

export interface UserList {
    [key: string]: User;
}

export interface Medal {
    value: number;
    name: string;
    color: string;
    project_id: number;
}

export interface TeamList {
    [key: string]: string[];
}

export interface MedalList {
    [key: string]: Array<{
        team_id: string;
        score: string;
    }>;
}

export interface ProjectVotes {
    [key: string]: Array<{
        points: string;
        count: string;
    }>;
}

export interface WriteIn {
    id: string;
    team_id: string;
    message: string;
}
