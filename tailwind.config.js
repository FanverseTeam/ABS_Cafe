/** @type {import('tailwindcss').Config} */
export default {
  darkMode: ["class"],
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        cafe: {
          dark: "#4A1F14",        // Coklat tua (primer, tombol utama, sidebar aktif, judul)
          brown: "#7A4A2B",       // Coklat sedang (hover, aksen)
          gold: "#D4A76A",        // Emas / karamel (tombol sekunder, badge, highlight)
          cream: "#F6EDE0",       // Krem terang (background halaman)
          beige: "#EFE0CC",       // Beige (kartu, panel, input)
          card: "#FBF6EE",        // Putih krem (kartu utama)
          text: "#3B1E14",        // Teks utama
          muted: "#8A6A55",       // Teks sekunder
          success: "#4F7A4A",     // Sukses
          warning: "#C98A2B",     // Peringatan
          danger: "#B03A2E",      // Bahaya / batal
          info: "#5B7C99",        // Info
        },
      },
      fontFamily: {
        serif: ["var(--font-playfair)", "Playfair Display", "Georgia", "serif"],
        sans: ["var(--font-poppins)", "Poppins", "DM Sans", "-apple-system", "BlinkMacSystemFont", "Segoe UI", "sans-serif"],
      },
      borderRadius: {
        "2xl": "1rem",
        "3xl": "1.5rem",
      },
      boxShadow: {
        soft: "0 4px 20px -2px rgba(74, 31, 20, 0.06), 0 2px 6px -1px rgba(74, 31, 20, 0.04)",
        "soft-lg": "0 10px 25px -3px rgba(74, 31, 20, 0.08), 0 4px 12px -2px rgba(74, 31, 20, 0.04)",
      },
    },
  },
  plugins: [],
};
