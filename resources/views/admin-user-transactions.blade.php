<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} Transactions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.25em] text-slate-500">Admin</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">{{ $user->name }} transactions</h1>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white">
                Back to users
            </a>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500">Email</p>
                    <p class="text-lg font-semibold text-slate-900">{{ $user->email }}</p>
                </div>
                <span class="rounded-full bg-[#183D2A] px-3 py-1.5 text-sm font-semibold text-white">
                    {{ $user->points ?? 0 }} pts
                </span>
            </div>

            @if ($transactions->isNotEmpty())
                <div class="space-y-3">
                    @foreach ($transactions as $transaction)
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">
                                    @if ($transaction->transaction_type == 'add_points')
                                        Add Points
                                    @elseif ($transaction->transaction_type == 'mission_reward')
                                        Mission Reward
                                    @elseif ($transaction->transaction_type == 'social_share_reward')
                                        Social Share Reward    
                                    @else
                                        Redeem Reward
                                    @endif
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $transaction->created_at ? $transaction->created_at->format('M d, Y h:i A') : 'N/A' }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-900">
                                    @if ($transaction->transaction_type == 'add_points')
                                        ₱{{ number_format($transaction->amount, 2) }}
                                    @else
                                        {{ number_format((int) $transaction->points) }} pts
                                    @endif
                                </p>
                                <p class="text-[10px] uppercase tracking-[0.2em] {{ $transaction->status == 1 ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ $transaction->status == 1 ? 'Approved' : 'Pending' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($transactions->hasPages())
                    <div class="mt-5">
                        {{ $transactions->links() }}
                    </div>
                @endif
            @else
                <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-5 text-sm text-slate-500">
                    No transactions found for this user.
                </div>
            @endif
        </div>
    </div>
</body>
</html>
