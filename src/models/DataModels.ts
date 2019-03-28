export interface Project {
    id: number;
    name: string;
    isOpen: boolean;
    medals: {
        gold: User;
        silver: User;
        bronze: User;
    }
}

export interface User {
    id: number;
    name: string;
}