/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './inc/**/*.php',
    './page-templates/**/*.php',
    './template-parts/**/*.php',
    './figma/**/*.php',
    './sample/**/*.{js,jsx,ts,tsx,php,html}',
    './legacy-block/**/*.{php,html}',
    './src/**/*.{js,jsx,ts,tsx}'
  ],
  theme: {
    extend: {
      colors: {
        'reci-blue': '#003594',
        'reci-yellow': '#FFB81C',
        'reci-bg': '#F3F5F9',
        'reci-ink': '#2B2B2B',
        'reci-dark': '#212529',
        'reci-muted': '#6A6D70',
        'reci-line': '#BABBBD'
      },
      fontFamily: {
        sans: ['Roboto', 'SF Pro Display', 'Segoe UI', 'sans-serif'],
        serif: ['EB Garamond', 'Times New Roman', 'serif']
      }
    }
  },
  plugins: []
};
