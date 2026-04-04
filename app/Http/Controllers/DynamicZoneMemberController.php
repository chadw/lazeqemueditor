<?php

namespace App\Http\Controllers;

use App\Models\DynamicZoneMember;
use Illuminate\Http\Request;

class DynamicZoneMemberController extends Controller
{
    public function destroy(DynamicZoneMember $dynamicZoneMember)
    {
        $dynamicZoneMember->delete();

        return response()->json([
            'success' => true,
            'id' => $dynamicZoneMember->id,
        ]);
    }
}
