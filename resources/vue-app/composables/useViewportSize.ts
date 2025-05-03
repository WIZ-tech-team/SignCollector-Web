import { onMounted, onUnmounted, reactive } from 'vue';

const viewport = reactive({
    width: document.documentElement.clientWidth,
    height: window.innerHeight
});

export function useViewportSize() {

    onMounted(() => {
        document.addEventListener('resize', updateViewportSizeHolder);
    });

    onUnmounted(() => {
        document.removeEventListener('resize', updateViewportSizeHolder);
    });

    const updateViewportSizeHolder = () => {
        viewport.width = document.documentElement.clientWidth;
        viewport.height = window.innerHeight;
    }

    return { viewport };

}