/**
 * postcss.config.js
 *
 * PostCSS configuration for the theme build pipeline.
 *
 * Current plugins:
 * - autoprefixer: adds vendor prefixes based on Browserslist rules.
 *
 * You can extend this later with plugins like:
 * - cssnano (if not using CssMinimizerPlugin)
 * - postcss-preset-env
 */
module.exports = {
    plugins: [require('autoprefixer')],
};
