@if ($paginador->total() > 0)
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-3">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Mostrando {{ $paginador->firstItem() }}–{{ $paginador->lastItem() }} de {{ $paginador->total() }} resultado(s)
        </p>
        @if ($paginador->hasPages())
        <div class="flex gap-1">
            @if ($paginador->onFirstPage())
                <span class="px-3 py-1.5 rounded-lg text-sm text-slate-400 cursor-not-allowed">Anterior</span>
            @else
                <a href="{{ $paginador->previousPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Anterior</a>
            @endif

            @foreach ($paginador->getUrlRange(max(1, $paginador->currentPage() - 2), min($paginador->lastPage(), $paginador->currentPage() + 2)) as $pagina => $url)
                @if ($pagina == $paginador->currentPage())
                    <span class="px-3 py-1.5 rounded-lg text-sm bg-blue-600 text-white font-bold">{{ $pagina }}</span>
                @else
                    <a href="{{ $url }}" class="px-3 py-1.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">{{ $pagina }}</a>
                @endif
            @endforeach

            @if ($paginador->hasMorePages())
                <a href="{{ $paginador->nextPageUrl() }}" class="px-3 py-1.5 rounded-lg text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">Siguiente</a>
            @else
                <span class="px-3 py-1.5 rounded-lg text-sm text-slate-400 cursor-not-allowed">Siguiente</span>
            @endif
        </div>
        @endif
    </div>
@endif
