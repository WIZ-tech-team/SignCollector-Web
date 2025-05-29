<template>
  <div class="dashboard_view_container">
    <div class="">
      <!-- Main two-column area: fixed height = map height, centered vertically & horizontally -->
      <div class="flex items-start justify-between gap-4 h-[75vh] mt-4 main-height">

        <!-- Right half: table + pagination -->
        <div class="w-1/2 flex flex-col gap-4 h-full border border-1 border-gray-100 p-4 rounded-lg shadow-sm bg-white">
          <!-- Header Actions -->
          <div class="flex items-center gap-4 justify-between border-b-2 pb-3 border-gray-200">
            <!-- Filter -->
            <div class="flex items-center justify-start gap-4">
              <label class="text-md font-semibold">الحالة</label>
              <select v-model="completeSignFilter" class="border rounded px-1 py-1 text-sm">
                <option value="all">الكل</option>
                <option value="complete">المكتملة</option>
                <option value="not-complete">الغير مكتملة</option>
              </select>
            </div>

            <!-- Export Action -->
            <template v-if="authStore.canUser('export detailed signs')">
              <SignsExport></SignsExport>
            </template>

          </div>
          <!-- Scrollable, centered table area -->
          <div class="h-full overflow-auto">
            <table v-if="signsFiltered" class="min-w-full border-collapse">
              <thead>
                <tr class="bg-gray-100 text-xs">
                  <th @click.prevent="toggleRecordsSort()" class="p-2 border cursor-pointer">
                    المعرّف &uarr;&darr;
                  </th>
                  <th class="p-2 border">اسم اللوحة </th>
                  <th class="p-2 border">الولاية</th>
                  <th class="p-2 border">تاريخ إدخال البيانات</th>
                  <th class="p-2 border">مدخل البيانات</th>
                  <th class="p-2 border">الحالة</th>
                  <th class="p-2 border">الإجراء</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="sign in signsFiltered" :key="sign.id" @click="selectRow(sign)" :class="[
                  'cursor-pointer transition-colors',
                  selectedId === sign.id ? 'bg-blue-100' : 'hover:bg-gray-50'
                ]" class="text-sm">
                  <td class="p-2 border">{{ sign.id }}</td>
                  <td class="p-2 border">{{ sign.sign_name }}</td>
                  <td class="p-2 border">{{ sign.willayat }}</td>
                  <td class="p-2 border text-xxs">{{ formatDate(sign.created_at) }}</td>
                  <td class="p-2 border">{{ sign.created_by }}</td>
                  <td class="p-2 border">
                    <span class="w-full px-2 py-1 text-center font-medium rounded-full text-nowrap" :class="isComplete(sign)
                      ? 'bg-green-100 text-green-800 border-green-800'
                      : 'bg-red-100 text-red-800 border-red-800'">
                      {{ isComplete(sign) ? 'مكتملة' : 'غير مكتملة' }}
                    </span>
                  </td>
                  <td class="p-2 border">
                    <div class="flex items-center justify-center gap-2">
                      <button v-if="authStore.canUser('show detailed sign')" @click.stop="openModal(sign)" title="عرض"
                        class="text-primary hover:text-light-primary bg-light-primary hover:bg-primary p-2 rounded-md">
                        <!-- eye icon -->
                        <SolidHeroIcon name="EyeIcon" classes="w-5 h-5" />
                      </button>
                      <button v-if="authStore.canUser('update detailed sign')" @click.stop="openEditModal(sign)"
                        title="تعديل"
                        class="text-warning hover:text-light-warning bg-light-warning hover:bg-warning p-2 rounded-md">
                        <!-- pencil icon -->
                        <SolidHeroIcon name="PencilIcon" classes="w-5 h-5" />
                      </button>
                      <button v-if="authStore.canUser('delete detailed sign')" @click.stop="onDelete(sign)"
                        title="حذف"
                        class="text-danger hover:text-light-danger bg-light-danger hover:bg-danger p-2 rounded-md">
                        <!-- trash icon -->
                        <SolidHeroIcon name="TrashIcon" classes="w-5 h-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <!-- <nav v-if="signsPaginated && false" class="flex justify-center space-x-2 py-2">
            <button @click="changePage(signsPaginated.current_page - 1)" :disabled="!signsPaginated.prev_page_url"
              class="px-3 py-1 border rounded disabled:opacity-50">السابق</button>
            <button v-for="p in pageNumbers" :key="p" @click="changePage(p)"
              :class="['px-3 py-1 border rounded', p === signsPaginated.current_page ? 'bg-blue-500 text-white' : '']">{{
                p }}</button>
            <button @click="changePage(signsPaginated.current_page + 1)" :disabled="!signsPaginated.next_page_url"
              class="px-3 py-1 border rounded disabled:opacity-50">التالي</button>
          </nav> -->
        </div>

        <!-- Left half: toggles, map, lat/lng -->
        <div class="w-1/2 flex flex-col gap-4 h-full p-4 rounded-lg bg-white">
          <!-- Overlay Toggles -->
          <div class="flex items-center gap-2">
            <button @click="toggleMara" :class="[
              'gap-2 p-2 rounded-md border',
              maraEnabled ? 'bg-green-600 text-white' : 'bg-white text-gray-700'
            ]">
              <!-- {{ maraEnabled ? 'Hide' : 'Show' }} Parcels -->
              قطع الأراضي
            </button>
            <button @click="toggleNsdi" :class="[
              'gap-2 p-2 rounded-md border',
              nsdiEnabled ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'
            ]">
              <!-- {{ nsdiEnabled ? 'Hide' : 'Show' }} NSDI Roads -->
              مسارات الطرق
            </button>
          </div>

          <!-- Map Container -->
          <div class="flex-1 map-container rounded-md overflow-hidden shadow-sm bg-white shadow-gray-300">
            <div ref="mapRef" class="w-full h-full"></div>
          </div>

          <!-- Lat/Lng Display -->
          <!-- <div id="latlong" class="p-2 bg-white text-sm font-medium text-center">
            Lat: {{ lat }}, Lng: {{ lng }}
          </div> -->
        </div>

      </div>
    </div>

    <!-- SHOW MODAL -->
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center modal-z-index">
      <div @click.prevent="showModal = false" class="fixed inset-0 bg-black bg-opacity-50 w-full h-full"></div>
      <div class="bg-white w-11/12 h-5/6 rounded-lg shadow-lg relative overflow-hidden">
        <!-- Close -->
        <button type="button" @click.prevent="showModal = false"
          class="absolute z-50 top-2 right-4 text-gray-600 hover:text-gray-900">✕</button>

        <div class="flex h-full">
          <!-- Data Table -->
          <div class="w-1/3 p-4 py-8">
            <div class="flex justify-between items-center mb-4">
              <button @click="prevSign" :disabled="modalIndex === 0"
                class="px-2 py-1 bg-gray-200 text-gray-700 rounded-md disabled:opacity-50">
                <SolidHeroIcon name="ArrowRightIcon" classes="w-6 h-6" />
              </button>


              <h2 class="text-2xl font-bold px-4 py-2 rounded-md"
                :class="isComplete(activeSign) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                لوحة - {{ activeSign.id }}#
              </h2>

              <button @click="nextSign" :disabled="modalIndex === signsFiltered.length - 1"
                class="px-2 py-1 bg-gray-200 text-gray-700 rounded-md disabled:opacity-50">
                <SolidHeroIcon name="ArrowLeftIcon" classes="w-6 h-6" />
              </button>
            </div>

            <!-- Details Attributes -->
            <SignsGroupForm :signs-group="activeSign" :is-edit="false"></SignsGroupForm>
          </div>

          <!-- Image Slider -->
          <div v-if="imageUrls.length > 0" class="w-1/3 flex flex-col bg-gray-100 p-4 h-full">
            <div class="flex-1 relative overflow-hidden">
              <div v-for="(url, idx) in imageUrls" :key="idx"
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300"
                :class="idx === sliderIndex ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                <img :src="url as string" class="max-w-full max-h-full object-contain rounded" alt="sign image" />
              </div>
            </div>
            <div v-if="imageUrls?.length > 0" class="mt-2 flex gap-2 justify-center">
              <button class="gap-2 p-2 rounded-md border bg-white w-[4.5rem] disabled:opacity-50"
                :disabled="sliderIndex === 0" @click="sliderIndex--">السابق</button>
              <button class="gap-2 p-2 rounded-md border bg-white w-[4.5rem] disabled:opacity-50"
                :disabled="sliderIndex === imageUrls.length - 1" @click="sliderIndex++">التالي</button>
            </div>
          </div>
          <div v-else class="w-1/3 flex flex-col bg-gray-100 p-4 h-full">
            <span class="text-lg text-gray-500 text-center font-bold">
              لا يوجد صور لهذه اللائحة
            </span>
          </div>
          <!-- Map + Street View -->
          <div class="w-1/3 flex flex-col">
            <div ref="modalMapRef" class="h-1/2 w-full"></div>
            <div ref="modalStreetRef" class="h-1/2 w-full"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- EDIT MODAL -->
    <div v-if="showEditModal" class="fixed inset-0 flex items-center justify-center modal-z-index">
      <div @click.prevent="showEditModal = false" class="fixed inset-0 bg-black bg-opacity-50 w-full h-full"></div>
      <div class="bg-white w-11/12 h-5/6 rounded-lg shadow-lg relative overflow-hidden">
        <!-- Close -->
        <button @click="showEditModal = false"
          class="absolute top-2 right-4 text-gray-600 hover:text-gray-900">✕</button>

        <form @submit.prevent="submitEditUpdate" class="flex h-full">
          <!-- Left column: form inputs -->
          <div class="w-1/3 p-4 py-8 overflow-hidden space-y-4">
            <div class="flex justify-between items-center mb-4 w-full">
              <button @click.prevent="prevEditSign" :disabled="editModalIndex === 0"
                class="px-2 py-1 bg-gray-200 text-gray-700 rounded-md disabled:opacity-50">
                <SolidHeroIcon name="ArrowRightIcon" classes="w-6 h-6" />
              </button>

              <h2 class="text-2xl font-bold px-4 py-2 rounded-md"
                :class="isComplete(editActiveSign) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                لوحة - {{ editActiveSign.id }}#
              </h2>
              <button @click.prevent="nextEditSign" :disabled="editModalIndex === signsFiltered.length - 1"
                class="px-2 py-1 bg-gray-200 text-gray-700 rounded-md disabled:opacity-50">
                <SolidHeroIcon name="ArrowLeftIcon" classes="w-6 h-6" />
              </button>
            </div>
            <div class="overflow-auto h-[90%]">
                <SignsGroupForm :signs-group="editActiveSign" :is-edit="true"></SignsGroupForm>
            </div>
          </div>

          <!-- Middle: preview slider -->
          <div v-if="editActiveSign.images.length > 0" class="w-1/3 bg-gray-100 p-4 flex flex-col">
            <div class="flex-1 relative overflow-hidden">
              <div v-for="(image, idx) in editActiveSign.images" :key="idx"
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300"
                :class="idx === editSliderIndex ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                <div class="max-w-full max-h-full object-contain rounded relative">
                  <img :src="image.original_url" class="max-w-full max-h-full rounded border border-gray-300" alt="Preview" />
                  <button type="button" @click.prevent="deleteImage(image.id)" class="bg-danger text-light-danger hover:bg-light-danger hover:text-danger rounded-full p-1
                    border-danger absolute top-1 right-1">
                    <SolidHeroIcon name="TrashIcon" classes="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>
            <div v-if="editActiveSign.images.length" class="mt-2 flex justify-center space-x-2">
              <button type="button" class="gap-2 p-2 rounded-md border bg-white w-[4.5rem] disabled:opacity-50"
                :disabled="editSliderIndex === 0" @click="editSliderIndex--">السابق</button>
              <button type="button" class="gap-2 p-2 rounded-md border bg-white w-[4.5rem] disabled:opacity-50"
                :disabled="editSliderIndex === editActiveSign.images.length - 1"
                @click="editSliderIndex++">التالي</button>
            </div>
          </div>
          <div v-else class="w-1/3 flex flex-col bg-gray-100 p-4 h-full">
            <span class="text-lg text-gray-500 text-center font-bold">
              لا يوجد صور لهذه اللائحة
            </span>
          </div>

          <!-- Right: map & street view -->
          <div class="w-1/3 flex flex-col">
            <div ref="editModalMapRef" class="h-1/2 w-full"></div>
            <div ref="editModalStreetRef" class="h-1/2 w-full"></div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onBeforeMount, onMounted, watch, nextTick } from 'vue';
