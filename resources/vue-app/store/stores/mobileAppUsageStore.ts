import { ref } from "vue";
import { defineStore } from "pinia";

// Types
export type AppUsage = {
    id: number;
    usersNumber: number;
    duration: number;
    date: string;
    interactions: number;
}


export const useMobileAppUsageStore = defineStore('mobileAppUsageStore', () => {

    const usages = ref<AppUsage[]>([
        {
          id: 1,
          usersNumber: 25,
          duration: 12543,
          date: "2025-02-05",
          interactions: 233
        },
        {
          id: 2,
          usersNumber: 23,
          duration: 21245,
          date: "2025-02-06",
          interactions: 332
        },
        {
          id: 3,
          usersNumber: 33,
          duration: 17583,
          date: "2025-02-07",
          interactions: 309
        },
        {
          id: 4,
          usersNumber: 40,
          duration: 27735,
          date: "2025-02-08",
          interactions: 396
        },
        {
          id: 4,
          usersNumber: 36,
          duration: 23679,
          date: "2025-02-09",
          interactions: 328
        }
      ]);

    return { usages };

});