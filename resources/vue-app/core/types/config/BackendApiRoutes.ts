import { string } from "yup"

// This type used to ensure the validation of back-end API routes
export type BackendApiRoute =
    `/api/spa/admin/auth/login` |
    `/api/spa/admin/auth/user` |
    `/api/spa/admin/auth/logout` |
    `/api/spa/users` |
    `/api/spa/users${string}` |
    `/api/spa/users/${number}/` |
    `/api/spa/users/${number}/soft` |
    `/api/spa/users/${number}/restore` |
    `/api/spa/signs/detailed${string}` |
    `/api/mobile/signs/detailed` |
    `/spa/signs/detailed/${number}` |
    '/api/governorates' |
    '/api/roads' |
    '/api/spa/geojson/roads' |
    `/api/spa/signs/groups${string}`