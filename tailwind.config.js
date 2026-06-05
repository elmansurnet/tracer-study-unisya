/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/js/**/*.{vue,js}',
    './resources/views/**/*.blade.php',
  ],
  darkMode: 'class',
  theme: {
    extend: {
      fontFamily: {
        display: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui'],
        body: ['Inter', 'ui-sans-serif', 'system-ui'],
        mono: ['"JetBrains Mono"', 'ui-monospace', 'monospace'],
      },
      colors: {
        // Primary Teal — identitas UNISYA
        primary: {
          50:  '#f0fafa',
          100: '#caf0f2',
          200: '#96e2e6',
          300: '#5ccbd2',
          400: '#2aadb8',
          500: '#0d6c7c', // Base
          600: '#0a5766',
          700: '#084450',
          800: '#053139',
          900: '#031f24',
        },
        // Accent Gold
        accent: {
          400: '#f59e0b',
          500: '#d97706', // Base
          600: '#b45309',
        },
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '240px': '240px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}