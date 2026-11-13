<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Marketing tracking
    |--------------------------------------------------------------------------
    | META_PIXEL_ID is issued by Meta Events Manager (a string of digits).
    | Leaving it blank keeps the pixel out of the HTML entirely.
    */

    'meta_pixel_id' => env('META_PIXEL_ID'),

    // Used while testing events from Events Manager's Test Events tool.
    'meta_pixel_test_code' => env('META_PIXEL_TEST_CODE'),

];
