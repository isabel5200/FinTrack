// Theme composable for managing application theme (light, dark, system)
import { ref, onMounted } from 'vue';

const theme = ref(localStorage.getItem("theme") || "system");
const isDark = ref(false);
let initialized = false;

export default function useTheme() {
    const applyTheme = () => {
        const html = document.documentElement;

        if (theme.value === "light") {
            html.classList.remove("dark");

            isDark.value = false;
        } else if (theme.value === "dark") {
            html.classList.add("dark");

            isDark.value = true;
        } else {
            const systemDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            html.classList.toggle("dark", systemDark);

            isDark.value = systemDark;
        }
    };

    const setTheme = (value) => {
        theme.value = value;
        localStorage.setItem("theme", value);

        applyTheme();
    };

    if (!initialized) {
        initialized = true;

        onMounted(() => {
            applyTheme();
        });
    }

    return { theme, setTheme, isDark };
}
