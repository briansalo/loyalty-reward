<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rewards</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <div class="min-h-dvh bg-[#FAF9F6] text-[#222624]">
    <div class="mx-auto flex min-h-dvh max-w-md flex-col bg-white">
      <header class="flex items-center justify-between px-6 pt-10">
        <div>
          <h1 class="text-3xl font-semibold tracking-tight text-[#183D2A]">
            {{ $business->name ?? 'Business Name' }}
          </h1>
          <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.3em] text-gray-500">
            Rewards
          </p>
        </div>

        <a href="{{ route('dashboard') }}" class="flex size-11 items-center justify-center rounded-full bg-[#EFF2EA] text-[#183D2A]" aria-label="Back to dashboard">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 18 9 12l6-6"/>
          </svg>
        </a>
      </header>

      <main class="flex-1 px-6 pt-8 pb-8">
        <div class="rounded-3xl bg-[#F1F5ED] px-5 py-5">
          <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#5F6A64]">Your points</p>
          <p class="mt-2 text-4xl font-bold text-[#183D2A]">
            {{ auth()->user()->points ?? 0 }}
            <span class="text-xl font-semibold text-[#222624]">points</span>
          </p>
        </div>

        <section class="mt-7">
          <div class="mb-4 flex items-center justify-between">
            <h2 class="text-xl font-semibold text-[#183D2A]">Available rewards</h2>
          </div>

          <div class="space-y-3">
            @php
              $userPoints = auth()->user()->points ?? 0;
            @endphp

            @forelse ($rewards as $reward)
              @php
                $canRedeem = $userPoints >= $reward->points;
              @endphp

              <div class="flex items-center justify-between gap-3 rounded-2xl border border-[#E9EEE7] bg-white px-4 py-4 shadow-sm">
                <div>
                  <p class="font-semibold text-[#1E2926]">{{ $reward->name }}</p>
                  <p class="text-xs uppercase tracking-[0.2em] text-[#5F6A64]">{{ $reward->points }} pts</p>
                </div>

                <div class="flex items-center gap-2">
                  <span class="rounded-full bg-[#F1F5ED] px-3 py-1 text-sm font-semibold text-[#183D2A]">
                    {{ $reward->points }} pts
                  </span>

                  @if ($canRedeem)
                    <a href="{{ route('reward.redeem', $reward->id) }}" class="rounded-full bg-[#183D2A] px-3 py-2 text-xs font-semibold text-white">
                      Redeem
                    </a>
                  @else
                    <button type="button" disabled class="cursor-not-allowed rounded-full bg-[#E9EEE7] px-3 py-2 text-xs font-semibold text-[#7A817F]">
                      Need more
                    </button>
                  @endif
                </div>
              </div>
            @empty
              <div class="rounded-2xl border border-dashed border-[#D9DED7] bg-[#F8F9F6] px-4 py-4 text-sm text-[#5F6A64]">
                No rewards available yet.
              </div>
            @endforelse
          </div>
        </section>
      </main>

      <nav class="sticky bottom-0 z-10 grid grid-cols-3 border-t border-[#EBEEE8] bg-white/95 px-4 pb-5 pt-3 backdrop-blur-sm">
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 text-[#626967]">
          <svg class="size-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3 2.5 11h2.2v9h5.5v-6h3.6v6h5.5v-9h2.2L12 3Z" />
          </svg>
          <span class="text-sm">Home</span>
        </a>

        <a href="{{ route('rewards') }}" class="flex flex-col items-center gap-1 text-[#183D2A]">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="9" width="18" height="12" rx="1" />
            <path d="M12 9v12M3 13h18M12 9C6 9 6 3 9 3c2 0 3 3 3 6Zm0 0c6 0 6-6 3-6-2 0-3 3-3 6Z" />
          </svg>
          <span class="text-sm font-medium">Rewards</span>
          <span class="h-0.5 w-12 rounded-full bg-[#183D2A]"></span>
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
