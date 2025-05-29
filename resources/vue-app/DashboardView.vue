<template>
  <div>
    <!-- Main two-column area: fixed height = map height, centered vertically & horizontally -->
    <div class="flex h-[400px] items-center justify-center gap-4">
    
      <!-- Right half: table + pagination -->
      <div class="w-1/2 flex flex-col gap-4 h-full">
        <!-- Scrollable, centered table area -->
        <div class="flex-1 overflow-auto flex items-center justify-center">
          <table v-if="signsPaginated" class="min-w-full border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="p-2 border">ID</th>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Governorate</th>
                <th class="p-2 border">Created At</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="sign in signsPaginated.data"
                :key="sign.id"
                @click="selectRow(sign)"
                :class="[
                  'cursor-pointer transition-colors',
                  selectedId === sign.id ? 'bg-blue-100' : 'hover:bg-gray-50'
                ]"
              >
                <td class="p-2 border">{{ sign.id }}</td>
                <td class="p-2 border">{{ sign.sign_name }}</td>
                <td class="p-2 border">{{ sign.governorate }}</td>
                <td class="p-2 border">{{ formatDate(sign.created_at) }}</td>
<td
  class="p-2 border text-center font-medium"
  :class="isComplete(sign)
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'"
>
  {{ isComplete(sign) ? 'Complete' : 'Incomplete' }}
