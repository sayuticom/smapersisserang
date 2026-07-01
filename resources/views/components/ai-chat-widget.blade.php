<div x-data="{
    open: false,
    input: '',
    messages: [
        {
            role: 'assistant',
            content: 'Halo! Saya Asisten SPMB SMA Persis Serang. Ada yang ingin ditanyakan seputar pendaftaran, biaya, asrama, atau program sekolah?'
        }
    ],
    loading: false,

    init() {
        if (localStorage.getItem('ai-chat-messages')) {
            try {
                const saved = JSON.parse(localStorage.getItem('ai-chat-messages'));
                if (Array.isArray(saved) && saved.length > 0) {
                    this.messages = saved;
                }
            } catch (e) {}
        }
    },

    toggle() {
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => {
                this.$refs.inputField?.focus();
                this.scrollToBottom();
            });
        }
    },

    async sendMessage() {
        const msg = this.input.trim();
        if (!msg || this.loading) return;

        this.messages.push({ role: 'user', content: msg });
        this.input = '';
        this.loading = true;
        this.scrollToBottom();
        this.saveMessages();

        try {
            const response = await fetch('{{ route("ai-chat.send") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=\'csrf-token\']')?.content || '',
                },
                body: JSON.stringify({ message: msg }),
            });

            const data = await response.json();

            this.messages.push({
                role: 'assistant',
                content: data.reply || 'Maaf, saya tidak bisa menjawab saat ini. Silakan hubungi panitia SPMB.'
            });
        } catch (e) {
            this.messages.push({
                role: 'assistant',
                content: 'Maaf, terjadi kesalahan koneksi. Silakan coba lagi nanti.'
            });
        }

        this.loading = false;
        this.scrollToBottom();
        this.saveMessages();
    },

    scrollToBottom() {
        this.$nextTick(() => {
            const container = this.$refs.messagesContainer;
            if (container) container.scrollTop = container.scrollHeight;
        });
    },

    saveMessages() {
        try {
            localStorage.setItem('ai-chat-messages', JSON.stringify(this.messages));
        } catch (e) {}
    }
}"
     class="fixed bottom-6 right-6 z-50 flex flex-col items-end">

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="mb-4 w-[360px] max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-emerald-100 overflow-hidden"
         @click.away="open = false">
        <div class="bg-gradient-to-r from-emerald-700 to-emerald-600 px-4 py-3.5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-white">Asisten SPMB</h3>
                        <p class="text-[11px] text-emerald-100">Tanyakan informasi pendaftaran SMA Persis Serang</p>
                    </div>
                </div>
                <button @click="open = false" class="text-white/80 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <div class="h-80 overflow-y-auto p-4 space-y-3 bg-gray-50" x-ref="messagesContainer">
            <template x-for="(msg, i) in messages" :key="i">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div :class="msg.role === 'user'
                        ? 'bg-emerald-600 text-white rounded-2xl rounded-br-md px-4 py-2.5 max-w-[85%]'
                        : 'bg-white text-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 max-w-[85%] shadow-sm border border-gray-100'">
                        <p class="text-sm leading-relaxed whitespace-pre-wrap" x-text="msg.content"></p>
                    </div>
                </div>
            </template>
            <div x-show="loading" class="flex justify-start">
                <div class="bg-white text-gray-500 rounded-2xl rounded-bl-md px-4 py-3 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 p-3 bg-white">
            <form @submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" x-model="input" x-ref="inputField"
                       placeholder="Tanyakan tentang SPMB..."
                       maxlength="500"
                       class="flex-1 rounded-xl border border-gray-200 bg-gray-50 px-3.5 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500 placeholder:text-gray-400"
                       :disabled="loading">
                <button type="submit" :disabled="loading || !input.trim()"
                        class="rounded-xl bg-emerald-600 px-3.5 py-2.5 text-white hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-7 7m7-7l7 7"/>
                    </svg>
                </button>
            </form>
            <p class="text-[10px] text-gray-400 mt-1.5 text-center">AI bisa salah. Untuk info pasti hubungi panitia SPMB.</p>
        </div>
    </div>

    <button @click="toggle"
            class="flex items-center gap-2 bg-gradient-to-r from-emerald-700 to-emerald-600 text-white px-5 py-3 rounded-full shadow-lg hover:shadow-xl hover:from-emerald-600 hover:to-emerald-500 transition-all duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
        </svg>
        <span class="text-sm font-semibold">Konsultasi SPMB</span>
    </button>
</div>
