<?php

declare(strict_types=1);

namespace Yivic\YivicLiteChild\Foundation\ThemeApp;

use RuntimeException;
use Yivic\YivicLiteChild\Foundation\Application;

final class ThemeAppRuntime
{
    /**
     * Global runtime key used to store the Application instance.
     *
     * NOTE:
     * - We intentionally keep this as an internal constant to avoid magic strings
     *   scattered across the codebase.
     */
    private const GLOBAL_KEY = 'yivic_theme_app';

    /**
     * Resolve the theme Application instance (boot once per request).
     *
     * Policy:
     * - If an app instance already exists, return it (no re-bootstrap).
     * - If basePath is invalid, fail fast with a clear message.
     */
    public static function resolve(string $basePath, array $config = []): Application
    {
        $existing = self::fromGlobals();
        if ($existing instanceof Application) {
            return $existing;
        }

        $basePath = \trim($basePath);
        if ($basePath === '') {
            throw new RuntimeException('[Yivic Lite Child] Theme application basePath is required.');
        }

        $app = new Application($basePath, $config);
        $app->bootstrap();

        $GLOBALS[self::GLOBAL_KEY] = $app;

        return $app;
    }

    /**
     * Get the resolved Application instance.
     *
     * @throws RuntimeException When called before resolve().
     */
    public static function app(): Application
    {
        $app = self::fromGlobals();
        if (!$app instanceof Application) {
            throw new RuntimeException('[Yivic Lite Child] Theme application is not bootstrapped. Call ThemeAppRuntime::resolve() first.');
        }

        return $app;
    }

    /**
     * Best-effort accessor (no exception).
     * Useful for optional integrations.
     */
    public static function maybeApp(): ?Application
    {
        $app = self::fromGlobals();
        return $app instanceof Application ? $app : null;
    }

    private static function fromGlobals(): mixed
    {
        return $GLOBALS[self::GLOBAL_KEY] ?? null;
    }
}