</td>
                <td class="p-2 border">
                  <div class="flex justify-center space-x-2">
                    <button @click.stop="openModal(sign)" title="عرض">
                      <!-- eye icon -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 hover:text-green-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.522 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.478 0-8.268-2.943-9.542-7z" />
                      </svg>
                    </button>
                    <button @click.stop="openEditModal(sign)" title="تعديل">
                      <!-- pencil icon -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-600 hover:text-yellow-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M16.5 3.5l4 4L11 17l-4 1 1-4 9.5-9.5z" />
                      </svg>
                    </button>
                    <button @click.stop="onDelete(sign)" title="حذف">
                      <!-- trash icon -->
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 hover:text-red-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1z" />
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <nav v-if="signsPaginated" class="flex justify-center space-x-2 py-2">
          <button
            @click="changePage(signsPaginated.current_page - 1)"
            :disabled="!signsPaginated.prev_page_url"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >Previous</button>
          <button
            v-for="p in pageNumbers"
            :key="p"
            @click="changePage(p)"
            :class="['px-3 py-1 border rounded', p === signsPaginated.current_page ? 'bg-blue-500 text-white' : '']"
          >{{ p }}</button>
          <button
            @click="changePage(signsPaginated.current_page + 1)"
            :disabled="!signsPaginated.next_page_url"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >Next</button>
        </nav>
      </div>
      <!-- Left half: toggles, map, lat/lng -->
      <div class="w-1/2 flex flex-col gap-4 h-full">
        <!-- Overlay Toggles -->
        <div class="flex items-center space-x-2">
          <button
            @click="toggleMara"
            :class="[
              'px-3 py-1 rounded border',
              maraEnabled ? 'bg-green-600 text-white' : 'bg-white text-gray-700'
            ]"
          >
            {{ maraEnabled ? 'Hide' : 'Show' }} Parcels
          </button>
          <button
            @click="toggleNsdi"
            :class="[
              'px-3 py-1 rounded border',
              nsdiEnabled ? 'bg-blue-600 text-white' : 'bg-white text-gray-700'
            ]"
          >
            {{ nsdiEnabled ? 'Hide' : 'Show' }} NSDI Roads
          </button>
        </div>

        <!-- Map Container -->
        <div class="flex-1 map-container rounded-md overflow-hidden">
          <div ref="mapRef" class="w-full h-full"></div>
        </div>

        <!-- Lat/Lng Display -->
        <div id="latlong" class="p-2 bg-white text-sm font-medium text-center">
          Lat: {{ lat }}, Lng: {{ lng }}
        </div>
      </div>

    </div>

        <pre class="whitespace-pre-wrap break-all">{{ JSON.stringify(activeSign, null, 2) }}</pre>

    <!-- SHOW MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-white w-11/12 h-5/6 rounded-lg shadow-lg relative overflow-hidden">
        <!-- Close -->
        <button
          @click="showModal = false"
          class="absolute top-4 right-4 text-gray-600 hover:text-gray-900"
        >✕</button>

        <div class="flex h-full">
          <!-- Data Table -->
          <div class="w-1/3 p-4 overflow-auto">
            <h2 class="text-2xl font-bold mb-4"></h2>
            <table class="w-full text-sm">
              <tbody>
                <tr v-for="(value, key) in tableData" :key="key" class="border-b">
                  <td class="font-semibold py-1">{{ formatKey(key) }}</td>
                  <td class="py-1">{{ value }}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Image Slider -->
          <div class="w-1/3 flex flex-col bg-gray-100 p-4 h-full">
            <div class="flex-1 relative overflow-hidden">
              <div
                v-for="(url, idx) in imageUrls"
                :key="idx"
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300"
                :class="idx === sliderIndex ? 'opacity-100' : 'opacity-0 pointer-events-none'"
              >
                <img
                  :src="url"
                  class="max-w-full max-h-full object-contain rounded"
                  alt="sign image"
                />
              </div>
            </div>
            <div class="mt-2 flex space-x-2 justify-center">
              <button
                class="px-2 py-1 bg-white border rounded disabled:opacity-50"
                :disabled="sliderIndex === 0"
                @click="sliderIndex--"
              >Prev</button>
              <button
                class="px-2 py-1 bg-white border rounded disabled:opacity-50"
                :disabled="sliderIndex === imageUrls.length - 1"
                @click="sliderIndex++"
              >Next</button>
            </div>
          </div>
          <!-- Map + Street View -->
          <div class="w-1/3 flex flex-col">
            <div ref="modalMapRef" class="h-1/2 w-full"></div>
            <div ref="modalStreetRef" class="h-1/2 w-full"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

 <div
    v-if="showEditModal"
    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
  >
    <div class="bg-white w-11/12 h-5/6 rounded-lg shadow-lg relative overflow-hidden">
      <!-- Close -->
      <button
        @click="showEditModal = false"
        class="absolute top-4 right-4 text-gray-600 hover:text-gray-900"
      >✕</button>

      <form @submit.prevent="submitEditUpdate" class="flex h-full">
        <!-- Left column: form inputs -->
        <div class="w-1/3 p-4 overflow-auto space-y-4">
          <h2 class="text-2xl font-bold"></h2>

          <!-- 1. ID (read-only) -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">ID</label>
            <input type="text" :value="editActiveSign.id" readonly class="border rounded px-2 py-1 bg-gray-100"/>
          </div>

        <!-- 2. Latitude / Longitude (read-only) -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Latitude</label>
            <input type="text" :value="editActiveSign.latitude" readonly class="border rounded px-2 py-1 bg-gray-100"/>
          </div>
          <div class="flex flex-col">
            <label class="font-medium mb-1">Longitude</label>
            <input type="text" :value="editActiveSign.longitude" readonly class="border rounded px-2 py-1 bg-gray-100"/>
          </div>

              <!-- 3. Signs Count (select 0–4) -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Signs Count</label>
            <select v-model.number="editActiveSign.signs_count" class="border rounded px-2 py-1">
              <option v-for="n in 5" :key="n-1" :value="n-1">{{ n-1 }}</option>
            </select>
          </div>
           <!-- 4. Columns Description -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Columns Description</label>
  <select v-model="editActiveSign.columns_description" class="border rounded px-2 py-1">
    <!-- optional placeholder -->
    <option disabled value="">— اختر وصف الأعمدة —</option>
    <option
      v-for="opt in columnOptions"
      :key="opt"
      :value="opt"
      :selected="editActiveSign.columns_description === opt"
    >
      {{ opt }}
    </option>
  </select>
