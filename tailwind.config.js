/** @type {import('tailwindcss').Config} */

module.exports = {
    content: [
        "./resources/views/**/*.{php,js,html}",
    ],
    theme: {
        container: {
            center: true,
            padding: '1rem',
            screens: {
                sm: '600px',
                md: '728px',
                lg: '984px'
            },
        },
        extend: {
            colors: {
                primary: {
                    700: '#191f26',
                    800: '#060d18',
                    900: '#10161d'
                },
            }
        },
    },
}

