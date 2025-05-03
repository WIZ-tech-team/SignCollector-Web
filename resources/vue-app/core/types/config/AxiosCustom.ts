import { UserInterface } from "../data/UserInterface";

export type BackendResponseData = {
    status: string;
    data?: Object|[]|{user?:UserInterface, token?:string}|any;
    errors?: Object|[];
    message?: string;
}