</div>

           <!-- 5–12. Road fields (all read-only) -->
          <div v-for="key in ['road_classification','road_name','road_number','road_type','road_direction','governorate','willayat','village','sign_location_from_road']" 
               :key="key" class="flex flex-col">
            <label class="font-medium mb-1">{{ formatKey(key) }}</label>
            <input type="text" :value="editActiveSign[key]" readonly class="border rounded px-2 py-1 bg-gray-100"/>
          </div>
            <!-- 13. Sign Base -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Sign Base</label>
            <select v-model="editActiveSign.sign_base" class="border rounded px-2 py-1">
           
              <option
              value="بقاعدة اسمنتية"
      :selected="editActiveSign.sign_base === 'بقاعدة اسمنتية'"
      >بقاعدة اسمنتية</option>
      <option
              value="بدون قاعدة"
      :selected="editActiveSign.sign_base === 'بدون قاعدة'"
      >بدون قاعدة</option>
            </select>
          </div>
          <!-- 14–17 Number entries -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Distance from Road Edge (m)</label>
            <input v-model.number="editActiveSign.distance_from_road_edge_meter" type="number" class="border rounded px-2 py-1"/>
          </div>
          <div class="flex flex-col">
            <label class="font-medium mb-1">Sign Column Radius (mm)</label>
            <input v-model.number="editActiveSign.sign_column_radius_mm" type="number" class="border rounded px-2 py-1"/>
          </div>
          <div class="flex flex-col">
            <label class="font-medium mb-1">Column Height (m)</label>
            <input v-model.number="editActiveSign.column_height" type="number" class="border rounded px-2 py-1"/>
          </div>

          <!-- 18. Column Colour -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Column Colour</label>
            <select v-model="editActiveSign.column_colour" class="border rounded px-2 py-1">
              <option
              value="رمادي"
      :selected="editActiveSign.column_colour === 'رمادي'"
      >رمادي</option>
           <option
              value="ابيض و  اسود"
      :selected="editActiveSign.column_colour === 'ابيض و  اسود'"
      ></option>
          <option
              value="أخرى"
      :selected="editActiveSign.column_colour === 'أخرى'"
      >أخرى</option>
       <option
              value="ممتسح"
      :selected="editActiveSign.column_colour === 'ممتسح'"
      >ممتسح</option>
         
            </select>
          </div>

          <!-- 19. Column Type -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Column Type</label>
            <select v-model="editActiveSign.column_type" class="border rounded px-2 py-1">
             <option
      value="خشب"
      :selected="editActiveSign.column_type === 'خشب'"
    >خشب</option>
    <option
      value="حديد"
      :selected="editActiveSign.column_type === 'حديد'"
    >حديد</option>
            </select>
          </div>

 <!-- 20. Sign Name -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Sign Name</label>
            <select v-model="editActiveSign.sign_name" class="border rounded px-2 py-1">
              <option v-for="opt in signNameOptions" :key="opt" :value="opt"       :selected="editActiveSign.sign_name === opt"
>{{ opt }}</option>
            </select>
          </div>

<!-- Sign Code -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Code (2010)</label>
  <select
    v-model="editActiveSign.sign_code"
    class="border rounded px-2 py-1"
  >
    <option disabled value="">— اختر رمز اللوحة —</option>
    <option
      v-for="code in signCodeOptions"
      :key="code"
      :value="code"
       :selected="editActiveSign.sign_code === code
    >{{ code }}</option>
  </select>
</div>

<!-- Sign Code GCC (auto-filled, read-only) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Code GCC</label>
  <input
    v-model="editActiveSign.sign_code_gcc"
    readonly
    class="border rounded px-2 py-1 bg-gray-100"
  />
</div>

<!-- Sign Type (auto-filled, read-only) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Type</label>
  <input
    v-model="editActiveSign.sign_type"
    readonly
    class="border rounded px-2 py-1 bg-gray-100"
  />
</div>

<!-- Sign Shape (auto-filled, read-only) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Shape</label>
  <input
    v-model="editActiveSign.sign_shape"
    readonly
    class="border rounded px-2 py-1 bg-gray-100"
  />
  <!-- Sign Length (number) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Length (m)</label>
  <input
    v-model.number="editActiveSign.sign_length"
    type="number"
    step="any"
    class="border rounded px-2 py-1"
  />
</div>

<!-- Sign Width (number) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Width (m)</label>
  <input
    v-model.number="editActiveSign.sign_width"
    type="number"
    step="any"
    class="border rounded px-2 py-1"
  />
</div>

<!-- Sign Radius (number) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Radius (m)</label>
  <input
    v-model.number="editActiveSign.sign_radius"
    type="number"
    step="any"
    class="border rounded px-2 py-1"
  />
</div>

