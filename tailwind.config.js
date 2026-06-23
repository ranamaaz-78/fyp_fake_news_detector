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
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#00288e',
                'on-primary': '#ffffff',
                'primary-container': '#1e40af',
                'on-primary-container': '#a8b8ff',
                'primary-fixed': '#dde1ff',
                secondary: '#555f6d',
                'on-secondary': '#ffffff',
                'secondary-container': '#d6e0f1',
                'secondary-fixed': '#d9e3f4',
                surface: '#f8f9fa',
                'surface-bright': '#f8f9fa',
                'surface-container': '#edeeef',
                'surface-container-low': '#f3f4f5',
                'surface-container-lowest': '#ffffff',
                'surface-container-high': '#e7e8e9',
                'surface-variant': '#e1e3e4',
                'surface-dim': '#d9dadb',
                background: '#f8f9fa',
                'on-background': '#191c1d',
                'on-surface': '#191c1d',
                'on-surface-variant': '#444653',
                outline: '#757684',
                'outline-variant': '#c4c5d5',
                error: '#ba1a1a',
                'trust-blue-light': '#EFF6FF',
                'trust-blue-dark': '#1E3A8A',
                'status-real': '#059669',
                'status-real-light': '#ECFDF5',
                'status-fake': '#DC2626',
                'status-fake-light': '#FEF2F2',
                'status-uncertain': '#D97706',
                'status-uncertain-light': '#FFFBEB',
            },
            spacing: {
                gutter: '24px',
                'container-max': '960px',
                'card-max': '720px',
                'stack-sm': '12px',
                'stack-md': '24px',
                'stack-lg': '48px',
                'margin-mobile': '16px',
            },
            maxWidth: {
                'container-max': '960px',
                'card-max': '720px',
            },
            fontSize: {
                'headline-xl': ['36px', { lineHeight: '44px', letterSpacing: '-0.02em', fontWeight: '700' }],
                'headline-xl-mobile': ['28px', { lineHeight: '34px', letterSpacing: '-0.01em', fontWeight: '700' }],
                'headline-lg': ['24px', { lineHeight: '32px', fontWeight: '600' }],
                'result-label': ['48px', { lineHeight: '48px', letterSpacing: '0.05em', fontWeight: '800' }],
                'body-lg': ['18px', { lineHeight: '28px', fontWeight: '400' }],
                'body-md': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                'body-sm': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                'label-bold': ['14px', { lineHeight: '20px', fontWeight: '600' }],
                'label-caps': ['12px', { lineHeight: '16px', letterSpacing: '0.05em', fontWeight: '500' }],
            },
            boxShadow: {
                fni: '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)',
                'fni-lg': '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)',
            },
        },
    },

    plugins: [forms],
};
