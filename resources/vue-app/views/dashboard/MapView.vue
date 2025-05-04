<template>
    <div class="flex flex-col items-center gap-4">
        <SignCard v-if="signs[locationIndex]" :sign="signs[locationIndex]" />
        <div class="flex items-center gap-2">
            <button type="button" @click.prevent="changeLocation('prev')" class="p-2 rounded-md bg-light-brand text-brand hover:bg-brand focus:outline-none
                hover:text-light-brand disabled:opacity-50" @click="showPopup = !showPopup"
                :disabled="locationIndex === 0">
                <SolidHeroIcon name="ArrowRightIcon" classes="h-6 w-6" />
            </button>
            <button type="button" @click.prevent="changeLocation('next')" class="p-2 rounded-md bg-light-brand text-brand hover:bg-brand focus:outline-none
                hover:text-light-brand disabled:opacity-50" @click="showPopup = !showPopup"
                :disabled="locationIndex === points.length - 1">
                <SolidHeroIcon name="ArrowLeftIcon" classes="h-6 w-6" />
            </button>
        </div>
        <div class="map-container rounded-md overflow-hidden">
            <div id="map" ref="mapRef"></div>
            <div id="street-view" ref="streetViewRef"></div>
            <div id="latlong">Lat: 0, Lng: 0</div>
            <!-- <div id="popup" v-show="showPopup">
                <div><b>Enter Information for this Point:</b></div>
                <div id="coords" style="margin-top:10px; font-size:13px; color:#555;">
                    Lat: {{ currentPosition?.lat.toFixed(6) }}, Lng: {{ currentPosition?.lng.toFixed(6) }}
                </div>
                <label for="signType">Street Sign Type:</label>
                <select id="signType" v-model="selectedSignType">
                    <option value="Type 1">Type 1</option>
                    <option value="Type 2">Type 2</option>
                    <option value="Type 3">Type 3</option>
                </select>
                <button @click="savePoint">Save</button>
            </div> -->
        </div>
    </div>
</template>

<script setup lang="ts">
import SignCard from '@/components/cards/SignCard.vue';
import SolidHeroIcon from '@/components/icons/SolidHeroIcon.vue'
import { useDetailedSignsStore } from '@/store/stores/detailedSignsStore'
import { ref, onMounted, onBeforeMount, computed } from 'vue'

onBeforeMount(async () => {
    await detailedSignsStore.fetchAllDetailedSigns().finally(async () => {

        if (detailedSignsStore.allSigns.length > 0) {
            points.value = detailedSignsStore.allSigns.map((sign) => {
                return { lat: parseFloat(sign.latitude), lng: parseFloat(sign.longitude) }
            })

            await loadGoogleMaps()
            initMap()
            latLongDisplay.value = document.getElementById('latlong')
        }
    });
});

const detailedSignsStore = useDetailedSignsStore();

const signs = computed(() => detailedSignsStore.allSigns ?? [])

interface Position {
    lat: number
    lng: number
}

interface MapPoint {
    position: Position
    type?: string
}

// Refs
const mapRef = ref<HTMLElement | null>(null)
const streetViewRef = ref<HTMLElement | null>(null)
const latLongDisplay = ref<HTMLElement | null>(null)
const showPopup = ref(false)
const selectedSignType = ref('Type 1')
const currentPosition = ref<Position | null>(null)

// Map instances
let map: google.maps.Map | null = null
let panorama: google.maps.StreetViewPanorama | null = null
let redArrowMarker: google.maps.Marker | null = null

// Sample data
const points = ref<Position[]>([
    { lat: 23.58353345, lng: 58.14637569 }
])

const locationIndex = ref(0)
const defaultLocation = ref(points.value[locationIndex.value])