import { useSignsGroupsStore } from '@/store/stores/signsGroupsStore';
import { useAuthStore } from '@/store/stores/authStore';
import ApiService from '@/core/services/ApiService';
import Swal from 'sweetalert2';
import signsList from '@/assets/json/signs_updated.json'           // ← your uploaded JSON
import { MSwal, QSwal } from '@/core/plugins/SweetAlerts2';
import { AxiosError, AxiosResponse } from 'axios';
import { BackendResponseData } from '@/core/types/config/AxiosCustom';
import { getMessageFromObj } from '@/assets/ts/swalMethods';
import SolidHeroIcon from '@/components/icons/SolidHeroIcon.vue';
import SignsExport from '@/components/partials/SignsExport.vue';
import SignsGroupForm from '@/components/form/forms/SignsGroupForm.vue';
import { SignsGroup } from '@/core/types/data/SignsGroup';

// index of the currently displayed sign in signsPaginated.data
const modalIndex = ref<number | null>(null);
const editModalIndex = ref<number | null>(null);

const mandatoryFields = ref([
  'sign_name',
  'sign_code',
  'sign_code_gcc',
  'sign_type',
  'sign_shape',
  'road_direction',
  'signs_count',
  'columns_description',
  'sign_location_from_road',
  'sign_base',
  'distance_from_road_edge_meter',
  'sign_column_radius_mm',
  'column_height',
  'column_colour',
  'column_type',
  'sign_condition',
  'sign_color'
])

