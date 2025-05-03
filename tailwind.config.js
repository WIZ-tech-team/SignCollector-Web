import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#851eeb',
                'light-primary': '#f5ecfd',
                'active-primary': '#7012CE',
                warning: '#f2994a',
                'light-warning': '#fef4ec',
                'active-warning': '#f0882d',
                danger: '#FD5B71',
                'light-danger': '#FFEBED',
                'active-danger': '#fd3550',
                success: '#37d278',
                'light-success': '#eefbf4',
                'active-success': '#27AE60',
                lotion: '#fafafa',
                brand: '#1B143F',
                'light-brand': '#f1effa',
                'active-brand': '#140f2f',
            }
        },
    },
    plugins: [],
};
