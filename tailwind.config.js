const defaultTheme = require('tailwindcss/defaultTheme');

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"Impact"', 'Haettenschweiler', '"Arial Narrow Bold"', ...defaultTheme.fontFamily.sans],
                display: ['"Impact"', 'Haettenschweiler', '"Arial Narrow Bold"', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fce4ec',
                    100: '#f8bbd9',
                    200: '#f48fb1',
                    300: '#f06292',
                    400: '#ec407a',
                    500: '#E91E63', // Landing Page Primary Pink
                    600: '#D81B60',
                    700: '#C2185B', // Landing Page Deep Pink
                    800: '#AD1457',
                    900: '#881337', // Landing Page Maroon
                },
                coral: {
                    50: '#fbe9e7',
                    100: '#ffccbc',
                    200: '#ffab91',
                    300: '#ff8a65',
                    400: '#ff7043',
                    500: '#FF5722', // Coral primary
                    600: '#F4511E',
                    700: '#E64A19',
                    800: '#D84315',
                    900: '#BF360C',
                },
                cta: {
                    DEFAULT: '#E91E63', // Pink for CTAs
                    hover: '#C2185B',
                },
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
