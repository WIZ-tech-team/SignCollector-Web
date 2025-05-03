import { onMounted, onUnmounted, reactive, ref, watch } from 'vue';
import { useViewportSize } from './useViewportSize';

export const TTK_DASHBOARD_LAYOUT_ID = "ttk_dashboard_layout";
export const TTK_DASHBOARD_HEADER_ID = "ttk_dashboard_header";
export const TTK_DASHBOARD_ASIDE_ID = "ttk_dashboard_aside";
export const TTK_DASHBOARD_MAIN_FOOTER_CONTAINER_ID = "ttk_dashboard_main_footer_container";
export const TTK_DASHBOARD_MAIN_ID = "ttk_dashboard_main";
export const TTK_DASHBOARD_FOOTER_ID = "ttk_dashboard_footer";
export const HIDE_SIDEBAR_CLASS_NAME = "hide-ttk-dashboard-sidebar";

export type DashboardControlState = {
    hideSidebar: boolean,
    isSmScreen: boolean,
    dashboardLayoutElement: HTMLElement | null,
    dashboardHeader: HTMLElement | null,
    dashboardMainContainer: HTMLElement | null,
    dashboardAside: HTMLElement | null
}

const controlState = reactive<DashboardControlState>({
    hideSidebar: false,
    isSmScreen: false,
    dashboardLayoutElement: null,
    dashboardHeader: null,
    dashboardMainContainer: null,
    dashboardAside: null
});

export function useDashboardLayoutControl() {

    onMounted(() => {

        controlState.hideSidebar = true;
        window.addEventListener('resize', checkSmScreen);

        // // Observe document element resize
        // dashboardLayoutResizeObserver.observe(document.documentElement);

        // Observe dashboard header resize
        // controlState.dashboardHeader = document.getElementById(TTK_DASHBOARD_HEADER_ID);
        updateLayoutComponentsSizes();
        // if (controlState.dashboardHeader) {
        //     console.log('controlState.dashboardHeader');
        //     dashboardLayoutResizeObserver.observe(controlState.dashboardHeader);
        // }

    });

    onUnmounted(() => {
        window.removeEventListener('resize', checkSmScreen);
    })

    // Composables
    const { viewport } = useViewportSize();

    // Header Resize Observer
    const dashboardLayoutResizeObserver = new ResizeObserver(() => {
        updateLayoutComponentsSizes();
    });

    watch([() => controlState.hideSidebar, () => controlState.isSmScreen], () => {
        checkAndToggleShowSidebarClass(null);
        // console.log('composable hide: ', controlState.hideSidebar);
        // console.log('composable smScreen: ', controlState.hideSidebar);
    });

    const checkSmScreen = () => {
        let screenWidth = window.innerWidth;
        if (screenWidth <= 920) {
            controlState.hideSidebar = true;
            controlState.isSmScreen = true;
        } else {
            controlState.hideSidebar = false;
            controlState.isSmScreen = false;
        }
        // console.log(`isSmall: ${controlState.isSmScreen}`);
        
        checkAndToggleShowSidebarClass(null);
    }

    const checkAndToggleShowSidebarClass = (hide: boolean | null) => {
        if (hide !== null) {
            controlState.hideSidebar = hide;
            // console.log(`hide: ${hide}`);
        }
        controlState.dashboardLayoutElement = document.getElementById(TTK_DASHBOARD_LAYOUT_ID);
        if (controlState.dashboardLayoutElement) {
            if (controlState.hideSidebar) {
                controlState.dashboardLayoutElement.classList.add(HIDE_SIDEBAR_CLASS_NAME);
            } else {
                controlState.dashboardLayoutElement.classList.remove(HIDE_SIDEBAR_CLASS_NAME);
            }
        }
        updateLayoutComponentsSizes();
    }

    const updateLayoutComponentsSizes = () => {
        // console.log('updateLayoutComponentsSizes');

        controlState.dashboardHeader = document.getElementById(TTK_DASHBOARD_HEADER_ID);
        controlState.dashboardMainContainer = document.getElementById(TTK_DASHBOARD_MAIN_FOOTER_CONTAINER_ID);
        controlState.dashboardAside = document.getElementById(TTK_DASHBOARD_ASIDE_ID);

        // Related dashboard header sizing
        if (controlState.dashboardHeader) {

            let headerHeight = parseFloat(getComputedStyle(controlState.dashboardHeader).height);
            let documentFontSize = parseFloat(getComputedStyle(document.documentElement).fontSize);

            if (controlState.dashboardAside) {
                controlState.dashboardAside.style.top = `${headerHeight / documentFontSize}rem`;
                controlState.dashboardAside.style.height = `${(document.documentElement.clientHeight - headerHeight) / documentFontSize}rem`;
            }

            if (controlState.dashboardMainContainer) {

                // console.log(`margit main top: ${headerHeight / documentFontSize}rem`);
                controlState.dashboardMainContainer.style.marginTop = `${headerHeight / documentFontSize}rem`;
                controlState.dashboardMainContainer.style.minHeight = `${(document.documentElement.clientHeight - headerHeight) / documentFontSize}rem`;

                // Main container width control
                if (controlState.dashboardAside) {
                    let asideWidth = parseFloat(getComputedStyle(controlState.dashboardAside).width);
                    if (!controlState.isSmScreen) {
                        if (controlState.hideSidebar) {
                            controlState.dashboardMainContainer.style.width = `${(document.documentElement.clientWidth) / documentFontSize}rem`;
                            controlState.dashboardMainContainer.style.marginRight = `0`;
                        } else {
                            controlState.dashboardMainContainer.style.width = `${(document.documentElement.clientWidth - asideWidth) / documentFontSize}rem`;
                            controlState.dashboardMainContainer.style.marginRight = `${asideWidth / documentFontSize}rem`;
                        }
                    } else {
                        controlState.dashboardMainContainer.style.width = `${(document.documentElement.clientWidth) / documentFontSize}rem`;
                        controlState.dashboardMainContainer.style.marginRight = `0`;
                    }
                }

            }
        }
    }

    return { controlState, checkAndToggleShowSidebarClass };

}
