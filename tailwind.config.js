/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig"
    ],
    theme: {
        colors: {
            'background': '#f8f5f2',
            'button': '#3da9fc',
            'headline': '#094067',
            'paragraph': '#5f6c7b',
            'button-text': '#fffffe',
            'text': '#232323FF',
            'highlight': '#3da9fc',
            'stroke': '#09067',
            'secondary': '#90b4ce',
            'tertiary': '#ef4565',
            'red': '#ef4505',
        },
        extend: {},
    },
    plugins: [],
}
