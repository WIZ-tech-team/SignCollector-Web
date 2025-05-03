// Type for validating the system routes
export type SystemRoute =
    '/' |
    AuthRoute |
    DashboardRoute

// Type for validating the auth layout routes
export type AuthRoute =
    `/sign-in`


// Type for validating the dashboard layout routes
export type DashboardRoute = 
`/dashboardLayout` |
`/dashboard` |
`/users` |
`/reports/usage` |
`/reports/subscriptions` |
`/elabs-config` |
`/elabs-config/voices` |
`/elabs-config/agents`
