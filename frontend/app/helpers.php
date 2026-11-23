<?php

if (! function_exists('media_url')) {
    /**
     * Public URL of a picture uploaded to the API (profile and hospital pictures).
     * Full URLs (e.g. external avatars) are returned unchanged.
     */
    function media_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (preg_match('#^https?://#', $path)) {
            return $path;
        }

        return rtrim(config('services.api.media_url'), '/').'/'.ltrim($path, '/');
    }
}
