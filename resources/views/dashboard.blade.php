<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

      <!-- Header -->
      <header class="flex items-center justify-between px-6 pt-10">
        <div>
          <h1 class="text-3xl font-semibold tracking-tight text-[#183D2A]">
             Business Name
          </h1>
          <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.3em] text-gray-500">
            Good coffee · Brighter days
          </p>
        </div>

        <button
          type="button"
          aria-label="Open profile"
          class="flex size-11 items-center justify-center rounded-full bg-[#EFF2EA] text-[#183D2A]"
        >
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <circle cx="12" cy="8" r="3.5" />
            <path stroke-linecap="round" d="M5 20c0-4 2.6-6 7-6s7 2 7 6" />
          </svg>
        </button>
      </header>

      <!-- Home content -->
      <main class="flex-1 px-6 pt-14">
        <h2 class="text-2xl font-semibold tracking-tight">
          Good afternoon,
        </h2>

        <section
          aria-label="Your loyalty points"
          class="mt-10 rounded-3xl bg-[#F1F5ED] px-7 py-9"
        >
          <p class="text-6xl font-bold leading-none tracking-tight text-[#183D2A]">
            {{ $points ?? 0 }}
            <span class="text-4xl font-semibold text-[#222624]">points</span>
          </p>

          <p class="mt-5 text-base text-[#59605B]">
            Earn 1 point for every
            <span class="font-semibold text-[#222624]">₱{{ $business->amount_per_points ?? 0 }}</span>
            spent.
          </p>
        </section>

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
          <section class="mt-8">
            <div class="mb-3 flex items-center justify-between gap-3">
              <h3 class="text-lg font-semibold text-[#183D2A]">Monthly mission</h3>
              @if ($mission['claimed'])
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-emerald-700">Claimed</span>
              @elseif ($mission['eligible'])
                <form method="POST" action="{{ route('mission.claim') }}">
                  @csrf
                  <button type="submit" class="rounded-full bg-[#183D2A] px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] text-white">
                    Claim
                  </button>
                </form>
              @else
                <span class="text-sm font-medium text-[#183D2A]">{{ $mission['count'] }}/{{ $mission['goal'] }}</span>
              @endif
            </div>

            <div class="rounded-2xl border border-[#E9EEE7] bg-[#F8FAF7] p-4 shadow-sm">
              <p class="text-sm text-[#59605B]">Make {{ $mission['goal'] }} purchases this month to earn {{ $mission['reward'] }} points.</p>
              <div class="mt-3 h-2.5 rounded-full bg-[#E7EAE6]">
                <div class="h-2.5 rounded-full bg-[#183D2A]" style="width: {{ min(100, ($mission['count'] / max(1, $mission['goal'])) * 100) }}%"></div>
              </div>
              <p class="mt-2 text-sm font-medium text-[#183D2A]">Progress: {{ $mission['count'] }} / {{ $mission['goal'] }}</p>
            </div>
          </section>
        @endif

        @if (($socialShareMission['enabled'] ?? true) && $business)
          <section class="mt-8">
            <div class="mb-3 flex items-center justify-between gap-3">
              <h3 class="text-lg font-semibold text-[#183D2A]">Social share</h3>
              @if ($socialShareMission['claimed'])
                <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold uppercase tracking-[0.15em] text-emerald-700">Claimed</span>
              @elseif ($socialShareMission['eligible'])
                <form method="POST" action="{{ route('social.share.submit') }}">
                  @csrf
                  <button type="submit" class="rounded-full bg-[#183D2A] px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.12em] text-white">
                    Claim {{ $socialShareMission['reward'] }} pts
                  </button>
                </form>
              @else
                <span class="text-sm font-medium text-[#183D2A]">{{ $socialShareMission['reward'] }} pts</span>
              @endif
            </div>

            <div class="rounded-2xl border border-[#E9EEE7] bg-[#F8FAF7] p-4 shadow-sm">
              <p class="text-sm text-[#59605B]">{{ $socialShareMission['description'] ?? 'Post a photo featuring your item purchase, tag our page, and earn points.' }}</p>
            </div>
          </section>
        @endif

        <div class="mt-6">
          <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center rounded-full bg-[#183D2A] px-4 py-2 text-sm font-medium text-white shadow-sm">
            Admin settings
          </a>
        </div>
      </main>

      <!-- Bottom navigation -->
      <nav class="sticky bottom-0 z-10 grid grid-cols-3 border-t border-[#EBEEE8] bg-white/95 px-4 pb-5 pt-3 backdrop-blur-sm">
        <a href="/home" aria-current="page" class="flex flex-col items-center gap-1 text-[#183D2A]">
          <svg class="size-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3 2.5 11h2.2v9h5.5v-6h3.6v6h5.5v-9h2.2L12 3Z" />
          </svg>
          <span class="text-sm font-medium">Home</span>
          <span class="h-0.5 w-12 rounded-full bg-[#183D2A]"></span>
        </a>

        <a href="{{ route('rewards') }}" class="flex flex-col items-center gap-1 text-[#626967]">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="9" width="18" height="12" rx="1" />
            <path d="M12 9v12M3 13h18M12 9C6 9 6 3 9 3c2 0 3 3 3 6Zm0 0c6 0 6-6 3-6-2 0-3 3-3 6Z" />
          </svg>
          <span class="text-sm">Rewards</span>
        </a>

        <a href="{{ route('activity') }}" class="flex flex-col items-center gap-1 text-[#626967]">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="5" y="3" width="14" height="18" rx="2" />
            <path stroke-linecap="round" d="M9 9h6M9 13h6M9 17h4" />
          </svg>
          <span class="text-sm">Activity</span>
        </a>
      </nav>
    </div>
  </div>

</body>
</html>
