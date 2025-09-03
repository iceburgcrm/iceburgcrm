const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    plugins: [
        require('@tailwindcss/forms'),
        require('daisyui'),
    ],

    daisyui: {
        themes: [
            // Your two custom themes
            {
                iceburgsaas: {
                    "primary": "#003AC3",
                    "secondary": "#F97316",
                    "accent": "#22C55E",
                    "neutral": "#1F2937",
                    "base-100": "#FFFFFF",
                    "base-200": "#F9FAFB",
                    "base-300": "#E5E7EB",
                    "info": "#3B82F6",
                    "success": "#10B981",
                    "warning": "#F59E0B",
                    "error": "#EF4444",
                },
            },
            {
                iceburgcorporate: {
                    "primary": "#1F2937",    // dark gray for headers/buttons
                    "secondary": "#4B5563",  // medium gray for accents
                    "accent": "#6B7280",     // subtle gray-blue for highlights
                    "neutral": "#374151",    // background panels
                    "base-100": "#FFFFFF",   // main background
                    "base-200": "#F3F4F6",   // secondary background
                    "base-300": "#E5E7EB",   // cards/panels
                    "info": "#3B82F6",       // subtle blue for info
                    "success": "#10B981",    // green for success messages
                    "warning": "#FBBF24",    // yellow for warnings
                    "error": "#EF4444",      // red for errors
                },

            },
            {
                iceburgai: {
                    "primary": "#00E0FF",   // neon cyan for primary buttons / links
                    "secondary": "#FF3D81", // hot pink for secondary actions
                    "accent": "#7C3AED",    // violet accent for highlights
                    "neutral": "#0F172A",   // very dark gray / background panels
                    "base-100": "#1E293B",  // dark background
                    "base-200": "#273449",  // panels / cards
                    "base-300": "#334155",  // subtle card background
                    "info": "#3B82F6",      // info messages (blue)
                    "success": "#22C55E",   // green for success
                    "warning": "#FACC15",   // yellow for warnings
                    "error": "#EF4444",     // red for errors
                },
            },
            "light", "dark", "cupcake", "bumblebee", "emerald", "corporate",
            "synthwave", "retro", "cyberpunk", "valentine", "halloween", "garden",
            "forest", "aqua", "lofi", "pastel", "fantasy", "wireframe", "black",
            "luxury", "dracula", "cmyk", "autumn", "business", "acid", "lemonade",
            "night", "coffee", "winter",
        ],
    },
};
