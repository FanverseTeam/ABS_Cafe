@props(['role'])

@php
    $navigation = \App\Support\MobileNavigation::build($role);
    $items = $navigation['items'];
    $more = collect($items)->firstWhere('type', 'more');
@endphp

@if($items !== [] && ! $navigation['hidden'])
    <nav id="mobile-bottom-nav"
         aria-label="Navigasi utama {{ ucfirst($role) }}"
         class="lg:hidden fixed inset-x-0 bottom-0 z-40 bg-[#FAFCF3]/95 backdrop-blur-md border-t border-[#E3DEA4] shadow-[0_-4px_18px_rgba(41,0,36,0.08)] transition-[transform,opacity] duration-200 ease-out motion-reduce:transition-none">
        <div class="mx-auto grid min-h-16 max-w-lg sm:min-h-[4.25rem] sm:max-w-2xl grid-cols-5 items-stretch gap-0.5 sm:gap-1 px-1 sm:px-3 pt-1 pb-[max(0.35rem,env(safe-area-inset-bottom))]">
            @foreach($items as $item)
                @php
                    $isActive = (bool) $item['active'];
                    $badge = $item['badge'] ?? null;
                @endphp

                @if($item['type'] === 'more')
                    <button type="button"
                            id="mobile-more-button"
                            data-mobile-nav-item
                            onclick="toggleMobileMore()"
                            aria-controls="mobile-more-sheet"
                            aria-expanded="false"
                            @if($isActive) aria-current="page" @endif
                            @if($badge) aria-label="{{ $item['label'] }}, {{ $badge }} menunggu tindakan" @endif
                            class="group relative flex min-h-[48px] sm:min-h-[52px] min-w-0 flex-col items-center justify-center gap-0.5 sm:gap-1 rounded-2xl px-0.5 py-1 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 motion-reduce:transition-none">
                        <span class="relative flex h-7 w-10 sm:h-8 sm:w-12 items-center justify-center rounded-full transition-colors duration-200 motion-reduce:transition-none {{ $isActive ? 'bg-[#D4954D] text-[#290024] shadow-sm' : 'text-[#775533] group-hover:bg-[#E3DEA4]/55' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4 sm:h-5 sm:w-5"></i>
                            @if($badge)
                                <span data-nav-badge class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#B03A2E] px-1 text-[9px] font-bold leading-none text-white shadow-sm">{{ $badge > 99 ? '99+' : $badge }}</span>
                            @endif
                        </span>
                        <span data-nav-label class="max-w-full truncate text-[10px] sm:text-[11px] leading-none {{ $isActive ? 'font-semibold text-[#290024]' : 'font-medium text-[#775533]' }}">
                            {{ $item['label'] }}
                        </span>
                    </button>
                @else
                    <a href="{{ $item['url'] }}"
                       data-mobile-nav-item
                       @if($isActive) aria-current="page" @endif
                       @if($badge) aria-label="{{ $item['label'] }}, {{ $badge }} baru" @endif
                       class="group relative flex min-h-[48px] sm:min-h-[52px] min-w-0 flex-col items-center justify-center gap-0.5 sm:gap-1 rounded-2xl px-0.5 py-1 transition-colors duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 motion-reduce:transition-none">
                        <span class="relative flex h-7 w-10 sm:h-8 sm:w-12 items-center justify-center rounded-full transition-colors duration-200 motion-reduce:transition-none {{ $isActive ? 'bg-[#D4954D] text-[#290024] shadow-sm' : 'text-[#775533] group-hover:bg-[#E3DEA4]/55' }}">
                            <i data-lucide="{{ $item['icon'] }}" class="h-4 w-4 sm:h-5 sm:w-5"></i>
                            @if($badge)
                                <span data-nav-badge class="absolute -right-1 -top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-[#B03A2E] px-1 text-[9px] font-bold leading-none text-white shadow-sm">{{ $badge > 99 ? '99+' : $badge }}</span>
                            @endif
                        </span>
                        <span data-nav-label class="max-w-full truncate text-[10px] sm:text-[11px] leading-none {{ $isActive ? 'font-semibold text-[#290024]' : 'font-medium text-[#775533]' }}">
                            {{ $item['label'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </nav>

    @if($more)
        <div id="mobile-more-backdrop"
             onclick="closeMobileMore()"
             class="fixed inset-0 z-40 hidden bg-[#290024]/50 backdrop-blur-sm lg:hidden"></div>

        <div id="mobile-more-sheet"
             role="dialog"
             aria-modal="true"
             aria-labelledby="mobile-more-title"
             class="fixed inset-x-0 sm:inset-x-auto sm:left-1/2 sm:-translate-x-1/2 sm:w-full sm:max-w-[560px] bottom-0 z-50 hidden max-h-[85dvh] overflow-y-auto overflow-x-hidden rounded-t-[2rem] border-t border-[#E3DEA4] bg-[#FAFCF3] px-4 pt-3 pb-[max(1rem,env(safe-area-inset-bottom))] shadow-[0_-12px_40px_rgba(41,0,36,0.18)] lg:hidden">
            <div class="mx-auto mb-3 h-1.5 w-12 rounded-full bg-[#775533]/25"></div>

            <div class="mx-auto flex max-w-lg items-center justify-between gap-3 pb-2">
                <h2 id="mobile-more-title" class="font-serif text-base font-bold text-[#290024]">Menu Lainnya</h2>
                <button type="button"
                        data-more-close
                        onclick="closeMobileMore()"
                        class="flex h-10 w-10 items-center justify-center rounded-full text-[#775533] transition-colors hover:bg-[#E3DEA4]/60 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70"
                        aria-label="Tutup menu lainnya">
                    <i data-lucide="x" class="h-5 w-5"></i>
                </button>
            </div>

            <div class="mx-auto max-w-lg space-y-1">
                @foreach($more['children'] as $child)
                    @php
                        $isDanger = $child['type'] === 'logout';
                        $isActiveChild = (bool) $child['active'];
                    @endphp

                    @if($child['type'] === 'logout')
                        <form action="{{ $child['url'] }}" method="POST" data-cafe-confirm>
                            @csrf
                            <button type="submit"
                                    data-mobile-nav-item
                                    class="flex min-h-[48px] w-full items-center gap-3 rounded-2xl px-3 py-2.5 text-left text-xs font-semibold text-[#B03A2E] transition-colors hover:bg-[#B03A2E]/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#B03A2E]/40">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#B03A2E]/10 text-[#B03A2E]">
                                    <i data-lucide="{{ $child['icon'] }}" class="h-4 w-4"></i>
                                </span>
                                <span data-nav-label>{{ $child['label'] }}</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ $child['url'] }}"
                           data-mobile-nav-item
                           @if($isActiveChild) aria-current="page" @endif
                           class="flex min-h-[48px] items-center gap-3 rounded-2xl px-3 py-2.5 text-xs font-semibold transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#D4954D]/70 {{ $isActiveChild ? 'bg-[#E3DEA4]/55 text-[#290024]' : 'text-[#290024] hover:bg-[#E3DEA4]/45' }}">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $isDanger ? 'bg-[#B03A2E]/10 text-[#B03A2E]' : ($isActiveChild ? 'bg-[#D4954D]/35 text-[#290024]' : 'bg-[#E3DEA4]/70 text-[#775533]') }}">
                                <i data-lucide="{{ $child['icon'] }}" class="h-4 w-4"></i>
                            </span>
                            <span data-nav-label class="min-w-0 truncate">{{ $child['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <style>
        @media (prefers-reduced-motion: reduce) {
            #mobile-bottom-nav,
            #mobile-bottom-nav *,
            #mobile-more-sheet,
            #mobile-more-sheet * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
            }
        }

        @media print {
            #mobile-bottom-nav,
            #mobile-more-backdrop,
            #mobile-more-sheet {
                display: none !important;
            }
        }
    </style>

    <script>
        (function () {
            if (window.__absMobileBottomNavReady) {
                return;
            }

            window.__absMobileBottomNavReady = true;

            const nav = document.getElementById('mobile-bottom-nav');
            const backdrop = document.getElementById('mobile-more-backdrop');
            const sheet = document.getElementById('mobile-more-sheet');
            const moreButton = document.getElementById('mobile-more-button');
            const editableSelector = 'input, textarea, select, [contenteditable="true"]';
            let focusTimer = null;
            let sheetOpen = false;

            function isEditable(element) {
                return Boolean(element && element.matches && element.matches(editableSelector));
            }

            function hideForKeyboard() {
                if (!nav) {
                    return;
                }

                nav.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
                closeMobileMore(false);
            }

            function restoreAfterKeyboard() {
                if (!nav) {
                    return;
                }

                nav.classList.remove('translate-y-full', 'opacity-0', 'pointer-events-none');
            }

            function openMobileMore() {
                if (!sheet || !backdrop || !moreButton) {
                    return;
                }

                sheetOpen = true;
                backdrop.classList.remove('hidden');
                sheet.classList.remove('hidden');
                moreButton.setAttribute('aria-expanded', 'true');
                document.body.classList.add('overflow-hidden');

                const closeButton = sheet.querySelector('[data-more-close]');
                if (closeButton) {
                    closeButton.focus({ preventScroll: true });
                }
            }

            function closeMobileMore(returnFocus = true) {
                if (!sheet || !backdrop || !moreButton || !sheetOpen) {
                    return;
                }

                sheetOpen = false;
                backdrop.classList.add('hidden');
                sheet.classList.add('hidden');
                moreButton.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('overflow-hidden');

                if (returnFocus && !isEditable(document.activeElement)) {
                    moreButton.focus({ preventScroll: true });
                }
            }

            window.openMobileMore = openMobileMore;
            window.closeMobileMore = closeMobileMore;
            window.toggleMobileMore = function () {
                if (sheetOpen) {
                    closeMobileMore();
                } else {
                    openMobileMore();
                }
            };

            function toggleAdminMore() {
                window.toggleMobileMore();
            }

            window.toggleAdminMore = toggleAdminMore;

            document.addEventListener('focusin', function (event) {
                if (!isEditable(event.target)) {
                    return;
                }

                clearTimeout(focusTimer);
                hideForKeyboard();
            });

            document.addEventListener('focusout', function () {
                clearTimeout(focusTimer);
                focusTimer = setTimeout(function () {
                    if (!isEditable(document.activeElement)) {
                        restoreAfterKeyboard();
                    }
                }, 120);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeMobileMore();
                }
            });

            window.addEventListener('pageshow', restoreAfterKeyboard);
        })();
    </script>
@endif
