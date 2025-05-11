// src/store/stores/detailedSignsStore.ts
import { ref } from "vue";
import { defineStore } from "pinia";
import ApiService from "@/core/services/ApiService";
import type { AxiosResponse } from "axios";
import type { PaginatedData } from "@/core/types/data/PaginatedDataInterface";
import type { DetailedSign } from "@/core/types/data/DetailedSign";
import type { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { MSwal } from "@/core/plugins/SweetAlerts2";
import { getMessageFromObj } from "@/assets/ts/swalMethods";

export const useDetailedSignsStore = defineStore('detailedSignsPaginatedStore', () => {
  // this needs to match PaginatedData<T>:
  const detailedSignsPaginated = ref<PaginatedData<DetailedSign>>();

  /**
   * Fetch page `page` of your SPA detailed-signs endpoint, transform Laravel's
   * { data, links, meta } into PaginatedData
   */
  async function fetchDetailedSignsPaginated(page = 1) {
    detailedSignsPaginated.value = undefined;

    try {
      const res: AxiosResponse<BackendResponseData> = await ApiService.get(
        `/api/spa/signs/detailed?page=${page}`
      );

      if (res.data.status !== 'success' || !res.data.data) {
        throw new Error(getMessageFromObj(res.data));
      }

      // Laravel's default paginator returns { data: [...], links: {...}, meta: {...} }
      const { data, links, meta } = res.data;

      detailedSignsPaginated.value = {
        data,
        current_page: meta.current_page,
        last_page: meta.last_page,
        prev_page_url: links.prev,
        next_page_url: links.next
      };

    } catch (e: any) {
      console.error(e);
      MSwal.fire(
        'Error loading signs',
        e.message || 'Unexpected error',
        'error'
      );
    }
  }

  return {
    detailedSignsPaginated,
    fetchDetailedSignsPaginated,
  };
});
