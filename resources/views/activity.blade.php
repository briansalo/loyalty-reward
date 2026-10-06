<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <div class="min-h-dvh bg-[#FAF9F6] text-[#222624]">
    <div class="mx-auto flex min-h-dvh max-w-md flex-col bg-white">
      @if (session('success'))
        <div class="mx-6 mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
          {{ session('success') }}
        </div>
      @endif

      @if (session('error'))
        <div class="mx-6 mt-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
          {{ session('error') }}
        </div>
      @endif

      <header class="flex items-center justify-between px-6 pt-10">
        <div>
          <h1 class="text-3xl font-semibold tracking-tight text-[#183D2A]">
            {{ $business->name ?? 'Business Name' }}
          </h1>
          <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.3em] text-gray-500">
            Activity
          </p>
        </div>

        <a href="{{ route('dashboard') }}" class="flex size-11 items-center justify-center rounded-full bg-[#EFF2EA] text-[#183D2A]" aria-label="Back to dashboard">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 18 9 12l6-6"/>
          </svg>
        </a>
      </header>

      <main class="flex-1 px-6 pt-8 pb-8">
        @php
          $mission = $mission ?? [
              'count' => 0,
              'goal' => (int) ($business ? ($business->currentMonthlyMission()->purchase_goal ?? 4) : 4),
              'reward' => (int) ($business ? ($business->currentMonthlyMission()->points_reward ?? 100) : 100),
              'enabled' => $business ? ($business->currentMonthlyMission()->enabled ?? true) : true,
              'claimed' => false,
              'eligible' => false,
          ];
        @endphp

        @if (($mission['enabled'] ?? true) && $business)
          <div class="mt-0 rounded-3xl border border-[#E9EEE7] bg-[#F8FAF7] p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
              <div>
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-[#5F6A64]">Monthly mission</p>
                <p class="mt-1 text-lg font-semibold text-[#183D2A]">{{ $mission['count'] }} / {{ $mission['goal'] }} purchases</p>
              </div>
              @if ($mission['claimed'])
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-emerald-700">Claimed</span>
              @elseif ($mission['eligible'])
                <form method="POST" action="{{ route('mission.claim') }}">
                  @csrf
                  <button type="submit" class="rounded-full bg-[#183D2A] px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] text-white">
                    Claim {{ $mission['reward'] }} pts
                  </button>
                </form>
              @else
                <span class="text-sm font-medium text-[#183D2A]">{{ $mission['reward'] }} pts</span>
              @endif
            </div>
          </div>
        @endif

        @if (($socialShareMission['enabled'] ?? true) && $business)
          <div class="mt-4 rounded-3xl border border-[#E9EEE7] bg-[#F8FAF7] p-4 shadow-sm">
            <div class="flex items-center justify-between gap-3">
              <div>
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-[#5F6A64]">Social share</p>
                <p class="mt-1 text-sm text-[#183D2A]">{{ $socialShareMission['description'] ?? 'Post a photo featuring your item purchase, tag our page, and earn points.' }}</p>
              </div>
              @if ($socialShareMission['claimed'])
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-emerald-700">Claimed</span>
              @elseif ($socialShareMission['eligible'])
                <form method="POST" action="{{ route('social.share.claim') }}">
                  @csrf
                  <button type="submit" class="rounded-full bg-[#183D2A] px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] text-white">
                    Claim
                  </button>
                </form>
              @else
                <span class="text-sm font-medium text-[#183D2A]">{{ $socialShareMission['reward'] }} pts</span>
              @endif
            </div>
          </div>
        @endif

        <div class="mt-7 rounded-3xl bg-[#F1F5ED] px-5 py-5">
          <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#5F6A64]">Transaction history</p>
          <p class="mt-2 text-4xl font-bold text-[#183D2A]">
            {{ $transactions->total() }}
            <span class="text-xl font-semibold text-[#222624]">entries</span>
          </p>
        </div>

        <section class="mt-7">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#183D2A]">Recent activity</h2>
          </div>

          <div class="space-y-3">
            @forelse ($transactions as $transaction)
              <div class="rounded-2xl border border-[#E9EEE7] bg-white px-4 py-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                  <div>
                    <p class="font-semibold text-[#1E2926]">{{ $transaction->business->name ?? 'Business' }}</p>
                    <p class="text-xs uppercase tracking-[0.2em] text-[#5F6A64]">
                      {{ $transaction->created_at ? $transaction->created_at->format('M d, Y') : 'N/A' }}
                    </p>
                  </div>
                  <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $transaction->status == 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $transaction->status == 1 ? 'Approved' : 'Pending' }}
                  </span>
                </div>

                <div class="mt-3 flex items-center justify-between border-t border-[#F0F2EE] pt-3 text-sm">
                  <span class="text-[#5F6A64]">
                    @if ($transaction->transaction_type == 'add_points')
                      Add Points
                    @elseif ($transaction->transaction_type == 'mission_reward')
                      Mission Reward
                    @elseif ($transaction->transaction_type == 'social_share_reward')
                      Social Share Reward
                    @else
                      Redeem Reward
                    @endif
                  </span>
                  <span class="font-semibold text-[#1E2926]">
                    @if ($transaction->transaction_type == 'add_points')
                      ₱{{ number_format($transaction->amount, 2) }}
                    @else
                      {{ number_format((int) $transaction->points) }} points
                    @endif
                  </span>
                </div>
              </div>
            @empty
              <div class="rounded-2xl border border-dashed border-[#D9DED7] bg-[#F8F9F6] px-4 py-4 text-sm text-[#5F6A64]">
                No transactions yet.
              </div>
            @endforelse
          </div>

          @if ($transactions->hasPages())
            <div class="mt-6">
              {{ $transactions->links() }}
            </div>
          @endif
        </section>
      </main>

      <nav class="sticky bottom-0 z-10 grid grid-cols-3 border-t border-[#EBEEE8] bg-white/95 px-4 pb-5 pt-3 backdrop-blur-sm">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-[#626967]">
          <svg class="size-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3 2.5 11h2.2v9h5.5v-6h3.6v6h5.5v-9h2.2L12 3Z" />
          </svg>
          <span class="text-sm">Home</span>
        </a>

        <a href="{{ route('rewards') }}" class="flex flex-col items-center gap-1 text-[#626967]">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="9" width="18" height="12" rx="1" />
            <path d="M12 9v12M3 13h18M12 9C6 9 6 3 9 3c2 0 3 3 3 6Zm0 0c6 0 6-6 3-6-2 0-3 3-3 6Z" />
          </svg>
          <span class="text-sm">Rewards</span>
        </a>

        <a href="{{ route('activity') }}" class="flex flex-col items-center gap-1 text-[#183D2A]">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="5" y="3" width="14" height="18" rx="2" />
            <path stroke-linecap="round" d="M9 9h6M9 13h6M9 17h4" />
          </svg>
          <span class="text-sm font-medium">Activity</span>
          <span class="h-0.5 w-12 rounded-full bg-[#183D2A]"></span>
        </a>
      </nav>
    </div>
  </div>
</body>
</html>
