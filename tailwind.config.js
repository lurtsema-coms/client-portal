import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
                bebas: ["Bebas Neue", "sans-serif"],
            },
            colors: {
                "custom-blue": "rgb(4, 32, 44)",
                "primary-blue": "#1d4867",
                "button-blue": "#3fb1da",
            },
            backgroundImage: {
                "custom-gradient":
                    "linear-gradient(to left, rgba(13, 39, 55, 1) 0%, rgba(92, 151, 201, 1) 50%, rgba(13, 39, 55, 1) 100%)",
                "custom-gradient-2":
                    "linear-gradient(to bottom, rgb(0, 0, 0) 60%, rgba(5, 15, 22, 0.811), #09202c00);",
            },
        },
        container: {
            padding: {
                DEFAULT: "1rem",
                sm: "2rem",
                md: "3rem",
                lg: "4rem",
                xl: "5rem",
                "2xl": "6rem",
            },
        },
    },

    plugins: [forms],
};
