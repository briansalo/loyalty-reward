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
      <main class="flex-1 px-6 pt-14 pb-8">
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

        <!-- Input & Submit Section -->
        <form
            class="mt-8 space-y-4"
            method="POST"
            action="{{ route('transaction.save.points') }}"
        >
            @csrf

            <div>
                <label
                    for="purchase-amount"
                    class="block text-sm font-semibold text-[#222624] mb-2"
                >
                    Total Purchase Amount
                </label>

                <div class="relative flex items-center">
                    <span class="absolute left-4 text-lg font-medium text-[#59605B]">
                        ₱
                    </span>

                    <input
                        type="number"
                        id="purchase-amount"
                        name="purchase_amount"
                        value="{{ old('purchase_amount') }}"
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                        required
                        class="w-full rounded-2xl border border-[#EBEEE8] bg-[#FAF9F6]
                              py-3.5 pl-9 pr-4 text-lg font-medium text-[#222624]
                              outline-none transition focus:border-[#183D2A]
                              focus:bg-white focus:ring-2 focus:ring-[#183D2A]/20"
                    />
                </div>

                @error('purchase_amount')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full rounded-2xl bg-[#183D2A] py-3.5 text-base
                      font-semibold text-white shadow-sm transition
                      hover:bg-[#122e1f] active:scale-[0.99]"
            >
                Submit Purchase
            </button>
        </form>
      </main>

      <!-- Bottom navigation -->
      <nav class="grid grid-cols-3 border-t border-[#EBEEE8] bg-white px-4 pb-5 pt-3">
        <a href="/home" aria-current="page" class="flex flex-col items-center gap-1 text-[#183D2A]">
          <svg class="size-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 3 2.5 11h2.2v9h5.5v-6h3.6v6h5.5v-9h2.2L12 3Z" />
          </svg>
          <span class="text-sm font-medium">Home</span>
          <span class="h-0.5 w-12 rounded-full bg-[#183D2A]"></span>
        </a>

        <a href="/rewards" class="flex flex-col items-center gap-1 text-[#626967]">
          <svg class="size-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="3" y="9" width="18" height="12" rx="1" />
            <path d="M12 9v12M3 13h18M12 9C6 9 6 3 9 3c2 0 3 3 3 6Zm0 0c6 0 6-6 3-6-2 0-3 3-3 6Z" />
          </svg>
          <span class="text-sm">Rewards</span>
        </a>

        <a href="/activity" class="flex flex-col items-center gap-1 text-[#626967]">
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