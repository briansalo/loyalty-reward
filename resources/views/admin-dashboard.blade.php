<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="mx-auto max-w-4xl px-4 py-10">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium uppercase tracking-[0.25em] text-slate-500">Admin</p>
                <h1 class="mt-2 text-3xl font-bold text-slate-900">Loyalty settings</h1>
            </div>
            <a href="{{ route('dashboard') }}" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white">
                Back to dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.dashboard.store') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="business_id" value="{{ $business->id }}">

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <label for="amount_per_points" class="block text-sm font-medium text-slate-700">Pesos for 1 point</label>
                <div class="mt-3 flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-3 py-3">
                    <span class="text-lg font-semibold text-slate-500">₱</span>
                    <input
                        id="amount_per_points"
                        name="amount_per_points"
                        type="number"
                        min="1"
                        step="0.01"
                        value="{{ old('amount_per_points', $business->amount_per_points ?? 50) }}"
                        class="w-full border-0 bg-transparent text-lg font-semibold text-slate-900 outline-none"
                        placeholder="50"
                    >
                </div>
                <p class="mt-2 text-sm text-slate-500">Example: 50 means 1 point is earned for every ₱50 spent.</p>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Monthly mission</h2>
                        <p class="mt-1 text-sm text-slate-500">Set the target number of purchase days and points reward for the current month.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                        <input
                            type="checkbox"
                            name="monthly_mission_enabled"
                            value="1"
                            {{ old('monthly_mission_enabled', $business->currentMonthlyMission()->enabled ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-[#183D2A] focus:ring-[#183D2A]"
                        >
                        Enable
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label for="monthly_purchase_goal" class="mb-2 block text-sm font-medium text-slate-700">Purchase days needed</label>
                        <input
                            id="monthly_purchase_goal"
                            name="monthly_purchase_goal"
                            type="number"
                            min="1"
                            value="{{ old('monthly_purchase_goal', $business->currentMonthlyMission()->purchase_goal ?? 4) }}"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]"
                        >
                    </div>

                    <div>
                        <label for="monthly_points_reward" class="mb-2 block text-sm font-medium text-slate-700">Points awarded</label>
                        <input
                            id="monthly_points_reward"
                            name="monthly_points_reward"
                            type="number"
                            min="1"
                            value="{{ old('monthly_points_reward', $business->currentMonthlyMission()->points_reward ?? 100) }}"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]"
                        >
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" class="rounded-full bg-[#183D2A] px-5 py-2.5 text-sm font-semibold text-white shadow-sm">
                        Save monthly mission
                    </button>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-900">Social share mission</h2>
                        <p class="mt-1 text-sm text-slate-500">Let customers earn points for posting a tagged café photo.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-medium text-slate-700">
                        <input
                            type="checkbox"
                            name="social_share_mission_enabled"
                            value="1"
                            {{ old('social_share_mission_enabled', $business->currentSocialShareMission()->enabled ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-slate-300 text-[#183D2A] focus:ring-[#183D2A]"
                        >
                        Enable
                    </label>
                </div>

                <div class="grid gap-4 md:grid-cols-1">
                    <div>
                        <label for="social_share_mission_text" class="mb-2 block text-sm font-medium text-slate-700">Mission text</label>
                        <textarea
                            id="social_share_mission_text"
                            name="social_share_mission_text"
                            rows="3"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]"
                            placeholder="Post a photo featuring your item purchase, tag our page, and earn points."
                        >{{ old('social_share_mission_text', $business->currentSocialShareMission()->description ?? 'Post a photo featuring your item purchase, tag our page, and earn points.') }}</textarea>
                    </div>

                    <div>
                        <label for="social_share_points_reward" class="mb-2 block text-sm font-medium text-slate-700">Points for each approved share</label>
                        <input
                            id="social_share_points_reward"
                            name="social_share_points_reward"
                            type="number"
                            min="1"
                            value="{{ old('social_share_points_reward', $business->currentSocialShareMission()->points_reward ?? 5) }}"
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]"
                        >
                    </div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" class="rounded-full bg-[#183D2A] px-5 py-2.5 text-sm font-semibold text-white shadow-sm">
                        Save social share mission
                    </button>
                </div>
            </div>

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-slate-900">Rewards</h2>
                    <button type="button" id="addRewardButton" class="rounded-full bg-[#183D2A] px-3 py-1.5 text-sm font-medium text-white">
                        + Add reward
                    </button>
                </div>

                <div id="rewardList" class="space-y-4">
                    @php
                        $rewards = $rewards ?? collect();
                    @endphp

                    @forelse ($rewards as $index => $reward)
                        <div class="reward-row grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 md:grid-cols-[1.5fr_1fr_auto]">
                            <input
                                type="text"
                                name="reward_name[]"
                                value="{{ old('reward_name.' . $index, $reward->name ?? '') }}"
                                placeholder="Reward name"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]"
                            >
                            <input
                                type="number"
                                name="reward_points[]"
                                min="1"
                                value="{{ old('reward_points.' . $index, $reward->points ?? 0) }}"
                                placeholder="Points"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]"
                            >
                            <button type="button" class="removeRewardButton rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-600">
                                Remove
                            </button>
                        </div>
                    @empty
                        <div class="reward-row grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 md:grid-cols-[1.5fr_1fr_auto]">
                            <input type="text" name="reward_name[]" value="" placeholder="Reward name" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]">
                            <input type="number" name="reward_points[]" min="1" value="" placeholder="Points" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]">
                            <button type="button" class="removeRewardButton rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-600">
                                Remove
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-full bg-[#183D2A] px-6 py-3 text-sm font-semibold text-white shadow-sm">
                    Save settings
                </button>
            </div>
        </form>

        <div class="mt-10 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-slate-900">Users</h2>
                <span class="rounded-full bg-[#F1F5ED] px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-[#183D2A]">
                    {{ $users->total() }} users
                </span>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200 text-left">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Name</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Email</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Points</th>
                            <th class="px-4 py-3 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Transactions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-50">
                                <td class="px-4 py-3">
                                    <a href="{{ route('admin.user.transactions', $user) }}" class="font-semibold text-[#183D2A] hover:underline">
                                        {{ $user->name }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-600">{{ $user->email }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $user->points ?? 0 }} pts</td>
                                <td class="px-4 py-3 text-sm text-slate-600">
                                    {{ $user->transactions->count() }} item(s)
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-5 text-sm text-slate-500">
                                    No users found for this business.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="mt-5">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <script>
        const addRewardButton = document.getElementById('addRewardButton');
        const rewardList = document.getElementById('rewardList');

        addRewardButton.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'reward-row grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 md:grid-cols-[1.5fr_1fr_auto]';
            row.innerHTML = `
                <input type="text" name="reward_name[]" value="" placeholder="Reward name" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]">
                <input type="number" name="reward_points[]" min="1" value="" placeholder="Points" class="rounded-lg border border-slate-200 bg-white px-3 py-2.5 outline-none ring-0 focus:border-[#183D2A]">
                <button type="button" class="removeRewardButton rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm font-medium text-red-600">
                    Remove
                </button>
            `;

            rewardList.appendChild(row);

            row.querySelector('.removeRewardButton').addEventListener('click', () => {
                row.remove();
            });
        });

        document.querySelectorAll('.removeRewardButton').forEach((button) => {
            button.addEventListener('click', () => {
                const row = button.closest('.reward-row');
                if (row) {
                    row.remove();
                }
            });
        });
    </script>
</body>
</html>
