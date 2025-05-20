// src/store/stores/detailedSignsStore.ts
import { ref } from "vue";
import { defineStore } from "pinia";
import ApiService from "@/core/services/ApiService";
import type { AxiosResponse } from "axios";
import type { PaginatedDataStyle2 } from "@/core/types/data/PaginatedDataInterface";
import type { BackendResponseData } from "@/core/types/config/AxiosCustom";
import { MSwal } from "@/core/plugins/SweetAlerts2";
import { getMessageFromObj } from "@/assets/ts/swalMethods";
import { SignsGroup } from "@/core/types/data/SignsGroup";

export const useSignsGroupsStore = defineStore('signsGroupsStore', () => {
  // this needs to match PaginatedData<T>:
  const signsGroupsPaginated = ref<PaginatedDataStyle2<SignsGroup>>();

  /**
   * Fetch page `page` of your SPA detailed-signs endpoint, transform Laravel's
   * { data, links, meta } into PaginatedData
   */
  async function fetchSignsGroupsPaginated(page = 1) {
    signsGroupsPaginated.value = undefined;

    try {
      const res: AxiosResponse<BackendResponseData> = await ApiService.get(
        `/api/spa/signs/groups?page=${page}`
      );

      if (res.data.status !== 'success' || !res.data.data) {
        throw new Error(getMessageFromObj(res.data));
      }

      // Laravel's default paginator returns { data: [...], links: {...}, meta: {...} }
      const { data, links, meta } = res.data as PaginatedDataStyle2<SignsGroup>;

      signsGroupsPaginated.value = {
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
    signsGroupsPaginated,
    fetchSignsGroupsPaginated,
  };
});
