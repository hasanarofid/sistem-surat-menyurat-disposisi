/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'class',
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        'primary': {
          DEFAULT: '#6366f1', // Indigo modern
          'soft': '#eef2ff',
          'dark': '#4f46e5',
        },
        'accent': {
          'blue': '#3b82f6',
          'indigo': '#6366f1',
          'violet': '#8b5cf6',
        },
        'success': {
          DEFAULT: '#10b981',
          'soft': '#ecfdf5',
        },
        'surface': {
          DEFAULT: '#ffffff',
          'dark': '#0f172a',
        },
        'background': {
          DEFAULT: '#f8fafc',
          'dark': '#020617',
        },
      },
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
        '3xl': '2rem',
      },
      boxShadow: {
        'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        'premium': '0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02)',
        'glow': '0 0 20px -5px rgba(99, 102, 241, 0.3)',
      },
      backgroundImage: {
        'glass': 'linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0))',
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
      },
      animation: {
        'float': 'float 3s ease-in-out infinite',
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-5px)' },
        }
      }
    },
  },
  plugins: [],
}