<!-- Sign Color (select) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Color</label>
  <select v-model="editActiveSign.sign_color" class="border rounded px-2 py-1">
    <option disabled value="">— اختر لون اللوحة —</option>
    <option value="أزرق">أزرق</option>
    <option value="أبيض">أبيض</option>
    <option value="أخضر">أخضر</option>
    <option value="بني">بني</option>
    <option value="أحمر">أحمر</option>
    <option value="أصفر">أصفر</option>
    <option value="لون أخر">لون أخر</option>
    <option value="متعدد الألوان">متعدد الألوان</option>
  </select>
</div>

<!-- Content Shape Description (text) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Content Shape Description</label>
  <input
    v-model="editActiveSign.sign_content_shape_description"
    type="text"
    class="border rounded px-2 py-1"
  />
</div>

<!-- Content Arabic Text (text) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">(المحتوى) نص عربي</label>
  <input
    v-model="editActiveSign.sign_content_arabic_text"
    type="text"
    class="border rounded px-2 py-1"
  />
</div>

<!-- Content English Text (text) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">(Content) English Text</label>
  <input
    v-model="editActiveSign.sign_content_english_text"
    type="text"
    class="border rounded px-2 py-1"
  />
</div>

<!-- Sign Condition (select) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Sign Condition</label>
  <select v-model="editActiveSign.sign_condition" class="border rounded px-2 py-1">
    <option disabled value="">— اختر حالة اللوحة —</option>
    <option value="جيدة">جيدة</option>
    <option value="متوسطة">متوسطة</option>
    <option value="سيئة">سيئة</option>
  </select>
</div>

<!-- Comments (textarea) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Comments</label>
  <textarea
    v-model="editActiveSign.comments"
    rows="3"
    class="border rounded px-2 py-1"
  ></textarea>
</div>

<!-- Created By (read-only) -->
<div class="flex flex-col">
  <label class="font-medium mb-1">Created By</label>
  <input
    v-model="editActiveSign.created_by"
    type="text"
    readonly
    class="border rounded bg-gray-100 px-2 py-1"
  />
</div>

          <!-- File picker -->
          <div class="flex flex-col">
            <label class="font-medium mb-1">Replace Images</label>
            <input type="file" multiple @change="handleEditFiles" class="border rounded p-1"/>
          </div>

          <button
            type="submit"
            class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
          >
            Save Changes
          </button>
        </div>

        <!-- Middle: preview slider -->
        <div class="w-1/3 bg-gray-100 p-4 flex flex-col">
          <div class="flex-1 relative overflow-hidden">
            <div
              v-for="(url, idx) in editImageUrls"
              :key="idx"
              class="absolute inset-0 flex items-center justify-center transition-opacity duration-300"
              :class="idx === editSliderIndex ? 'opacity-100' : 'opacity-0 pointer-events-none'"
            >
              <img
                :src="url"
                class="max-w-full max-h-full object-contain rounded"
                alt="Preview"
              />
            </div>
          </div>
          <div class="mt-2 flex justify-center space-x-2">
            <button
              type="button"
              class="px-2 py-1 bg-white border rounded disabled:opacity-50"
              :disabled="editSliderIndex === 0"
              @click="editSliderIndex--"
            >Prev</button>
            <button
              type="button"
              class="px-2 py-1 bg-white border rounded disabled:opacity-50"
              :disabled="editSliderIndex === editImageUrls.length - 1"
              @click="editSliderIndex++"
            >Next</button>
          </div>
        </div>

        <!-- Right: map & street view -->
        <div class="w-1/3 flex flex-col">
          <div ref="editModalMapRef" class="h-1/2 w-full"></div>
          <div ref="editModalStreetRef" class="h-1/2 w-full"></div>
        </div>
      </form>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref, computed, onBeforeMount, onMounted, watch, nextTick } from 'vue';
import { useDetailedSignsStore } from '@/store/stores/detailedSignsStore';
import { useAuthStore } from '@/store/stores/authStore';
import ApiService from '@/core/services/ApiService';
import Swal from 'sweetalert2';
import { DetailedSign } from '@/core/types/data/DetailedSign';
import signsList from '@/assets/json/signs_updated.json'           // ← your uploaded JSON
// 1️⃣ Build an array of codes and a lookup map
const signCodeOptions = computed<string[]>(() =>
  signsList.map((s: any) => s.Sign_Code_2010)
)

