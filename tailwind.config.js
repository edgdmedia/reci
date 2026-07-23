/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './inc/**/*.php',
    './page-templates/**/*.php',
    './template-parts/**/*.php',
    './templates/**/*.php',
    './sample/**/*.{js,jsx,ts,tsx,php,html}',
    './legacy-block/**/*.{php,html}',
    './src/**/*.{js,jsx,ts,tsx}',
    './modules/reflection-system/**/*.{php,js,jsx,ts,tsx}'
  ],
  theme: {
    extend: {
      colors: {
        'reci-blue': '#003594',
        'reci-yellow': '#FFB81C',
        'reci-bg': '#E8E9EE',
        'reci-link': '#3366FF',
        'reci-ink': '#2B2B2B',
        'reci-dark': '#212529',
        'reci-muted': '#6A6D70',
        'reci-line': '#BABBBD',
        'sphere-1': '#9B4D3A',
        'sphere-2': '#7A6340',
        'sphere-3': '#4A7A5C',
        'sphere-4': '#3A6B7A',
        'sphere-5': '#6A4A7A',
        'sphere-6': '#2C5F5A',
      },
      fontFamily: {
        sans: ['Roboto', 'SF Pro Display', 'Segoe UI', 'sans-serif'],
        serif: ['Merriweather', 'Georgia', 'Times New Roman', 'serif'],
        heading: ['Alternate Gothic ATF', 'Arial Narrow', 'Arial', 'sans-serif'],
        accent: ['Instrument Serif', 'Georgia', 'serif'],
        subhead: ['Roboto', 'SF Pro Display', 'Segoe UI', 'sans-serif']
      },
      fontWeight: {
        regular: '400',
        bold: '700'
      }
    }
  },
  plugins: []
};
