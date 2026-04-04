<?php

namespace App\Http\Controllers;

use App\Models\RuleValue;
use Illuminate\Http\Request;
use App\Http\Requests\RuleValueRequest;

class ServerRuleController extends Controller
{
    public function index(Request $request)
    {
        $rules = RuleValue::orderBy('rule_name')->get();

        return view('server-rules.index', compact('rules'));
    }

    public function update(RuleValueRequest $request, string $rule_name)
    {
        $rule = RuleValue::findOrFail($rule_name);

        $request->validate([
            'value' => ['required', 'string'],
        ]);

        $rule->update([
            'rule_value' => $request->value,
        ]);

        return response()->json(['success' => true, 'value' => $rule->rule_value]);
    }
}
