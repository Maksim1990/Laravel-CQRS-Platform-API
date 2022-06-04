<?php

namespace App\Http\Controllers\Api;

use App\Models\Account;
use App\Domain\Account\AccountAggregateRoot;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends BaseApiController
{
    public function index()
    {
        $accounts = Account::all();

        return response()->json($accounts);
    }

    public function show(int $id)
    {
        $account = Account::find($id);

        if ($account === null) {
            return response()->json(
                [
                'code' => Response::HTTP_NOT_FOUND,
                'message' => 'Item not found'
                ]
            );
        }

        return response()->json($account);
    }

    public function store(Request $request)
    {
        $newUuid = Str::uuid()->toString();

        AccountAggregateRoot::retrieve($newUuid)
            ->createAccount($request->name, 1)
            ->persist();

        return response()->json(['status' => 'created']);
    }

    public function update(Account $account, UpdateAccountRequest $request)
    {
        $aggregateRoot = AccountAggregateRoot::retrieve($account->uuid);

        $request->adding()
            ? $aggregateRoot->addMoney($request->amount)
            : $aggregateRoot->subtractMoney($request->amount);

        $aggregateRoot->persist();

        return response()->json(['status' => 'updated']);
    }

    public function destroy(Account $account)
    {
        AccountAggregateRoot::retrieve($account->uuid)
            ->deleteAccount()
            ->persist();

        return response()->json(['status' => 'deleted']);
    }

    public function snapshot(Account $account)
    {
        $account = AccountAggregateRoot::retrieve($account->uuid);

        return response()->json($account->snapshot());
    }

    public function restore(Account $account)
    {
        $version = AccountAggregateRoot::retrieve($account->uuid)->restoreStateFromAggregateVersion(4);

        return response()->json($version);
    }
}