const sortById = ref<'asc' | 'desc'>('asc')
const completeSignFilter = ref<'complete' | 'not-complete' | 'all'>('all')
const signsFiltered = computed<SignsGroup[]>(() => {
  let arr: SignsGroup[]
  if (completeSignFilter.value === 'all')
    arr = signsPaginated.value?.data ?? [];
  else if (completeSignFilter.value === 'complete')
    arr = signsPaginated.value?.data ? signsPaginated.value.data.filter(sign => isComplete(sign)) : []
  else
    arr = signsPaginated.value?.data ? signsPaginated.value.data.filter(sign => !isComplete(sign)) : []

  if (sortById.value === 'asc')
    arr = arr.sort((a, b) => a.id - b.id)
  else
    arr = arr.sort((a, b) => b.id - a.id)

  return arr
})

const signCodeMap: Record<string, any> = signsList.reduce((map, s: any) => {
  map[s.Sign_Name] = s
  return map
}, {})

//
// Helper: format dates in table
//
function formatDate(dateString: string) {
  let isoString = dateString.replace(" ", "T") + "Z";
  const d = new Date(isoString);
  let date = d.toLocaleDateString();
  let time = `${d.getHours()}:${d.getMinutes()}`
  return date + ' ' + time
}

//
// STORES & PAGINATION
//
const signsGroupsStore = useSignsGroupsStore();
const authStore = useAuthStore();