// Initialize the map
const initMap = () => {
    if (!mapRef.value || !streetViewRef.value) return

    defaultLocation.value = points.value[locationIndex.value]

    // Initialize map
    map = new google.maps.Map(mapRef.value, {
        center: defaultLocation.value,
        zoom: 17,
        mapTypeId: 'satellite'
    })

    // Add MARA raster tile overlay
    const maraParcelsOverlay = new google.maps.ImageMapType({
        getTileUrl: function (coord: google.maps.Point, zoom: number) {
            const y = Math.pow(2, zoom) - 1 - coord.y // Convert TMS to XYZ
            return `https://gis.mara.gov.om/gs/geoserver/mara/gwc/service/tms/1.0.0/mara:PARCELS@EPSG:900913@png/${zoom}/${coord.x}/${y}.png`
        },
        tileSize: new google.maps.Size(256, 256),
        name: "MARA Parcels",
        opacity: 0.7
        // ,
        // isPng: true
    })

    map.overlayMapTypes.insertAt(0, maraParcelsOverlay)

    // Initialize Street View
    panorama = new google.maps.StreetViewPanorama(streetViewRef.value, {
        position: defaultLocation.value,
        pov: { heading: 165, pitch: 0 },
        zoom: 1
    })

    // Create red arrow marker
    redArrowMarker = new google.maps.Marker({
        position: defaultLocation.value,
        map: map,
        icon: {
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
            scale: 6,
            fillColor: 'red',
            fillOpacity: 1,
            strokeColor: 'white',
            strokeWeight: 2
        }
    })

    // Street View event listeners
    panorama.addListener('pov_changed', () => {
        if (!panorama || !redArrowMarker) return

        const heading = panorama.getPov().heading
        redArrowMarker.setIcon({
            path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
            scale: 6,
            rotation: heading,
            fillColor: 'red',
            fillOpacity: 1,
            strokeColor: 'white',
            strokeWeight: 2
        })
    })

    panorama.addListener('position_changed', () => {
        if (!panorama || !redArrowMarker) return

        const position = panorama.getPosition()
        redArrowMarker.setPosition(position)
        if (position) {
            currentPosition.value = { lat: position.lat(), lng: position.lng() }

            if (latLongDisplay.value) {
                latLongDisplay.value.innerText = `Lat: ${position.lat().toFixed(6)} | Lng: ${position.lng().toFixed(6)}`
            }
        } else {
            console.log('No position available: ', position);
        }
    })

    // Add sign markers
    points.value.forEach(point => {
        const marker = new google.maps.Marker({
            position: point,
            map: map,
            icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
        })

        marker.addListener('click', () => {
            if (!panorama || !redArrowMarker) return

            panorama.setPosition(marker.getPosition() as google.maps.LatLng)
            redArrowMarker.setPosition(marker.getPosition())
        })
    })

    // Add right-click event to show popup
    map.addListener('rightclick', (e: google.maps.MapMouseEvent) => {
        if (!e.latLng) return

        currentPosition.value = { lat: e.latLng.lat(), lng: e.latLng.lng() }
        showPopup.value = true

        if (panorama && redArrowMarker) {
            panorama.setPosition(e.latLng)
            redArrowMarker.setPosition(e.latLng)
        }
    })
}

// // Save point function
// const savePoint = () => {
//     if (!currentPosition.value) {
//         alert('No point selected.')
//         return
//     }

//     alert(`Point Saved!\nType: ${selectedSignType.value}\nLat: ${currentPosition.value.lat.toFixed(6)}\nLng: ${currentPosition.value.lng.toFixed(6)}`)
//     showPopup.value = false

//     // Here you would typically send data to your backend
//     // saveToBackend({
//     //   position: currentPosition.value,
//     //   type: selectedSignType.value
//     // })
// }

// Load Google Maps script
const loadGoogleMaps = () => {
    return new Promise<void>((resolve) => {
        if (window.google && window.google.maps) {
            resolve()
            return
        }

        const script = document.createElement('script')
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyBJs7FlVKhtppCAUasLTo4CBebnFMvENb4&libraries=geometry`
        script.onload = () => resolve()
        document.head.appendChild(script)
    })
}

// Initialize when mounted
onMounted(async () => {
    await loadGoogleMaps()
    initMap()
    latLongDisplay.value = document.getElementById('latlong')
})

const changeLocation = (direction: 'next' | 'prev') => {
    if (direction === 'next') {
        locationIndex.value = (locationIndex.value + 1) % points.value.length
    } else {
        locationIndex.value = (locationIndex.value - 1 + points.value.length) % points.value.length
    }
    defaultLocation.value = points.value[locationIndex.value]
    map?.setCenter(defaultLocation.value)
    panorama?.setPosition(defaultLocation.value)
}
</script>

<style scoped>
.map-container {
    margin: 0;
    padding: 0;
    height: 100vh;
    width: 100%;
    display: flex;
    overflow: auto;
    flex-direction: row;
}

#map,
#street-view {
    flex: 1;
    height: 100%;
    min-width: 400px;
    position: relative;
}

#latlong,
#popup {
    position: fixed;
    z-index: 200;
}

#latlong {
    top: 10px;
    left: 50%;
    transform: translateX(-50%);
    background: white;
    padding: 5px;
    font-size: 14px;
}

#popup {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
}
</style>