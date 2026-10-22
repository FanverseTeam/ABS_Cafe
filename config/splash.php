<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Splash Screen Duration
    |--------------------------------------------------------------------------
    |
    | Durasi pemutaran animasi splash screen pada route /staf sebelum
    | diarahkan otomatis ke form login (/login), dalam satuan detik (float/int).
    | Nilai default: 3.0 detik. Dapat diubah melalui environment SPLASH_DURATION.
    |
    */

    'duration' => (float) env('SPLASH_DURATION', 3.0),

];