const signsPaginated = computed(() => signsGroupsStore.signsGroupsPaginated);

//
// TABLE SELECTION
//
const selectedId = ref<number | null>(null);
function selectRow(sign: SignsGroup) {
  selectedId.value = sign.id;
  if (!map) return;
  const la = parseFloat(sign.latitude as any);
  const ln = parseFloat(sign.longitude as any);
  if (isNaN(la) || isNaN(ln)) return;
  const pos = new google.maps.LatLng(la, ln);
  map.panTo(pos);
  map.setZoom(17);
  marker?.setPosition(pos);
}

//
// SHOW MODAL
//
const showModal = ref(false);
const activeSign = ref<SignsGroup>({} as SignsGroup);
const sliderIndex = ref(0);
const imageUrls = computed(() =>
  Array.isArray(activeSign.value?.image_urls)
    ? activeSign.value.image_urls
    : []
);

// build the tableData for the show-modal
const modalMapRef = ref<HTMLDivElement | null>(null);
const modalStreetRef = ref<HTMLDivElement | null>(null);
let mapm: google.maps.Map;
let markerm: google.maps.Marker;
let panoramam: google.maps.StreetViewPanorama;

function openModal(sign: SignsGroup) {
  modalIndex.value = signsPaginated.value!.data.findIndex(s => s.id === sign.id);

  activeSign.value = sign;
  sliderIndex.value = 0;
  showModal.value = true;
  nextTick(initModalViews);
}

