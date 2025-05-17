import 'vue-router';
import { UserType } from '@/core/types/data/Admin';

declare module 'vue-router' {
    interface RouteMeta {
        auth?: boolean;
        allowedUsers?: UserType[];
        pageTitle?: string;
        permissions?: string[];
    }
}