<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Redeem Reward</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
  <div class="min-h-dvh bg-[#FAF9F6] text-[#222624]">
    <div class="mx-auto flex min-h-dvh max-w-md flex-col bg-white shadow-lg">
      <header class="bg-[#183D2A] px-6 py-4 flex items-center justify-between text-white">
        <h1 class="text-xl font-semibold tracking-tight">{{ $business->name ?? 'Business Name' }}</h1>
        <div class="flex size-9 items-center justify-center rounded-full bg-white/20">
          <svg class="size-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
        </div>
      </header>

      <main class="flex-1 px-6 pt-8 pb-8 flex flex-col justify-between">
        <div>
          <div class="text-center">
            <h2 class="text-3xl font-bold tracking-tight text-[#183D2A]">
              Redeem reward
            </h2>
            <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-[#F3A851]/20 px-4 py-1.5 text-xs font-semibold text-[#D97706]">
              <span class="size-2 rounded-full bg-[#D97706] animate-pulse"></span>
              Confirmation needed
            </div>
          </div>

          <div class="mt-8 rounded-3xl bg-[#F1F5ED] p-6 text-center shadow-sm">
            <p class="text-sm font-medium text-[#59605B]">Reward</p>
            <div class="mt-4">
              <p class="text-3xl font-extrabold text-[#183D2A] tracking-tight">
                {{ $reward->name }}
              </p>
              <p class="mt-1 text-xs font-medium text-gray-500 uppercase tracking-wider">
                Redeem with points
              </p>
            </div>

            <hr class="my-5 border-gray-200" />

            <div class="text-center">
              <p class="text-2xl font-bold text-[#183D2A]">
                -{{ $points }} points
              </p>
            </div>
          </div>
        </div>

        <div class="mt-8 space-y-3">
          <form method="POST" action="{{ route('reward.redeem.confirm', $reward->id) }}">
            @csrf
            <button type="submit" class="block w-full rounded-full bg-[#183D2A] px-4 py-3 text-center text-sm font-semibold text-white">
              Confirm Redeem
            </button>
          </form>

          <a href="{{ route('rewards') }}" class="block w-full text-center text-sm font-medium text-gray-600 underline hover:text-gray-900 transition">
            Cancel
          </a>
        </div>
      </main>
    </div>
  </div>
</body>
</html>
