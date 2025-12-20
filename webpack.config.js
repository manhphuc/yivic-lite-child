/**
 * webpack.config.js
 *
 * Main Webpack configuration entrypoint.
 *
 * Responsibilities:
 * - Detect the current build mode (development/production).
 * - Delegate all pipeline logic to webpack-base.config.js.
 * - Keep this file minimal and predictable.
 *
 * This is intentionally thin to ensure:
 * - One place for pipeline logic (webpack-base.config.js)
 * - One place for paths (webpack.var.config.js)
 */

const baseConfig = require('./webpack-base.config');
const pluginVariables = require('./webpack.var.config');

module.exports = function (env, argv) {
    const mode = argv.mode || 'development';
    return baseConfig.buildConfig(pluginVariables, mode);
};
