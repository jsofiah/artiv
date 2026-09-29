import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    DEFAULT: '#745BB8',
                    dark: '#6349a8',
                },
                ink: '#1A1824',
                muted: '#6C687D',
                neutral: '#6B7280',
                line: '#D8D3E3',
                surface: '#E9E7EE',
                star: '#F59E0B',
                lime: '#D4F94E', // GANTI dengan warna lime dari Figma
            },
        },
    },

    plugins: [forms],
};