<div x-data="{
    messages: [],
    add(type, text) {
        const id = Date.now();
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
"
class="space-y-2 mb-4">
    <template x-for="msg in messages" :key="msg.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             :class="{
                 'bg-green-50 border-green-200 text-green-800': msg.type === 'success',
                 'bg-red-50 border-red-200 text-red-800':     msg.type === 'error',
                 'bg-amber-50 border-amber-200 text-amber-800': msg.type === 'warning',
                 'bg-blue-50 border-blue-200 text-blue-800':  msg.type === 'info',
             }"
             class="flex items-start gap-3 px-4 py-3 rounded-lg border text-sm">
            <span class="flex-1" x-text="msg.text"></span>
            <button @click="remove(msg.id)" class="ml-auto opacity-60 hover:opacity-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