const signCodeMap: Record<string, any> = signsList.reduce((map, s: any) => {
  map[s.Sign_Code_2010] = s
  return map
}, {})
const columnOptions = [
  'عمود واحد بلوحة واحدة',
  'عمود واحد بلوحتين',
  'عمود واحد بثلاث لواحات',
  'عمود واحد ب 4 لوحات',
  'عمودين بلوحة واحدة',
  'عمودين بلوحتين',
  'عمودين بثلاث لوحات',
  'عمودين بأربع لوحات',
  'ثلاثة أعمدة بلوحة واحدة',
  'ثلاثة أعمدة بلوحتين',
  'ثلاثة أعمدة بثلاث لوحات',
  'ثلاثة أعمدة بأربع لوحات',
  'أربع أعمدة بلوحة واحدة',
  'أربع أعمدة بلوحتين',
  'أربع أعمدة بثلاث لوحات',
  'أربع أعمدة بأربع لوحات',
  'عمود بدون لوحة',
  'عمودين بدون لوحة',
  '3أعمدة بدون لوحة',
  '4أعمدة بدون لوحة',
  'لوحة مثبتة في الجسر'
];
const signNameOptions = [
  'Left Bend',
  'Right Bend',
  'Series Of Bends First Left',
  'Series Of Bends First Right',
  'Steep Descent',
  'Steep Ascent',
  'Carriageway Narrows (Both Sides)',
  'Carriageway Narrows From Left',
  'Carriageway Narrows From Right',
  'Dual Carriageway Ends',
  'Quayside Ahead',
  'Uneven Road',
  'Speed Hump',
  'Dip',
  'Slippery Road',
  'Loose Gravel',
  'Danger Of Falling Rocks (Can Be Reversed)',
  'Pedestrian Crossing Ahead',
  'Children Crossing Or School',
  'Cyclists',
  'Animal Crossing',
  'Horses Crossing',
  'Wild Animals Crossing',
  'Road Works Or Obstruction (Temporary Sign)',
  'Traffic Signals',
  'Low Flying Aircraft',
  'Cross Wind',
  'Two Way Traffic',
  'Hazard/Other Danger (Use With Supplementary Plate)',
  'Roundabout',
  'Countdown Markers (Use At 100m Centers)',
  'Countdown Markers (Use At 100m Centers)',
  'Minor Road Merges From Left',
  'Minor Road Merges From Right',
  'Minor Road On Left',
  'Minor Road On Right',
  'Cross Roads',
  'Junction Road',
  'Merge With Major Road From Left',
  'Merge With Major Road From Right',
  'Staggered Junction',
  'Staggered Junction',
  'Two Way Traffic Across One Way Carriageway',
  'Tunnel',
  'Restricted Headroom',
  'Floodways',
  'Overhead Electric Line',
  'Divert To Opposite Carriageway',
  'Left Lane Of Dual Carriageway Closed Inumber Of Lanes May Be Varied As Required',
  'Sand Dunes',
  'Sharp Deviation To Left (152 Reversed To Right',
  'Stop Ahead',
  'Give Way Ahead',
  'Give Way',
  'Stop',
  'No Entry For All Vehicles',
  'Closed To All S Except Non-Mechan Nical Lly Prope Lled Vehicles Being Pushed By Pedestrians',
  'No Entry For Motor Cars',
  'No Entry For Motorcycles',
  'No Entry For Cycles And Mopeds',
  'No Entry For Buses',
  'No Entry For Goods Vehicles',
  'No Entry For Trailers Other Than Semitrailers Or Single Axle Trailers',
  'No Entry For Pedestrians',
  'No Entry For Animal Drawn Vehicles',
  'No Entry For Handcarts',
  'No Entry For Power Driven Agricultural Vehicles',
  'No Entry For Vehicles With Overali Width Greater Than Limit Shown',
  'No Entry For Vehicles With Overall Height Greater Than Limit Shown',
  'No Entry For Vehicles With Gross Weight Exceeding Limit Shown',
  'No Entry For Vehicles With An Axle Load Exceeding Limit Shown',
  'No Entry For Vehicles With Overall Length Greater Than Limit Shown Noles',
  'No Left Turn',
  'No Right Turn',
  'No U Turn',
  'No U Turn',
  'No Overtaking By All Vehicles',
  'No Overtaking By Goods Vehicles',
  '(60)Maximum Speed As Limit Shown',
  'No Sounding Of Horn',
  'Customs',
  'End Of Restriction',
  '(60) End Of Maximum Speed Limit Shown',
  'End Of Overtaking Restriction',
  'No Stopping Except For Loading And Unloading Passengers Or Goods',
  'No Stopping For Any Reason',
  'Turn Left',
  'Turn Right',
  'Go Ahead Only',
  'Turn Left Ahead',
  'Turn Right Ahead',
  'Roundabout - Give Way To Traffic From Left',
  'Go Ahead Or Left Only',
  'Go Ahead Or Right Only /A Go Ahead Or U-Turn Only / Go Left Or Right Only',
  'Keep Left',
  'Keep Right',
  'Pass Either Side',
  'Meter Zone',
  'End Of Meter Zone',
  'Trucks Keep Right(X Heigh=120Mm)',
  'Minimum Speed As Limit Shown',
  'Pedestrian Crossing',
  'Hospital',
  'Parking',
  'One Way Street',
  'No Through Road Straight Ahead',
  'No Through Road To Right',
  'Reversed',
  'No Through Road To Left',
  'Reversed',
  'Expressway',
  'End Of Expressway',
  'Bus Stop',
  'First Aid Station',
  'Breakdown Service',
  'Telephone / A Emergenct Telephone',
  'Filling Station',
  'Refreshment Cafeteria',
  'Camping Site',
  'Caravan Site',
  'Camping/Caravan Site',
  'Youth Hostel',
  'Pedestrian Subway',
  'Kilometer Post Sign',
  'Trucks Over... Tonne Not To Use The Bridge',
  'U-Turn Permitted Except By Trucks',
  'Escape Lane Ahead'
];



