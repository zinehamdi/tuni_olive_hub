<?php

use App\Services\SrCode;

it('generates a code with checksum', function () {
    $code = SrCode::make('TRP');
    expect($code)->toMatch('/^TRP-[A-Z0-9]{5}-\d$/');
});
