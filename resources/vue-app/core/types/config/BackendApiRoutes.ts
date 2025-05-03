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
    `/api/spa/admin/statistics/users` |
    `/api/spa/admin/statistics/subscriptions` |
    `/api/spa/admin/statistics/subscriptions/year` |
    `/api/spa/admin/elevenlabs/voices` |
    `/api/spa/admin/elevenlabs/voices/${number}` |
    `/api/spa/admin/elevenlabs/voices${string}` |
    `/api/spa/admin/elevenlabs/agents` |
    `/api/spa/admin/elevenlabs/agents/${number}` |
    `/api/spa/admin/elevenlabs/agents${string}` |
    `/api/spa/admin/reports/subscriptions/all` |
    `/api/spa/admin/reports/subscriptions/export`
