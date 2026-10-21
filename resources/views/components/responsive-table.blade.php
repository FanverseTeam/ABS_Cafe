@props([
    'paginate' => null,
    'wrapperClass' => '',
])

<div {{ $attributes->merge(['class' => 'bg-transparent md:bg-cafe-card rounded-2xl md:shadow-soft md:border md:border-cafe-beige/40 overflow-hidden ' . $wrapperClass]) }} data-responsive-table>
    <div class="overflow-x-hidden md:overflow-x-auto">
        <table class="w-full text-left text-xs block md:table border-collapse">
            @if(isset($headers))
                <thead class="hidden md:table-header-group bg-[#F2F5E2]/70 border-b border-cafe-beige text-cafe-muted uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        {{ $headers }}
                    </tr>
                </thead>
            @endif
            <tbody class="block md:table-row-group space-y-3.5 md:space-y-0 divide-y-0 md:divide-y divide-cafe-beige/30">
                {{ $slot }}
            </tbody>
        </table>
    </div>

    @if($paginate && method_exists($paginate, 'hasPages') && $paginate->hasPages())
        <div class="p-4 sm:p-5 border-t border-cafe-beige/50 bg-cafe-card rounded-2xl md:rounded-none mt-3.5 md:mt-0 shadow-soft md:shadow-none border border-cafe-beige/60 md:border-0">
            {{ $paginate->links() }}
        </div>
    @elseif($paginate && method_exists($paginate, 'links'))
        <div class="p-4 sm:p-5 border-t border-cafe-beige/50 bg-cafe-card rounded-2xl md:rounded-none mt-3.5 md:mt-0 shadow-soft md:shadow-none border border-cafe-beige/60 md:border-0">
            {{ $paginate->links() }}
        </div>
    @endif
</div>
