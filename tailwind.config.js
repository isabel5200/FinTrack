import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'selector',
    theme: {
        extend: {
            keyframes: {
                dropdown: {
                    '0%': { opacity: '0', transform: 'translateY(-6px) scale(0.95)' },
                    '100%': { opacity: '1', transform: 'translateY(0) scale(1)' },
                },
            },
            animation: {
                dropdown: 'dropdown 0.18s ease-out forwards',
            },
            colors: {
                dark: "#0d1117",
                sidebar: "#0e0f1a",
                neon: "#00eaff",
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
