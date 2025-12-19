<?php
/**
 * Yivic Lite Child Theme Autoloader
 *
 * This file provides a small, framework-agnostic, PSR-4 compatible
 * autoloader used instead of Composer's vendor/autoload.php.
 *
 * It is responsible for loading all classes that live under the
 * `Yivic\YivicLiteChild` root namespace.
 *
 * The implementation is intentionally lightweight so that it can be
 * shipped to WordPress.org without pulling in the full Composer stack,
 * while still remaining easy to extend in the future.
 */

declare ( strict_types = 1 );

if ( ! class_exists( 'YivicLiteChild_Theme_Autoloader', false ) ) {

    /**
     * Class YivicLiteChild_Theme_Autoloader
     *
     * Minimal PSR-4 style autoloader with support for multiple
     * namespace prefixes. The `Yivic\YivicLiteChild\` root namespace
     * is registered by default, but additional prefixes can be
     * registered via ::addNamespace().
     */
    class YivicLiteChild_Theme_Autoloader {

        /**
         * Map of namespace prefixes to base directories.
         *
         * Example:
         *  [
         *      'Yivic\\YivicLiteChild\\' => '/path/to/theme/src/'
         *  ]
         *
         * @var array<string,string>
         */
        protected static array $prefixes = [];

        /**
         * Registers this autoloader with the SPL autoload stack.
         *
         * This method is idempotent and can be safely called multiple times;
         * the loader will only be registered once.
         *
         * @return void
         */
        public static function register(): void {
            static $registered = false;

            if ( $registered ) {
                return;
            }

            // Base directory for all project classes (theme "src" folder).
            $baseDir = __DIR__ . DIRECTORY_SEPARATOR;

            // Register the primary root namespace for the theme.
            // PSR-4 mapping:
            //   Yivic\YivicLiteChild\*  =>  src/*
            static::addNamespace( 'Yivic\\YivicLiteChild\\', $baseDir );

            // Register the loader with SPL.
            spl_autoload_register( [ static::class, 'loadClass' ] );

            $registered = true;
        }

        /**
         * Adds a namespace prefix → base directory mapping.
         *
         * You can call this from anywhere (for example, if you decide
         * to split some classes into a different directory in a future
         * version of the theme).
         *
         * @param string $prefix  Fully-qualified namespace prefix.
         * @param string $baseDir Absolute path to the source directory.
         *
         * @return void
         */
        public static function addNamespace( string $prefix, string $baseDir ): void {
            // Normalize the namespace prefix (ensure trailing backslash).
            $prefix = trim( $prefix, '\\' ) . '\\';

            // Normalize the directory path (ensure trailing directory separator).
            $baseDir = rtrim( $baseDir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;

            static::$prefixes[ $prefix ] = $baseDir;
        }

        /**
         * Attempts to load the file corresponding to a given class name.
         *
         * This follows the PSR-4 convention:
         *  - Strip the matching namespace prefix from the FQCN.
         *  - Replace namespace separators (`\`) with directory separators.
         *  - Append `.php` and prepend the mapped base directory.
         *
         * If the resulting file exists, it is required.
         *
         * @param string $class Fully-qualified class name.
         *
         * @return void
         */
        public static function loadClass( string $class ): void {
            // Iterate over all registered namespace prefixes.
            foreach ( static::$prefixes as $prefix => $baseDir ) {

                $len = strlen( $prefix );

                // If the class does not start with this prefix, move on.
                if ( strncmp( $prefix, $class, $len ) !== 0 ) {
                    continue;
                }

                // Class name relative to the namespace prefix.
                $relativeClass = substr( $class, $len );

                // Convert namespace separators to directory separators.
                $relativePath = str_replace( '\\', DIRECTORY_SEPARATOR, $relativeClass ) . '.php';

                // Build the absolute path to the file.
                $file = $baseDir . $relativePath;

                // Require the file if it exists. If not, just return silently
                // and allow other autoloaders (if any) to run.
                if ( is_file( $file ) ) {
                    require $file;
                }

                // Either way, we stop after the first matching prefix.
                return;
            }
        }
    }

    // Register the autoloader for the theme classes immediately.
    YivicLiteChild_Theme_Autoloader::register();
}