function initModalViews() {
  if (!modalMapRef.value || !modalStreetRef.value) return;
  const la = parseFloat(activeSign.value.latitude as any);
  const ln = parseFloat(activeSign.value.longitude as any);
  if (isNaN(la) || isNaN(ln)) return;
  const pos = new google.maps.LatLng(la, ln);

  mapm = new google.maps.Map(modalMapRef.value, { center: pos, zoom: 17 });
  markerm = new google.maps.Marker({ position: pos, map: mapm });
  panoramam = new google.maps.StreetViewPanorama(modalStreetRef.value, {
    position: pos,
    pov: { heading: 0, pitch: 0 }
  });
}

//
// EDIT MODAL
//
const showEditModal = ref(false);
const editActiveSign = ref<SignsGroup>({} as SignsGroup);
const editSliderIndex = ref(0);
const editFilesToUpload = ref<File[]>([]);

const editModalMapRef = ref<HTMLDivElement | null>(null);
const editModalStreetRef = ref<HTMLDivElement | null>(null);
let editMap: google.maps.Map;
let editMarker: google.maps.Marker;
let editPanorama: google.maps.StreetViewPanorama;

function openEditModal(sign: SignsGroup) {
  editModalIndex.value = signsPaginated.value!.data.findIndex(s => s.id === sign.id);

  editActiveSign.value = { ...sign };
  editSliderIndex.value = 0;
  editFilesToUpload.value = [];
  showEditModal.value = true;
  nextTick(initEditModalViews);
}

function initEditModalViews() {
  if (!editModalMapRef.value || !editModalStreetRef.value) return;
  const la = parseFloat(editActiveSign.value.latitude as any);
  const ln = parseFloat(editActiveSign.value.longitude as any);
  if (isNaN(la) || isNaN(ln)) return;
  const pos = new google.maps.LatLng(la, ln);

  editMap = new google.maps.Map(editModalMapRef.value, { center: pos, zoom: 17 });
  editMarker = new google.maps.Marker({ position: pos, map: editMap });
  editPanorama = new google.maps.StreetViewPanorama(editModalStreetRef.value, {
    position: pos,
    pov: { heading: 0, pitch: 0 }
  });
}

async function submitEditUpdate() {
  const id = editActiveSign.value.id;
  const form = new FormData();
  for (const k in editActiveSign.value) {
    if (k === 'id' || k === 'image_urls') continue;
    form.append(k, (editActiveSign.value as any)[k] ?? '');
  }
  editFilesToUpload.value.forEach(f => form.append('files[]', f));

  QSwal.fire('تحديث اللوحة ؟', 'سيتم تحديث بيانات اللوحة.', 'question')
    .then(async (result) => {
      if (result.isConfirmed) {

        try {
          ApiService.setHeader(authStore.token as string);
          // form.append('_method', 'PATCH');
          await ApiService.post(`/api/spa/signs/groups/${id}`, form);
          await Swal.fire('تم!', 'تم تحديث اللوحة بنجاح.', 'success');
        } catch (err: any) {
          await Swal.fire('خطأ!', err.message || 'فشل التحديث.', 'error');
        } finally {
          await signsGroupsStore.fetchSignsGroupsPaginated();
          await loadGoogle();
          await initMap();
          watch(signsPaginated, () => placeAllSignMarkers());
        }

      }
    })
}

//
// DELETE
//
async function onDelete(sign: SignsGroup) {
  const res = await Swal.fire({
    title: 'تأكيد الحذف',
    text: `هل أنت متأكد أنك تريد حذف اللائحة "${sign.id}"؟`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'نعم، احذفها',
    cancelButtonText: 'إلغاء'
  });
  if (!res.isConfirmed) return;
  try {
    ApiService.setHeader(authStore.token as string, 'application/json');
    await ApiService.delete(`/api/spa/signs/groups/${sign.id}`);
    await Swal.fire('تم الحذف', 'تم حذف اللوحة بنجاح.', 'success');
    await signsGroupsStore.fetchSignsGroupsPaginated(signsPaginated.value!.current_page);
  } catch (err: any) {
    await Swal.fire('خطأ', err.message || 'Delete failed.', 'error');
  }
}

