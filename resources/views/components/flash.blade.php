<div x-data="{
    messages: [],
    add(type, text) {
        if (!text) return;
        const id = Date.now() + Math.random();
        this.messages.push({ id, type, text });
        setTimeout(() => this.remove(id), 5000);
    },
    remove(id) { this.messages = this.messages.filter(m => m.id !== id); }
}"
x-init="
    @if(session('success')) add('success', @js(session('success'))); @endif
    @if(session('error'))   add('error',   @js(session('error')));   @endif
    @if(session('warning')) add('warning', @js(session('warning'))); @endif
    @if(session('info'))    add('info',    @js(session('info')));    @endif
    /* Flash persistido em sessionStorage — sobrevive a reloads após ações via fetch. */
    try { const f = JSON.parse(sessionStorage.getItem('flash')); if (f) { add(f.type, f.text); sessionStorage.removeItem('flash'); } } catch (e) {}
"
@toast.window="add($event.detail.type, $event.detail.text)"
class="fixed top-4 right-4 z-[60] w-[calc(100%-2rem)] max-w-sm space-y-2 pointer-events-none">
    <template x-for="msg in messages" :key="msg.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             :class="{
                 'bg-green-50 dark:bg-green-900/40 border-green-200 dark:border-green-700 text-green-800 dark:text-green-200': msg.type === 'success',
                 'bg-red-50 dark:bg-red-900/40 border-red-200 dark:border-red-700 text-red-800 dark:text-red-200':     msg.type === 'error',
                 'bg-amber-50 dark:bg-amber-900/40 border-amber-200 dark:border-amber-700 text-amber-800 dark:text-amber-200': msg.type === 'warning',
                 'bg-blue-50 dark:bg-blue-900/40 border-blue-200 dark:border-blue-700 text-blue-800 dark:text-blue-200':  msg.type === 'info',
             }"
             class="pointer-events-auto flex items-start gap-3 px-4 py-3 rounded-lg border shadow-lg text-sm">
            <span class="flex-1" x-text="msg.text"></span>
            <button @click="remove(msg.id)" class="ml-auto opacity-60 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
