<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Legacy Admin Back-Office
    |--------------------------------------------------------------------------
    |
    | The old admin dashboard, orders, standalone menu/category/stock pages,
    | tables, cafe settings, and activity log routes are disabled by default.
    | Set FEATURE_LEGACY_ADMIN=true to expose them again without code changes.
    |
    */

    'legacy_admin' => env('FEATURE_LEGACY_ADMIN', false),

];
