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
    `/api/spa/admin/elevenlabs/voices` |
    `/api/spa/admin/elevenlabs/voices/${number}` |
    `/api/spa/admin/elevenlabs/voices${string}` |
    `/api/spa/signs/detailed${string}` |
    `/api/mobile/signs/detailed`