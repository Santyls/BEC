/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        bec: {
          dark: '#1E3A5F',     // Azul Profundo (Fondo principal, textos fuertes)
          primary: '#3B82F6',  // Azul Brillante (Botones, acentos)
          light: '#60A5FA',    // Azul Claro (Hover, detalles secundarios)
          pale: '#DBEAFE',     // Azul Pálido (Fondos suaves)
          glass: 'rgba(255, 255, 255, 0.1)', // Base para el cristal
        }
      },
      fontFamily: {
        // Usaremos Poppins para títulos y Inter para lectura (muy estético)
        sans: ['Inter', 'sans-serif'],
        display: ['Poppins', 'sans-serif'],
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-out',
        'slide-up': 'slideUp 0.5s ease-out',
        'carousel': 'carousel 20s infinite linear', // Animación lenta para el fondo
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(20px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        }
      }
    },
  },
  plugins: [],
}