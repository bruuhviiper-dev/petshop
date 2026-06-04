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
        },
    },

    safelist: [
        'ml-0', 'ml-60',
        'translate-x-0', '-translate-x-full',
        'bg-amber-100', 'text-amber-800', 'border-amber-200',
        'bg-blue-100',  'text-blue-800',  'border-blue-200',
        'bg-purple-100','text-purple-800','border-purple-200',
        'bg-green-100', 'text-green-800', 'border-green-200',
        'bg-gray-100',  'text-gray-800',  'border-gray-200',
        'bg-red-100',   'text-red-800',   'border-red-200',
    ],

    plugins: [forms],
};
