import { ref, onMounted } from 'vue';

export default function useTheme() {
    const theme = ref(localStorage.getItem("theme") || "system");

    const applyTheme = () => {
        const html = document.documentElement;

        if (theme.value === "light") {
            html.classList.remove("dark");
        } else if (theme.value === "dark") {
            html.classList.add("dark");
        } else {
            const systemDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            html.classList.toggle("dark", systemDark);
        }
    };

    const setTheme = (value) => {
        theme.value = value;
        localStorage.setItem("theme", value);

        applyTheme();
    };

    onMounted(() => {
        applyTheme();
    });

    return { theme, setTheme };
}
