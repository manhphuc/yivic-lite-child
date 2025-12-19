<?php

declare( strict_types = 1 );

$textDomain = 'yivic-lite-child';

return [
    'version'    => YIVIC_LITE_CHILD_VERSION,

    // Context-aware root (child theme)
    'basePath'   => get_stylesheet_directory(),
    'baseUrl'    => get_stylesheet_directory_uri(),

    // i18n isolation
    'textDomain' => $textDomain,

    // Identity
    'themeSlug'  => 'yivic-lite-child',

    // Extension point
    'services'   => [],
];