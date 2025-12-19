<?php
declare( strict_types = 1 );

namespace Yivic\YivicLiteChild\App\Support\Traits;

trait YivicLiteChildTransTrait {
    /**
     * Return the text domain used by translation helpers.
     * Child kernel can override this if needed.
     */
    protected function getTextDomain(): string {
        return 'yivic-lite-child';
    }

    /**
     * Translate a string using theme text domain.
     *
     * @param string $message
     * @return string
     */
    protected function __( string $message ): string {
        // phpcs:ignore WordPress.WP.I18n.NonSingularStringLiteralText
        return \__( $message, $this->getTextDomain() );
    }
}