//
// COMPLETENESS CHECK
//
function isComplete(sign: SignsGroup): boolean {
  return mandatoryFields.value.every(key => {
    // Handle both direct and nested properties
    const value = (key as string).split('.').reduce((o: any, k) => o?.[k], sign);

    // Check for null or undefined
    if (value == null) return false;

    // Check for empty string
    if (typeof value === 'string' && value.trim() === '') return false;

    // Check for empty array
    if (Array.isArray(value) && value.length === 0) return false;

    // Numbers (including 0) are considered valid
    // Objects with properties are considered valid
    return true;
  });
}

//
// — Google Map & Overlays
//
const mapRef = ref<HTMLDivElement | null>(null);
let map: google.maps.Map | null = null;
let marker: google.maps.Marker | null = null;
const markers = ref<google.maps.Marker[]>([]);
const maraEnabled = ref(false);
const nsdiEnabled = ref(false);

async function loadGoogle() {
  if ((window as any).google?.maps) return;
  await new Promise<void>(res => {
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBJs7FlVKhtppCAUasLTo4CBebnFMvENb4&libraries=geometry';
    s.onload = () => res();
    document.head.append(s);
  });
}

function newMaraOverlay() {
  return new google.maps.ImageMapType({
    getTileUrl(c, z) {
      const y = 2 ** z - 1 - c.y;
      return `https://gis.mara.gov.om/gs/geoserver/mara/gwc/service/tms/1.0.0/mara:PARCELS@EPSG:900913@png/${z}/${c.x}/${y}.png`;
    },
    tileSize: new google.maps.Size(256, 256),
    opacity: 0.7,
    isPng: true
  });
}

function newNsdiOverlay() {
  return new google.maps.ImageMapType({
    getTileUrl(c, z) {
      const proj = map!.getProjection()!;
      const scale = 2 ** z, ts = 256;
      const sw = proj.fromPointToLatLng(new google.maps.Point(c.x * ts / scale, (c.y + 1) * ts / scale))!;
      const ne = proj.fromPointToLatLng(new google.maps.Point((c.x + 1) * ts / scale, c.y * ts / scale))!;
      const bbox = `${sw.lng()},${sw.lat()},${ne.lng()},${ne.lat()}`;
      return `https://nsdiapps.ncsi.gov.om/arcgis1/rest/services/Geoportal/Transportation/MapServer/export?bbox=${bbox}&bboxSR=4326&imageSR=3857&format=png&transparent=true&size=256,256&f=image`;
    },
    tileSize: new google.maps.Size(256, 256),
    opacity: 0.8,
    isPng: true
  });
}

function rebuildOverlays() {
  if (!map) return;
  map.overlayMapTypes.clear();
  if (maraEnabled.value) map.overlayMapTypes.insertAt(0, newMaraOverlay());
  if (nsdiEnabled.value) map.overlayMapTypes.insertAt(1, newNsdiOverlay());
}

function toggleMara() { maraEnabled.value = !maraEnabled.value; rebuildOverlays(); }
function toggleNsdi() { nsdiEnabled.value = !nsdiEnabled.value; rebuildOverlays(); }

const lat = ref<number | string>(22.5409934)
const lng = ref<number | string>(55.8908847)

const roadsGeojson = ref<any>()

async function initMap() {
  if (!mapRef.value) return;
  map = new google.maps.Map(mapRef.value, {
    center: { lat: 23.585, lng: 57.996 },
    zoom: 6,
    mapTypeId: 'hybrid'
  });

  await fetchRoadsGeojson().finally(() => {
    if (roadsGeojson.value && map?.data) {
      map.data.addGeoJson(roadsGeojson.value)
    }
  })

  //marker = new google.maps.Marker({ position: map.getCenter()!, map });
  rebuildOverlays();
  placeAllSignMarkers();
  map.addListener('center_changed', () => {
    const c = map!.getCenter()!;
    lat.value = c.lat().toFixed(6);
    lng.value = c.lng().toFixed(6);
    // document.getElementById('latlong')!
    //   .innerText = `Lat: ${c.lat().toFixed(6)} | Lng: ${c.lng().toFixed(6)}`;
  });
}

