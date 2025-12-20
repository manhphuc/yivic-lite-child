/**
 * webpack.var.config.js
 *
 * Build-time variables for the Webpack pipeline.
 *
 * Why this file exists:
 * - Keeps entry/output paths out of the base config.
 * - Makes it easy to adjust file locations without touching pipeline logic.
 * - Helps keep webpack-base.config.js reusable and clean.
 *
 * Conventions:
 * - Paths are relative to the theme root.
 * - Output goes to: public-assets/dist/
 * - Source files live in: public-assets/src/
 */

const basePath = '.';

const webpackParams = {
    entryPath: {
        main: [
            basePath + '/public-assets/src/scss/main.scss',
            basePath + '/public-assets/src/js/main.js',
        ],
        admin: [
            basePath + '/public-assets/src/scss/admin.scss',
            basePath + '/public-assets/src/js/admin.js',
        ],
    },

    // dev default (no hash)
    jsOutputPath: basePath + '/public-assets/dist/js/[name].js',
    cssOutputPath: basePath + '/public-assets/dist/css/[name].css',
};

module.exports = { webpackParams };
