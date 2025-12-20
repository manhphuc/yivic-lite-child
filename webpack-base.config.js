/**
 * Base Webpack configuration for the Yivic Lite Child theme.
 *
 * Goals:
 * - Compile SCSS -> CSS (extracted files)
 * - Bundle JS (ES5-compatible for classic WP environments)
 * - Provide source maps in development
 * - Minify CSS/JS in production
 * - Generate a deterministic manifest for Laravel-like "mix()" usage
 *
 * Manifest contract (used by ThemeContext::mix()):
 * - Keys:   "css/main.css", "js/main.js"
 * - Values: "css/main.<hash>.css", "js/main.<hash>.js"
 *
 * Output structure:
 * public-assets/dist/
 *   css/
 *   js/
 *   manifest/manifest.json
 */

const path = require('path');
const WebpackBuildNotifierPlugin = require('webpack-build-notifier');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const { WebpackManifestPlugin } = require('webpack-manifest-plugin');

module.exports.buildConfig = function (webpackVariables, mode) {
    const { webpackParams } = webpackVariables;
    const isProduction = mode === 'production';

    // Build root for emitted assets (relative to theme root)
    const distRoot = 'public-assets/dist';
    const distRootNormalized = distRoot.replace(/\\/g, '/') + '/';

    // Output filenames:
    // - dev: keep stable names for easier debugging
    // - prod: add contenthash for long-term caching + correct manifest behavior
    const jsFilename = isProduction
        ? 'public-assets/dist/js/[name].[contenthash:8].js'
        : webpackParams.jsOutputPath;

    const cssFilename = isProduction
        ? 'public-assets/dist/css/[name].[contenthash:8].css'
        : webpackParams.cssOutputPath;

    return {
        /**
         * Target modern browsers while keeping ES5 compatibility.
         * This avoids Webpack's "no default script chunk format available" issues
         * when the output is consumed by non-module environments.
         */
        target: ['web', 'es5'],

        /**
         * Source maps:
         * - dev: inline for faster local debugging
         * - prod: disabled for performance + smaller output
         */
        devtool: isProduction ? false : 'inline-source-map',

        /**
         * Entry points:
         * - main: front-end CSS/JS
         * - admin: admin CSS/JS
         */
        entry: webpackParams.entryPath,

        /**
         * Output:
         * - Writes into the theme root.
         * - clean: false (safety) -> never wipe theme directory by accident.
         */
        output: {
            filename: jsFilename,
            path: path.resolve(__dirname),

            /**
             * Explicit chunk format to resolve compatibility issues
             * when building for non-module environments.
             */
            chunkFormat: 'array-push',

            clean: false,
        },

        module: {
            rules: [
                /**
                 * TypeScript (optional).
                 * Safe to remove if TS is not used in this theme.
                 */
                {
                    test: /\.tsx?$/,
                    use: 'ts-loader',
                    exclude: /node_modules/,
                },

                /**
                 * JavaScript transpilation via Babel.
                 * Targets "defaults" for broad compatibility.
                 */
                {
                    test: /\.js$/,
                    exclude: /node_modules/,
                    use: {
                        loader: 'babel-loader',
                        options: {
                            presets: [['@babel/preset-env', { targets: 'defaults' }]],
                        },
                    },
                },

                /**
                 * Load source maps for JS files (dev convenience).
                 */
                {
                    test: /\.js$/,
                    enforce: 'pre',
                    use: ['source-map-loader'],
                },

                /**
                 * Plain CSS -> extracted file.
                 */
                {
                    test: /\.css$/i,
                    use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader'],
                },

                /**
                 * SCSS -> CSS -> PostCSS -> extracted file.
                 */
                {
                    test: /\.(sass|scss)$/i,
                    use: [
                        MiniCssExtractPlugin.loader,
                        {
                            loader: 'css-loader',
                            options: {
                                sourceMap: true,
                                url: false,
                            },
                        },
                        {
                            loader: 'resolve-url-loader',
                            options: { sourceMap: true },
                        },
                        {
                            loader: 'postcss-loader',
                            options: { sourceMap: true },
                        },
                        {
                            loader: 'sass-loader',
                            options: { sourceMap: true },
                        },
                    ],
                },
            ],
        },

        plugins: [
            /**
             * Extract compiled CSS into standalone files.
             */
            new MiniCssExtractPlugin({
                filename: cssFilename,
            }),

            /**
             * Generate a manifest that maps logical asset names to hashed outputs.
             * This powers ThemeContext::mix() (Laravel-style).
             *
             * Notes:
             * - Keep only initial (entry) assets, excluding sourcemaps.
             * - Keys are normalized to: "css/<entry>.css" and "js/<entry>.js"
             * - Values are paths relative to dist root: "css/<entry>.<hash>.css"
             *   (NOT "public-assets/dist/css/...").
             */
            new WebpackManifestPlugin({
                fileName: `${distRoot}/manifest/manifest.json`,
                publicPath: '',
                writeToFileEmit: true,

                filter: (file) => file.isInitial && !String(file.path).endsWith('.map'),

                generate: (seed, files) => {
                    const manifest = {};

                    for (const file of files) {
                        const name = String(file.name || '');
                        const emittedPath = String(file.path || '').replace(/\\/g, '/');

                        // Only care about our entry assets: main/admin css/js
                        // file.name examples:
                        // - "main.js"   | "main.css"
                        // - "admin.js"  | "admin.css"
                        const m = name.match(/^(main|admin)\.(css|js)$/);
                        if (!m) continue;

                        const entryName = m[1];
                        const ext = m[2];

                        // Convert emittedPath to dist-relative:
                        // "public-assets/dist/css/main.<hash>.css" -> "css/main.<hash>.css"
                        let distRelative = emittedPath;

                        // Strip leading slashes (defensive)
                        distRelative = distRelative.replace(/^\/+/, '');

                        // Ensure we only store values relative to dist root
                        const idx = distRelative.indexOf(distRootNormalized);
                        if (idx !== -1) {
                            distRelative = distRelative.slice(idx + distRootNormalized.length);
                        }

                        // Final keys match ThemeContext::mix() contract
                        manifest[`${ext}/${entryName}.${ext}`] = distRelative;
                    }

                    return manifest;
                },
            }),

            /**
             * Desktop notifications for build results.
             */
            new WebpackBuildNotifierPlugin({
                title: 'Yivic Lite Child Webpack Build',
                suppressSuccess: true,
            }),
        ],

        /**
         * Allow importing without specifying extensions.
         */
        resolve: {
            extensions: ['.js', '.jsx', '.ts', '.tsx'],
        },

        /**
         * Production optimizations.
         */
        optimization: {
            minimize: isProduction,
            minimizer: [
                new TerserPlugin({
                    parallel: true,
                    terserOptions: { compress: true },
                }),
                new CssMinimizerPlugin(),
            ],
        },

        stats: 'minimal',
    };
};