//
// Helper: format dates in table
//
function formatDate(dateString: string) {
  const d = new Date(dateString);
  return d.toLocaleDateString(undefined, {
    year:   'numeric',
    month:  'short',
    day:    '2-digit'
  });
}

function formatKey(k: string) {
  return k
    .replace(/_/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase());
}

//
// STORES & PAGINATION
//
const detailedSignsStore = useDetailedSignsStore();
const authStore          = useAuthStore();

const signsPaginated = computed(() => detailedSignsStore.detailedSignsPaginated);
const pageNumbers    = computed(() => {
  const p = signsPaginated.value;
  return p ? Array.from({ length: p.last_page }, (_, i) => i + 1) : [];
});

function changePage(to: number) {
  const p = signsPaginated.value;
  if (!p || to < 1 || to > p.last_page) return;
  detailedSignsStore.fetchDetailedSignsPaginated(to);
}

//
// TABLE SELECTION
//
const selectedId = ref<number|null>(null);
function selectRow(sign: DetailedSign) {
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
const showModal   = ref(false);
const activeSign  = ref<DetailedSign>({} as DetailedSign);
const sliderIndex = ref(0);
const imageUrls   = computed(() =>
  Array.isArray(activeSign.value.image_urls)
    ? activeSign.value.image_urls
    : []
);

// build the tableData for the show-modal
const tableData = computed(() => {
  const out: Record<string, any> = {};
  for (const k in activeSign.value) {
    if (['id','latitude','longitude','image_urls','media'].includes(k)) continue;
    out[k] = (activeSign.value as any)[k];
  }
  return out;
});

const modalMapRef    = ref<HTMLDivElement|null>(null);
const modalStreetRef = ref<HTMLDivElement|null>(null);
let   mapm: google.maps.Map;
let   markerm: google.maps.Marker;
let   panoramam: google.maps.StreetViewPanorama;

function openModal(sign: DetailedSign) {
  activeSign.value  = sign;
  sliderIndex.value = 0;
  showModal.value   = true;
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
const showEditModal     = ref(false);
const editActiveSign    = ref<DetailedSign>({} as DetailedSign);
const editSliderIndex   = ref(0);
const editFilesToUpload = ref<File[]>([]);
const editImageUrls     = computed(() =>
  Array.isArray(editActiveSign.value.image_urls)
    ? editActiveSign.value.image_urls
    : []
);
// 2️⃣ Whenever the selected code changes, fill in its related fields
watch(() => editActiveSign.value.sign_code, (newCode) => {
  const info = signCodeMap[newCode]
  if (info) {
    editActiveSign.value.sign_code_gcc  = info.Sign_Code_GCC
    editActiveSign.value.sign_type      = info.Sign_Type
    editActiveSign.value.sign_shape     = info.Sign_Shape
  } else {
    editActiveSign.value.sign_code_gcc  = ''
    editActiveSign.value.sign_type      = ''
    editActiveSign.value.sign_shape     = ''
  }
})
// build the tableData for the edit-modal
const editTableData = computed(() => {
  const out: Record<string, any> = {};
  for (const k in editActiveSign.value) {
    if (['id','image_urls'].includes(k)) continue;
    out[k] = (editActiveSign.value as any)[k];
  }
  return out;
});

// helper for choosing input types
function inputTypeForEdit(key: string) {
  const numKeys = ['latitude','longitude','gps_accuracy','sign_length','sign_width'];
  return numKeys.includes(key) ? 'number' : 'text';
}

const editModalMapRef    = ref<HTMLDivElement|null>(null);
const editModalStreetRef = ref<HTMLDivElement|null>(null);
let   editMap: google.maps.Map;
let   editMarker: google.maps.Marker;
let   editPanorama: google.maps.StreetViewPanorama;

function openEditModal(sign: DetailedSign) {
  editActiveSign.value    = { ...sign };
  editSliderIndex.value   = 0;
  editFilesToUpload.value = [];
  showEditModal.value     = true;
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

function handleEditFiles(e: Event) {
  const files = (e.target as HTMLInputElement).files;
  editFilesToUpload.value = files ? Array.from(files) : [];
}

async function submitEditUpdate() {
  const id = editActiveSign.value.id;
  const form = new FormData();
  for (const k in editActiveSign.value) {
    if (k === 'id' || k === 'image_urls') continue;
    form.append(k, (editActiveSign.value as any)[k] ?? '');
  }
  editFilesToUpload.value.forEach(f=>form.append('files[]', f));

  try {
    ApiService.setHeader(authStore.token as string);
    form.append('_method','PATCH');
    await ApiService.post(`/api/spa/signs/detailed/${id}`, form);
    await Swal.fire('Updated!','Sign updated successfully.','success');
    await detailedSignsStore.fetchDetailedSignsPaginated(signsPaginated.value!.current_page);
    showEditModal.value = false;
  } catch (err:any) {
    await Swal.fire('Error', err.message||'Update failed.','error');
  }
}

//
// DELETE
//
async function onDelete(sign: DetailedSign) {
  const res = await Swal.fire({
    title: 'تأكيد الحذف',
    text:  `هل أنت متأكد أنك تريد حذف الإشارة "${sign.sign_name}"؟`,
    icon:  'warning',
    showCancelButton: true,
    confirmButtonText: 'نعم، احذفها',
    cancelButtonText:  'إلغاء'
  });
  if (!res.isConfirmed) return;
  try {
    ApiService.setHeader(authStore.token as string,'application/json');
    await ApiService.delete(`/api/spa/signs/detailed/${sign.id}`);
    await Swal.fire('تم الحذف','تم حذف الإشارة بنجاح.','success');
    await detailedSignsStore.fetchDetailedSignsPaginated(signsPaginated.value!.current_page);
  } catch (err:any) {
    await Swal.fire('خطأ', err.message||'Delete failed.','error');
  }
}

//
// COMPLETENESS CHECK
//
function isComplete(sign: DetailedSign): boolean {
  return (Object.keys(sign) as (keyof DetailedSign)[])
    .filter(k=>!['id','latitude','longitude','image_urls'].includes(k))
    .every(k=>{
      const v=(sign as any)[k];
      if (v==null) return false;
      if (typeof v==='string' && !v.trim()) return false;
      if (Array.isArray(v) && v.length===0) return false;
      return true;
    });
}

//
// — Google Map & Overlays
//
const mapRef       = ref<HTMLDivElement|null>(null);
let   map: google.maps.Map|null   = null;
let   marker: google.maps.Marker|null = null;
const markers     = ref<google.maps.Marker[]>([]);
const maraEnabled = ref(false);
const nsdiEnabled = ref(false);

async function loadGoogle() {
  if ((window as any).google?.maps) return;
  await new Promise<void>(res=>{
    const s = document.createElement('script');
    s.src  = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBJs7FlVKhtppCAUasLTo4CBebnFMvENb4&libraries=geometry';
    s.onload = ()=>res();
    document.head.append(s);
  });
}

function newMaraOverlay() {
  return new google.maps.ImageMapType({
    getTileUrl(c,z){
      const y = 2**z -1 - c.y;
      return `https://gis.mara.gov.om/gs/geoserver/mara/gwc/service/tms/1.0.0/mara:PARCELS@EPSG:900913@png/${z}/${c.x}/${y}.png`;
    },
    tileSize: new google.maps.Size(256,256),
    opacity:0.7,
    isPng:true
  });
}

function newNsdiOverlay() {
  return new google.maps.ImageMapType({
    getTileUrl(c,z){
      const proj=map!.getProjection()!;
      const scale=2**z, ts=256;
      const sw=proj.fromPointToLatLng(new google.maps.Point(c.x*ts/scale,(c.y+1)*ts/scale))!;
      const ne=proj.fromPointToLatLng(new google.maps.Point((c.x+1)*ts/scale,c.y*ts/scale))!;
      const bbox=`${sw.lng()},${sw.lat()},${ne.lng()},${ne.lat()}`;
      return `https://nsdiapps.ncsi.gov.om/arcgis1/rest/services/Geoportal/Transportation/MapServer/export?bbox=${bbox}&bboxSR=4326&imageSR=3857&format=png&transparent=true&size=256,256&f=image`;
    },
    tileSize:new google.maps.Size(256,256),
    opacity:0.8,
    isPng:true
  });
}

function rebuildOverlays(){
  if(!map) return;
  map.overlayMapTypes.clear();
  if(maraEnabled.value) map.overlayMapTypes.insertAt(0,newMaraOverlay());
  if(nsdiEnabled.value) map.overlayMapTypes.insertAt(1,newNsdiOverlay());
}

function toggleMara(){ maraEnabled.value=!maraEnabled.value; rebuildOverlays(); }
function toggleNsdi(){ nsdiEnabled.value=!nsdiEnabled.value; rebuildOverlays(); }

async function initMap(){
  if(!mapRef.value) return;
  map = new google.maps.Map(mapRef.value,{
    center:{lat:23.585,lng:57.996},
    zoom:6
  });
  marker = new google.maps.Marker({ position: map.getCenter()!, map });
  rebuildOverlays();
  placeAllSignMarkers();
  map.addListener('center_changed',()=>{
    const c=map!.getCenter()!;
    document.getElementById('latlong')!
      .innerText = `Lat: ${c.lat().toFixed(6)} | Lng: ${c.lng().toFixed(6)}`;
  });
}

function placeAllSignMarkers(){
  if(!map) return;
  markers.value.forEach(m=>m.setMap(null));
  markers.value.splice(0);
  for(const s of signsPaginated.value?.data||[]){
    const la=parseFloat(s.latitude as any),
          ln=parseFloat(s.longitude as any);
    if(isNaN(la)||isNaN(ln)) continue;
    const m=new google.maps.Marker({
      position:{lat:la,lng:ln},
      map,
      icon:{
        path:google.maps.SymbolPath.CIRCLE,
        scale:6,
        fillColor:isComplete(s)?'#28a745':'#dc3545',
        fillOpacity:1,
        strokeWeight:1,
        strokeColor:'#fff'
      }
    });
    m.addListener('click',()=>{
      const inf=new google.maps.InfoWindow({
        content:`
          <button id="show-${s.id}">Show</button>
          <button id="edit-${s.id}">Edit</button>`
      });
      inf.open(map,m);
      google.maps.event.addListenerOnce(inf,'domready',()=>{
        document.getElementById(`show-${s.id}`)
          ?.addEventListener('click',()=>{ openModal(s); inf.close(); });
        document.getElementById(`edit-${s.id}`)
          ?.addEventListener('click',()=>{ openEditModal(s); inf.close(); });
      });
    });
    markers.value.push(m);
  }
}

onBeforeMount(()=>detailedSignsStore.fetchDetailedSignsPaginated());
onMounted(async()=>{
  await detailedSignsStore.fetchDetailedSignsPaginated();
  await loadGoogle();
  await initMap();
  watch(signsPaginated,()=>placeAllSignMarkers());
});
</script>




<style scoped>
.cursor-pointer {
  cursor: pointer;
}

</style>
