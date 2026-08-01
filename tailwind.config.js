module.exports = {
  content: [
    './*.php',
    './functions.php'
  ],
  darkMode: 'class',
  safelist: [
    'bg-cover',
    'bg-center',
    'bg-white/30',
    'dark:bg-darkCard/30',
    'backdrop-blur-md',
    'font-sans',
    'font-serif',
    'text-teal',
    'border-teal',
    'dark:border-teal',
    'opacity-0',
    'opacity-100',
    'pointer-events-none',
    'pointer-events-auto',
    'translate-y-0',
    'translate-y-5',
    'translate-x-full',
    'invisible',
    'visible'
  ],
  theme: {
    extend: {
      colors: {
        teal: '#39c5bb',
        darkBg: '#121418',
        darkCard: '#1a1d24'
      },
      fontFamily: {
        sans: ['ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'sans-serif'],
        playfair: ['ui-serif', 'Georgia', 'Cambria', '"Times New Roman"', 'serif']
      },
      boxShadow: {
        glow: '0 0 20px rgba(57, 197, 187, 0.15)'
      }
    }
  },
  plugins: [
    require('@tailwindcss/typography')
  ]
};
