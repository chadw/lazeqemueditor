<?php

namespace App\Http\Controllers;

use App\Filters\AccountFilter;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\AccountIp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = (new AccountFilter($request))
            ->apply(Account::query())
            ->select([
                'id',
                'name',
                'charname',
                'status',
                'lsaccount_id',
                'suspendeduntil',
                'time_creation',
                'ban_reason',
                'suspend_reason',
            ])
            ->with('character')
            ->sortable('id')
            ->paginate(100)
            ->withQueryString();

        return view('accounts.index', compact('accounts'));
    }

    public function show(Account $account, Request $request)
    {
        $account->load([
            'characters' => function ($q) {
                $q->select(
                    'id',
                    'account_id',
                    'name',
                    'race',
                    'class',
                    'level',
                    'birthday',
                    'last_login',
                    'time_played'
                );
            },
            'ips',
            'sharedbank',
        ]);

        return view('accounts.show', compact('account'));
    }

    public function store(AccountRequest $request)
    {
        $data = $request->validated();

        Account::create($data);

        return back()->with('success', 'Account created.');
    }

    public function update(AccountRequest $request, Account $account)
    {
        $data = $request->validated();
        //$data['suspendeduntil'] = $data['suspendeduntil'] ? \Carbon\Carbon::parse($data['suspendeduntil']) : null;
        $account->update($data);

        toast()->success('Saved!', "Account updated.");

        return response()->json([
            'success' => true,
            'data'    => $account,
            'redirect'=> url()->previous(),
        ], 201);
    }

    public function destroy(Account $account)
    {
        $accountName = $account->name;

        try {
            DB::transaction(function () use ($account) {
                $account = Account::findOrFail($account->id);

                $account->ips()->delete();
                $account->rewards()->delete();
                $account->gmIps()->delete();
                $account->sharedBank()->delete();

                $account->delete();
            });
        } catch (\Throwable $e) {
            toast()->error('Error', 'Failed to delete account [' . $accountName . ']: ' . $e->getMessage());

            return back();
        }

        toast()->success('Saved!', 'Account [' . $accountName . '] successfully deleted.');

        return back();
    }

    public function ipsFor(Account $account, $ip)
    {
        $decodedIp = rawurldecode($ip);

        $others = AccountIp::with('account')
            ->where('ip', $decodedIp)
            ->where('accid', '<>', $account->id)
            ->orderBy('lastused', 'desc')
            ->get();

        return view('accounts.partials.ip-others', ['others' => $others]);
    }

    public function search(Request $request)
    {
        $search = $request->string('q');

        return Account::query()
            ->select('id', 'name')
            ->when($search, function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhere('name', 'like', "%{$search}%");
            })
            ->orderBy('id')
            ->limit(50)
            ->get();
    }
}
