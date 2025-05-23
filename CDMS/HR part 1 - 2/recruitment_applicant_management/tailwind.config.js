module.exports = {
  content: ["./src/**/*.{html,js,php}"],
  theme: {
    extend: {
      backgroundImage: {
        custom: "url('/img/logo.png')",
      },
      fontSize: {
        xs: "0.75rem", // Equivalent to 12px
        sm: "0.875rem", // Equivalent to 14px
        base: "1rem", // Equivalent to 16px
        lg: "1.125rem", // Equivalent to 18px
        xl: "1.25rem", // Equivalent to 20px
        "2xl": "1.5rem", // Equivalent to 24px
      },
    },
  },
  plugins: [],
};
