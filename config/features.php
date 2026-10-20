<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Legacy Admin Back-Office
    |--------------------------------------------------------------------------
    |
    | The old admin orders, standalone menu/category/stock pages, tables,
    | cafe settings, and activity log routes are disabled by default. The
    | Dashboard is always active. Set FEATURE_LEGACY_ADMIN=true to expose the
    | remaining legacy pages and dashboard widgets again without code changes.
    |
    */

    'legacy_admin' => env('FEATURE_LEGACY_ADMIN', false),

];
