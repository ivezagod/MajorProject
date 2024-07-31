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
                'primary-dark': '#3AA6B9',
                'primary-light': '#F9F9E0',
                'secondary-light': '#FFF9D0',
                'highlight-light': '#FF9EAA',
                'highlight-dark': '#A0DEFF',
                'error': '#FF9EAA',
                'error-text': '#FF9EAA',
                'success': '#5AB2FF',
                'success-light': '#CAF4FF',
                'success-dark': '#A0DEFF',
            },
            backgroundImage: {
                'custom-bg': "url('public/images/bgImage.jpg')",
            },
            backdropBlur: {
                'md': '8px',
            },
        },
    },

    plugins: [forms],
};
