<?php

namespace App\Http\Controllers;

use App\Services\UpdateChecker;
use Illuminate\Http\Request;

class UpdateController
{
    public function check(Request $request, UpdateChecker $checker)
    {
        $result = $checker->checkForUpdate();
        return response()->json($result);
    }
}
