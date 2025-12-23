<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Html;

/**
 * Class HtmlAttributes
 *
 * A tiny, framework-friendly HTML attribute builder.
 *
 * Goals:
 * - Security: escape all attribute names/values, block dangerous attributes (on*).
 * - Maintainability: centralized logic for boolean attributes, arrays, and style.
 * - Extensibility: reusable for all HTML helpers (Input, Select, Textarea, Button...).
 */
final class HtmlAttributes {
    /**
     * Escape text content (text nodes), e.g. label text, textarea content.
     * Use this for body content, not attribute values.
     */
    public static function escape( string $text ): string {
        return htmlspecialchars( $text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }

    /**
     * Build a safe HTML attribute string from an associative array.
     *
     * Supported:
     * - ['class' => 'a b']
     * - ['class' => ['a', 'b']]  -> "a b"
     * - ['disabled' => true]     -> disabled="disabled"
     * - ['disabled' => false]    -> omitted
     * - ['style' => ['width' => '10px', 'display' => 'none']] -> style="width:10px;display:none"
     * - ['data-x' => '1']        -> data-x="1"
     *
     * @param array<string, mixed> $attrs
     */
    public static function build( array $attrs ): string {
        if ( $attrs === [] ) {
            return '';
        }

        $parts = [];

        foreach ( $attrs as $name => $value ) {
            if ( $name === '' || $value === null ) {
                continue;
            }

            $rawName = trim( (string) $name );
            if ( $rawName === '' ) {
                continue;
            }

            // Hardening: block event handler attributes (onclick, onload, etc.)
            // If you ever need them, explicitly allow via a controlled whitelist.
            if ( preg_match( '/^on[a-z]+\z/i', $rawName ) ) {
                continue;
            }

            // Normalize & validate attribute name
            $attrName = self::escapeAttrName( $rawName );
            if ( $attrName === '' ) {
                continue;
            }

            // Boolean attributes
            if ( is_bool( $value ) ) {
                if ( $value === true ) {
                    $parts[] = sprintf( '%s="%s"', $attrName, $attrName );
                }
                continue;
            }

            // class can be array
            if ( $attrName === 'class' && is_array( $value ) ) {
                $value = implode( ' ', array_values( array_filter( array_map( 'strval', $value ) ) ) );
            }

            // style can be array
            if ( $attrName === 'style' && is_array( $value ) ) {
                $value = self::buildStyle( $value );
            }

            // If value is still an array/object, safely stringify
            if ( is_array( $value ) ) {
                $value = implode( ' ', array_values( array_filter( array_map( 'strval', $value ) ) ) );
            } elseif ( is_object( $value ) ) {
                $value = method_exists( $value, '__toString' ) ? (string) $value : '';
            }

            $attrValue = self::escapeAttrValue( (string) $value );

            // Allow empty string values for attrs like value="" or placeholder=""
            $parts[] = sprintf( '%s="%s"', $attrName, $attrValue );
        }

        return $parts ? ' ' . implode( ' ', $parts ) : '';
    }

    /**
     * Build inline style from array.
     *
     * @param array<string, mixed> $styles
     */
    private static function buildStyle( array $styles ): string {
        $pairs = [];

        foreach ( $styles as $k => $v ) {
            $k = trim( (string) $k );
            if ( $k === '' || $v === null ) {
                continue;
            }

            // Only allow safe style property name characters
            // (letters, numbers, dash). Prevent: "width;position:fixed" etc.
            if ( ! preg_match( '/\A[a-zA-Z0-9\-]+\z/', $k ) ) {
                continue;
            }

            // Convert scalar values only
            if ( ! is_scalar( $v ) ) {
                continue;
            }

            $v = trim( (string) $v );

            // Strip dangerous characters from style value
            // Prevent: url("javascript:..."), quotes, semicolons, etc.
            $v = str_replace( [ '"', "'", ';' ], '', $v );

            $pairs[] = $k . ':' . $v;
        }

        return implode( ';', $pairs );
    }

    /**
     * Escape attribute name.
     * - Only allow common safe characters: letters, numbers, dash, underscore, colon.
     */
    private static function escapeAttrName( string $name ): string {
        $name = trim($name);

        // Remove dangerous characters
        $safe = preg_replace( '/[^a-zA-Z0-9\-\_\:]/', '', $name );

        return is_string( $safe ) ? $safe : '';
    }

    /**
     * Escape attribute value for HTML output.
     */
    private static function escapeAttrValue( string $value ): string {
        return htmlspecialchars( $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8' );
    }
}
