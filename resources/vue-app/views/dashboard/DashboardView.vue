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
            <SignsExport></SignsExport>

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
                      <button @click.stop="openModal(sign)" title="Show"
                        class="text-primary hover:text-light-primary bg-light-primary hover:bg-primary p-2 rounded-md">
                        <!-- eye icon -->
                        <SolidHeroIcon name="EyeIcon" classes="w-5 h-5" />
                      </button>
                      <button @click.stop="openEditModal(sign)" title="Edit"
                        class="text-warning hover:text-light-warning bg-light-warning hover:bg-warning p-2 rounded-md">
                        <!-- pencil icon -->
                        <SolidHeroIcon name="PencilIcon" classes="w-5 h-5" />
                      </button>
                      <button @click.stop="onDelete(sign)" title="Delete"
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
            <div class="overflow-auto h-[90%]">
              <!-- 1. ID -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">المعرّف</label>
                <input type="text" :value="activeSign.id" readonly class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 2. Latitude / Longitude -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">خط العرض</label>
                <input type="text" :value="activeSign.latitude" readonly class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <div class="flex flex-col">
                <label class="font-medium mb-1">خط الطول</label>
                <input type="text" :value="activeSign.longitude" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!--  gps_accuracy -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">دقة الجي بي أس</label>
                <input type="number" :value="activeSign.gps_accuracy" readonly
                  class="border rounded px-2 py-1 bg-gray-100" step="any" />
              </div>

              <!-- 3. Signs Count -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">عدد (تسلسل) اللوحات</label>
                <input type="text" :value="activeSign.signs_count" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 4. Columns Description -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">وصف الأعمدة</label>
                <input type="text" :value="activeSign.columns_description" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 5–12. Road fields -->
              <div v-for="field in roadFields" :key="field.key" class="flex flex-col">
                <label class="font-medium mb-1">{{ field.label }}</label>
                <input type="text" :value="activeSign[field.key as keyof DetailedSign]" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 13. Sign Base -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">قاعدة اللوحة</label>
                <input type="text" :value="activeSign.sign_base" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 14–17. Numeric fields -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">المسافة من نهاية كتف الطريق حتى اللوحة (م)</label>
                <input type="text" :value="activeSign.distance_from_road_edge_meter" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <div class="flex flex-col">
                <label class="font-medium mb-1">نصف قطر أنبوب اللوحة (مم)</label>
                <input type="text" :value="activeSign.sign_column_radius_mm" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <div class="flex flex-col">
                <label class="font-medium mb-1">طول الأنبوب (م)</label>
                <input type="text" :value="activeSign.column_height" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 18. Column Colour -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">لون الأنبوب</label>
                <input type="text" :value="activeSign.column_colour" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 19. Column Type -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">نوع الأعمدة</label>
                <input type="text" :value="activeSign.column_type" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 20. Sign Name -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">اسم اللوحة</label>
                <input type="text" :value="activeSign.sign_name" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <!-- 20. Sign Name -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">اسم اللوحة</label>
                <input type="text" :value="activeSign.sign_name" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <div class="flex flex-col">
                <label class="font-medium mb-1">الرمز (2010)</label>
                <input type="text" :value="activeSign.sign_code" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>



              <div class="flex flex-col">
                <label class="font-medium mb-1">الرمز (GCC)</label>
                <input v-model="activeSign.sign_code_gcc" readonly class="border rounded px-2 py-1 bg-gray-100" />

              </div>

              <!-- Sign Type (auto-filled, read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">نوعية للوحة</label>
                <input v-model="activeSign.sign_type" readonly class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- Sign Shape (auto-filled, read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">الشكل</label>
                <input v-model="activeSign.sign_shape" readonly class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <!-- Sign Length (number) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">طول اللوحة (م)</label>
                <input v-model.number="activeSign.sign_length" type="number" step="any" readonly
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Width (number) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">عرض اللوحة (م)</label>
                <input v-model.number="activeSign.sign_width" type="number" step="any" readonly
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Radius (number) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">نصف قطر اللوحة (مم)</label>
                <input v-model.number="activeSign.sign_radius" type="number" step="any" readonly
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Color (select) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">لون الخلفية</label>
                <input v-model.number="activeSign.sign_color" type="number" step="any" readonly
                  class="border rounded px-2 py-1" />

              </div>

              <!-- Content Shape Description (text) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">(المحتوى) الشكل المرسوم </label>
                <input v-model="activeSign.sign_content_shape_description" type="text" readonly
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Content Arabic Text (text) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">(المحتوى) المكتوب بالعربي</label>
                <input v-model="activeSign.sign_content_arabic_text" type="text" readonly
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Content English Text (text) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">(المحتوى) المكتوب بالإنجليزي</label>
                <input v-model="activeSign.sign_content_english_text" type="text" readonly
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Condition (select) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">حالة اللوحة</label>
                <input v-model="activeSign.sign_condition" type="text" readonly class="border rounded px-2 py-1" />

              </div>

              <!-- Comments (textarea) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">ملاحظات أخرى</label>
                <textarea v-model="activeSign.comments" rows="3" readonly class="border rounded px-2 py-1"></textarea>
              </div>

              <!-- Created By (read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">مدخل البيانات</label>
                <input v-model="activeSign.created_by" type="text" readonly
                  class="border rounded bg-gray-100 px-2 py-1" />
              </div>

              <!-- File picker -->
              <!-- <div class="flex flex-col">
                  <label class="font-medium mb-1">Replace Images</label>
                  <input type="file" multiple @change="handleEditFiles" class="border rounded p-1" />
                </div> -->
              <!-- …and so on for sign_code, sign_code_gcc, sign_type, sign_shape, sign_length, sign_width, sign_radius, sign_color, sign_content_shape_description, sign_content_arabic_text, sign_content_english_text, sign_condition, comments, created_by, created_at, updated_at, image_urls… -->
            </div>
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
              <!-- 1. ID (read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">المعرّف</label>
                <input type="text" :value="editActiveSign.id" readonly class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- 2. Latitude / Longitude (read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">خط الطول</label>
                <input type="text" :value="editActiveSign.latitude" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <div class="flex flex-col">
                <label class="font-medium mb-1">دائرة العرض</label>
                <input type="text" :value="editActiveSign.longitude" readonly
                  class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <!--  gps_accuracy -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">دقة الجي بي أس</label>
                <input type="number" :value="editActiveSign.gps_accuracy" readonly
                  class="border rounded px-2 py-1 bg-gray-100" step="any" />
              </div>


              <!-- 3. Signs Count (select 0–4) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">عدد (تسلسل) اللوحات</label>
                <select v-model.number="editActiveSign.signs_count" required class="border rounded px-2 py-1">
                  <option v-for="n in 5" :key="n - 1" :value="n - 1">{{ n - 1 }}</option>
                </select>
              </div>
              <!-- 4. Columns Description -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">وصف الأعمدة</label>
                <select v-model="editActiveSign.columns_description" required class="border rounded px-2 py-1">
                  <!-- optional placeholder -->
                  <option disabled value="">— اختر وصف الأعمدة —</option>
                  <option v-for="opt in columnOptions" :key="opt" :value="opt"
                    :selected="editActiveSign.columns_description === opt">
                    {{ opt }}
                  </option>
                </select>
              </div>

              <div v-for="field in roadFields" :key="field.key" class="flex flex-col">
                <label class="font-medium mb-1">{{ field.label }}</label>
                <input v-if="field.key != 'sign_location_from_road'" type="text"
                  v-model="editActiveSign[field.key as keyof DetailedSign]"
                  :readonly="!['road_direction'].includes(field.key)" :required="['road_direction'].includes(field.key)"
                  class="border rounded px-2 py-1 bg-gray-100" />
                <select v-else v-model="editActiveSign.sign_location_from_road" required
                  class="border rounded px-2 py-1">
                  <option v-for="option in signLocationsFromRoad" :value="option">
                    {{ option }}
                  </option>
                </select>
              </div>
              <!-- 13. Sign Base -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">قاعدة اللوحة</label>
                <select v-model="editActiveSign.sign_base" required class="border rounded px-2 py-1">

                  <option value="بقاعدة اسمنتية" :selected="editActiveSign.sign_base === 'بقاعدة اسمنتية'">بقاعدة
                    اسمنتية
                  </option>
                  <option value="بدون قاعدة" :selected="editActiveSign.sign_base === 'بدون قاعدة'">بدون قاعدة</option>
                </select>
              </div>
              <!-- 14–17 Number entries -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">المسافة من نهاية كتف الطريق حتى اللوحة (م)</label>
                <input v-model.number="editActiveSign.distance_from_road_edge_meter" type="number" step="any"
                  class="border rounded px-2 py-1" required />
              </div>
              <div class="flex flex-col">
                <label class="font-medium mb-1">نصف قطر أنبوب اللوحة (مم)</label>
                <input v-model.number="editActiveSign.sign_column_radius_mm" required type="number" step="any"
                  class="border rounded px-2 py-1" />
              </div>
              <div class="flex flex-col">
                <label class="font-medium mb-1">طول الأنبوب (م)</label>
                <input v-model.number="editActiveSign.column_height" required type="number" step="any"
                  class="border rounded px-2 py-1" />
              </div>

              <!-- 18. Column Colour -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">لون الأنبوب</label>
                <select v-model="editActiveSign.column_colour" required class="border rounded px-2 py-1">
                  <option value="رمادي" :selected="editActiveSign.column_colour === 'رمادي'">رمادي</option>
                  <option value="أبيض وأسود" :selected="editActiveSign.column_colour === 'أبيض وأسود'">أبيض وأسود
                  </option>
                  <option value="أخرى" :selected="editActiveSign.column_colour === 'أخرى'">أخرى</option>
                  <option value="ممتسح" :selected="editActiveSign.column_colour === 'ممتسح'">ممتسح</option>

                </select>
              </div>

              <!-- 19. Column Type -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">نوع الأعمدة </label>
                <select v-model="editActiveSign.column_type" required class="border rounded px-2 py-1">
                  <option value="خشب" :selected="editActiveSign.column_type === 'خشب'">خشب</option>
                  <option value="حديد" :selected="editActiveSign.column_type === 'حديد'">حديد</option>
                </select>
              </div>

              <!-- 20. Sign Name -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">اسم اللوحة </label>

                <select v-model="editActiveSign.sign_name" class="border rounded px-2 py-1" required>
                  <option disabled value="">— اختر رمز اللوحة —</option>
                  <option v-for="code in signCodeOptions" :key="code" :value="code">{{ code }}</option>
                </select>
              </div>

              <!-- Sign Code -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">الرمز (2010)</label>

                <input v-model="editActiveSign.sign_code" required class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- Sign Code GCC (auto-filled, read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">الرمز (GCC)</label>
                <input v-model="editActiveSign.sign_code_gcc" required class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- Sign Type (auto-filled, read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">نوعية للوحة</label>
                <input v-model="editActiveSign.sign_type" required class="border rounded px-2 py-1 bg-gray-100" />
              </div>

              <!-- Sign Shape (auto-filled, read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">الشكل</label>
                <input v-model="editActiveSign.sign_shape" required class="border rounded px-2 py-1 bg-gray-100" />
              </div>
              <!-- Sign Length (number) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">طول اللوحة (م)</label>
                <input v-model.number="editActiveSign.sign_length" type="number" step="any"
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Width (number) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">عرض اللوحة (م)</label>
                <input v-model.number="editActiveSign.sign_width" type="number" step="any"
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Radius (number) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">نصف قطر اللوحة (مم)</label>
                <input v-model.number="editActiveSign.sign_radius" type="number" step="any"
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Color (select) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">لون الخلفية</label>
                <select v-model="editActiveSign.sign_color" required class="border rounded px-2 py-1">
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
                <label class="font-medium mb-1">(المحتوى) الشكل المرسوم </label>
                <input v-model="editActiveSign.sign_content_shape_description" type="text"
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Content Arabic Text (text) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">(المحتوى) المكتوب بالعربي</label>
                <input v-model="editActiveSign.sign_content_arabic_text" type="text" class="border rounded px-2 py-1" />
              </div>

              <!-- Content English Text (text) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">(المحتوى) المكتوب بالإنجليزي</label>
                <input v-model="editActiveSign.sign_content_english_text" type="text"
                  class="border rounded px-2 py-1" />
              </div>

              <!-- Sign Condition (select) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">حالة اللوحة</label>
                <select v-model="editActiveSign.sign_condition" required class="border rounded px-2 py-1">
                  <option disabled value="">— اختر حالة اللوحة —</option>
                  <option value="جيدة">جيدة</option>
                  <option value="متوسطة">متوسطة</option>
                  <option value="سيئة">سيئة</option>
                </select>
              </div>

              <!-- Comments (textarea) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">ملاحظات أخرى</label>
                <textarea v-model="editActiveSign.comments" rows="3" class="border rounded px-2 py-1"></textarea>
              </div>

              <!-- Created By (read-only) -->
              <div class="flex flex-col">
                <label class="font-medium mb-1">مدخل البيانات</label>
                <input v-model="editActiveSign.created_by" type="text" readonly
                  class="border rounded bg-gray-100 px-2 py-1" />
              </div>

              <!-- File picker -->
              <!-- <div class="flex flex-col">
              <label class="font-medium mb-1">Replace Images</label>
              <input type="file" multiple @change="handleEditFiles" class="border rounded p-1" />
            </div> -->

              <button type="submit" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Save Changes
              </button>
            </div>
          </div>

          <!-- Middle: preview slider -->
          <div v-if="editImageUrls.length > 0" class="w-1/3 bg-gray-100 p-4 flex flex-col">
            <div class="flex-1 relative overflow-hidden">
              <div v-for="(url, idx) in editImageUrls" :key="idx"
                class="absolute inset-0 flex items-center justify-center transition-opacity duration-300"
                :class="idx === editSliderIndex ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                <img :src="url as string" class="max-w-full max-h-full object-contain rounded" alt="Preview" />
              </div>
            </div>
            <div v-if="editImageUrls.length" class="mt-2 flex justify-center space-x-2">
              <button type="button" class="gap-2 p-2 rounded-md border bg-white w-[4.5rem] disabled:opacity-50"
                :disabled="editSliderIndex === 0" @click="editSliderIndex--">السابق</button>
              <button type="button" class="gap-2 p-2 rounded-md border bg-white w-[4.5rem] disabled:opacity-50"
                :disabled="editSliderIndex === editImageUrls.length - 1" @click="editSliderIndex++">التالي</button>
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
import { useDetailedSignsStore } from '@/store/stores/detailedSignsStore';
import { useAuthStore } from '@/store/stores/authStore';
import ApiService from '@/core/services/ApiService';
import Swal from 'sweetalert2';
import { DetailedSign } from '@/core/types/data/DetailedSign';
import signsList from '@/assets/signs.json'           // ← your uploaded JSON
import { MSwal, QSwal } from '@/core/plugins/SweetAlerts2';
import { AxiosError } from 'axios';
import { BackendResponseData } from '@/core/types/config/AxiosCustom';
import { getMessageFromObj } from '@/assets/ts/swalMethods';
import SolidHeroIcon from '@/components/icons/SolidHeroIcon.vue';
import ModalCard from '@/components/cards/ModalCard.vue';
import SignsExport from '@/components/partials/SignsExport.vue';

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
const signsFiltered = computed<DetailedSign[]>(() => {
  let arr: DetailedSign[]
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

// instead of just a string array, make each one an object
const roadFields = [
  { key: 'road_name', label: 'اسم الطريق' },
  { key: 'road_classification', label: 'تصنيف الطريق' },

  { key: 'road_number', label: 'رقم الطريق' },
  { key: 'road_type', label: 'نوع الطريق' },
  { key: 'road_direction', label: ' الطريق نحو' },
  { key: 'governorate', label: 'المحافظة' },
  { key: 'willayat', label: 'الولاية' },
  { key: 'village', label: 'القرية' },
  { key: 'sign_location_from_road', label: 'موقع اللوحة من الطريق' }
];
// 1️⃣ Build an array of codes and a lookup map
const signCodeOptions = computed<string[]>(() =>
  signsList.map((s: any) => s.Sign_Name)
)

const signCodeMap: Record<string, any> = signsList.reduce((map, s: any) => {
  map[s.Sign_Name] = s
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
const signLocationsFromRoad = ref(['يمين', 'يسار', 'منتصف', 'جزيرة وسطية'])

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

function formatKey(k: string) {
  return k
    .replace(/_/g, ' ')
    .replace(/\b\w/g, l => l.toUpperCase());
}

//
// STORES & PAGINATION
//
const detailedSignsStore = useDetailedSignsStore();
const authStore = useAuthStore();

const signsPaginated = computed(() => detailedSignsStore.detailedSignsPaginated);
const pageNumbers = computed(() => {
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
const selectedId = ref<number | null>(null);
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
const showModal = ref(false);
const activeSign = ref<DetailedSign>({} as DetailedSign);
const sliderIndex = ref(0);
const imageUrls = computed(() =>
  Array.isArray(activeSign.value?.image_urls)
    ? activeSign.value.image_urls
    : []
);

// build the tableData for the show-modal
const tableData = computed(() => {
  const out: Record<string, any> = {};
  for (const k in activeSign.value) {
    if (['id', 'latitude', 'longitude', 'image_urls', 'media'].includes(k)) continue;
    out[k] = (activeSign.value as any)[k];
  }
  return out;
});

const modalMapRef = ref<HTMLDivElement | null>(null);
const modalStreetRef = ref<HTMLDivElement | null>(null);
let mapm: google.maps.Map;
let markerm: google.maps.Marker;
let panoramam: google.maps.StreetViewPanorama;

function openModal(sign: DetailedSign) {
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
const editActiveSign = ref<DetailedSign>({} as DetailedSign);
const editSliderIndex = ref(0);
const editFilesToUpload = ref<File[]>([]);
const editImageUrls = computed(() =>
  Array.isArray(editActiveSign.value.image_urls)
    ? editActiveSign.value.image_urls
    : []
);
// 2️⃣ Whenever the selected code changes, fill in its related fields
watch(() => editActiveSign.value.sign_name, (newCode) => {
  const info = signCodeMap[newCode]
  if (info) {
    editActiveSign.value.sign_code = info.Sign_Code_2010

    editActiveSign.value.sign_code_gcc = info.Sign_Code_GCC
    editActiveSign.value.sign_type = info.Sign_Type
    editActiveSign.value.sign_shape = info.Sign_Shape
  } else {
    editActiveSign.value.sign_code = ''
    editActiveSign.value.sign_code_gcc = ''
    editActiveSign.value.sign_type = ''
    editActiveSign.value.sign_shape = ''
  }
})
// build the tableData for the edit-modal
const editTableData = computed(() => {
  const out: Record<string, any> = {};
  for (const k in editActiveSign.value) {
    if (['id', 'image_urls'].includes(k)) continue;
    out[k] = (editActiveSign.value as any)[k];
  }
  return out;
});

// helper for choosing input types
function inputTypeForEdit(key: string) {
  const numKeys = ['latitude', 'longitude', 'gps_accuracy', 'sign_length', 'sign_width'];
  return numKeys.includes(key) ? 'number' : 'text';
}

const editModalMapRef = ref<HTMLDivElement | null>(null);
const editModalStreetRef = ref<HTMLDivElement | null>(null);
let editMap: google.maps.Map;
let editMarker: google.maps.Marker;
let editPanorama: google.maps.StreetViewPanorama;

function openEditModal(sign: DetailedSign) {
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
  editFilesToUpload.value.forEach(f => form.append('files[]', f));

  QSwal.fire('تحديث اللوحة ؟', 'سيتم تحديث بيانات اللوحة.', 'question')
    .then(async (result) => {
      if (result.isConfirmed) {

        try {
          ApiService.setHeader(authStore.token as string);
          // form.append('_method', 'PATCH');
          await ApiService.post(`/api/spa/signs/detailed/${id}`, form);
          await Swal.fire('تم!', 'تم تحديث اللوحة بنجاح.', 'success');
        } catch (err: any) {
          await Swal.fire('خطأ!', err.message || 'فشل التحديث.', 'error');
        } finally {
          await detailedSignsStore.fetchDetailedSignsPaginated();
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
async function onDelete(sign: DetailedSign) {
  const res = await Swal.fire({
    title: 'تأكيد الحذف',
    text: `هل أنت متأكد أنك تريد حذف اللوحة "${sign.sign_name}"؟`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'نعم، احذفها',
    cancelButtonText: 'إلغاء'
  });
  if (!res.isConfirmed) return;
  try {
    ApiService.setHeader(authStore.token as string, 'application/json');
    await ApiService.delete(`/api/spa/signs/detailed/${sign.id}`);
    await Swal.fire('تم الحذف', 'تم حذف اللوحة بنجاح.', 'success');
    await detailedSignsStore.fetchDetailedSignsPaginated(signsPaginated.value!.current_page);
  } catch (err: any) {
    await Swal.fire('خطأ', err.message || 'Delete failed.', 'error');
  }
}

//
// COMPLETENESS CHECK
//
function isComplete(sign: DetailedSign): boolean {
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

async function initMap() {
  if (!mapRef.value) return;
  map = new google.maps.Map(mapRef.value, {
    center: { lat: 23.585, lng: 57.996 },
    zoom: 6,
    mapTypeId: 'hybrid'
  });
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

onBeforeMount(() => detailedSignsStore.fetchDetailedSignsPaginated());
onMounted(async () => {
  await detailedSignsStore.fetchDetailedSignsPaginated();
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

const submitExport = async () => {
  QSwal.fire('تصدير اللوحات ؟', 'التصدير إلى ملف إكسل.', 'question')
    .then(async (result) => {
      if (result.isConfirmed) {

        ApiService.setHeader(authStore.token as string, 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
        await ApiService.post(`/api/spa/signs/detailed/export`, null, {
          responseType: 'arraybuffer', // Crucial for binary files
          headers: {
            'Accept': 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Type': 'multipart/form-data' // Or 'application/json'
          }
        }).then(response => {
          // Verify we got binary data
          if (!(response.data instanceof ArrayBuffer)) {
            throw new Error('Invalid response format');
          }

          // Create blob with explicit type
          const blob = new Blob([response.data], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
          });

          // Get filename
          let filename = 'Subscriptions_' + new Date().toISOString().split('T')[0] + '.xlsx';
          const disposition = response.headers['content-disposition'];
          if (disposition) {
            const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
            if (matches && matches[1]) {
              filename = matches[1].replace(/['"]/g, '');
            }
          }

          // Create and trigger download
          const url = window.URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          link.download = filename;
          document.body.appendChild(link);
          link.click();

          // Cleanup
          setTimeout(() => {
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
          }, 100);

        }).catch((error: AxiosError<BackendResponseData>) => {
          MSwal.fire('خطأ غير متوقع!', getMessageFromObj(error), 'error');
        });

      }
    })
}

const toggleRecordsSort = () => {
  sortById.value = (sortById.value === 'asc') ? 'desc' : 'asc'
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