async function fetchRoadsGeojson() {
  await ApiService.get('/api/spa/geojson/roads')
    .then((res) => {
      if (res.data?.status === 'success') {
        roadsGeojson.value = res.data?.data
        console.log('Roads GeoJSON data:\n', roadsGeojson.value);

      }
    })
    .catch((err) => {
      console.log('erroe loading roads geojson file:\n', err);
    })
}

function placeAllSignMarkers() {
  if (!map) return;
  markers.value.forEach(m => m.setMap(null));
  markers.value.splice(0);
  for (const s of signsPaginated.value?.data || []) {
    const la = parseFloat(s.latitude as any),
      ln = parseFloat(s.longitude as any);
    if (isNaN(la) || isNaN(ln)) continue;
    const m = new google.maps.Marker({
      position: { lat: la, lng: ln },
      map,
      icon: {
        path: 'M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z',
        scale: 1.35,
        fillColor: isComplete(s) ? '#00ae42' : '#b00101',
        fillOpacity: 1,
        strokeWeight: 1,
        strokeColor: '#fff'
      }
    });
    m.addListener('click', () => {
      const inf = new google.maps.InfoWindow({
        content: `
          <div style="margin: 4px auto; text-align: center;">Sign#${s.id}</div>
          <button id="show-${s.id}" style="margin: 4px;">Show</button>
          <button id="edit-${s.id}" style="margin: 4px;">Edit</button>`
      });
      inf.open(map, m);
      google.maps.event.addListenerOnce(inf, 'domready', () => {
        document.getElementById(`show-${s.id}`)
          ?.addEventListener('click', () => { openModal(s); inf.close(); });
        document.getElementById(`edit-${s.id}`)
          ?.addEventListener('click', () => { openEditModal(s); inf.close(); });
      });
    });
    markers.value.push(m);
  }
}

onBeforeMount(() => signsGroupsStore.fetchSignsGroupsPaginated());
onMounted(async () => {
  await signsGroupsStore.fetchSignsGroupsPaginated();
  await loadGoogle();
  await initMap();
  watch(signsPaginated, () => placeAllSignMarkers());
});


function prevSign() {
  if (modalIndex.value! > 0) {
    const prev = signsFiltered.value[--modalIndex.value!];
    openModal(prev);
  }
}
function nextSign() {
  if (modalIndex.value! < signsFiltered.value.length - 1) {
    const nxt = signsFiltered.value[++modalIndex.value!];
    openModal(nxt);
  }
}

function prevEditSign() {
  if (editModalIndex.value! > 0) {
    const prev = signsFiltered.value[--editModalIndex.value!];
    openEditModal(prev);
  }
}
function nextEditSign() {
  if (editModalIndex.value! < signsFiltered.value.length - 1) {
    const nxt = signsFiltered.value[++editModalIndex.value!];
    openEditModal(nxt);
  }
}

const toggleRecordsSort = () => {
  sortById.value = (sortById.value === 'asc') ? 'desc' : 'asc'
}

const deleteImage = (image_id: number) => {
  QSwal.fire('حذف الصورة ؟', 'سيتم حذف هذه الصورة من تفاصيل اللائحة.', 'question')
    .then(async result => {
      if (result.isConfirmed) {
        await ApiService.delete(`/api/spa/signs/groups/${editActiveSign.value.id}/images/${image_id}`)
          .then((res: AxiosResponse<BackendResponseData>) => {
            if (res.data?.status === 'success') {
              MSwal.fire('تم', 'تم حذف الصورة بنجاح.', 'success')
              showEditModal.value = false
            } else {
              MSwal.fire('رد غير متوقع !', getMessageFromObj(res), 'error')
            }
          })
          .catch((err: AxiosError<BackendResponseData>) => {
            MSwal.fire('خطأ !', getMessageFromObj(err), 'error')
          })
          .finally(async () => {
            await signsGroupsStore.fetchSignsGroupsPaginated();
            await loadGoogle();
            await initMap();
          })
      }
    })
}

</script>




<style scoped>
/*.dashboard_view_container {
  min-height: 100vh;
}*/

.cursor-pointer {
  cursor: pointer;
}

.main-height {
  height: calc(100vh - 9.25rem);
}

.modal-z-index {
  z-index: 1050;
}

.text-xxs {
  font-size: smaller;
}

/* 
.label-btn {
  @apply p-2 m-2 bg-light-brand text-brand hover:bg-brand hover:text-light-brand;
} */
</style>
