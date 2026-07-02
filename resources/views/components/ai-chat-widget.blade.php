<button id="ai-chat-toggle"
        type="button"
        onclick="toggleAiChatPanel()"
        style="position: fixed; right: 1rem; bottom: 5rem; z-index: 9999;"
        class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-emerald-700 to-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:from-emerald-600 hover:to-emerald-500 hover:shadow-xl transition-all duration-200">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
    </svg>
    <span>Konsultasi SPMB</span>
</button>

<div id="ai-chat-panel"
     style="display: none; position: fixed; left: 0; right: 0; bottom: 6.5rem; z-index: 9998;"
     class="px-3 sm:px-6">
    <div class="w-full max-w-3xl mx-auto rounded-2xl bg-white shadow-2xl border border-emerald-100 overflow-hidden">
        <div x-data="aiChatWidget()">
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
                    <button type="button" onclick="toggleAiChatPanel()" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="h-80 max-h-[70vh] overflow-y-auto px-4 sm:px-6 py-4 space-y-3 bg-gray-50" x-ref="messagesContainer">
                <template x-for="(msg, i) in messages" :key="i">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role === 'user'
                            ? 'bg-emerald-600 text-white rounded-2xl rounded-br-md px-4 py-2.5 max-w-[85%] sm:max-w-xl lg:max-w-2xl'
                            : 'bg-white text-gray-800 rounded-2xl rounded-bl-md px-4 py-2.5 max-w-[85%] sm:max-w-2xl lg:max-w-3xl shadow-sm border border-gray-100'">
                            <div class="text-sm leading-relaxed text-left [&_ul]:my-1 [&_li]:text-sm [&_a]:break-all" x-html="renderMessage(msg.content)"></div>
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

            <div class="border-t border-gray-100 px-4 sm:px-6 py-3 bg-white">
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
    </div>
</div>

<script>
function aiChatWidget() {
    return {
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

        escapeHtml(text) {
            const div = document.createElement('div');
            div.appendChild(document.createTextNode(text || ''));
            return div.innerHTML;
        },

        renderMessage(text) {
            var html = this.escapeHtml(text);

            html = html.replace(/089661234569/g, '<a href="https://wa.me/6289661234569" target="_blank" rel="noopener noreferrer" class="text-emerald-600 underline font-semibold hover:text-emerald-700">089661234569</a>');

            html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

            var lines = html.split('\n');
            var out = [];
            var inList = false;

            for (var i = 0; i < lines.length; i++) {
                var line = lines[i];
                var bullet = line.match(/^[\u2022\-]\s*(.*)/);

                if (bullet) {
                    if (!inList) {
                        out.push('<ul class="list-disc pl-5 space-y-0.5 my-1">');
                        inList = true;
                    }
                    out.push('<li>' + (bullet[1] || '') + '</li>');
                } else {
                    if (inList) {
                        out.push('</ul>');
                        inList = false;
                    }
                    if (line.trim() === '') {
                        out.push('<br>');
                    } else {
                        out.push('<p class="mb-1 last:mb-0">' + line + '</p>');
                    }
                }
            }

            if (inList) {
                out.push('</ul>');
            }

            return out.join('\n');
        },

        async sendMessage() {
            var msg = this.input.trim();
            if (!msg || this.loading) return;

            this.messages.push({ role: 'user', content: msg });
            this.input = '';
            this.loading = true;
            this.$nextTick(function() { this.scrollToBottom(); }.bind(this));
            this.saveMessages();

            try {
                var response = await fetch('{{ route("ai-chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({ message: msg }),
                });
                var data = await response.json();
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
            this.$nextTick(function() { this.scrollToBottom(); }.bind(this));
            this.saveMessages();
        },

        scrollToBottom() {
            var container = this.$refs.messagesContainer;
            if (container) container.scrollTop = container.scrollHeight;
        },

        saveMessages() {
            try {
                localStorage.setItem('ai-chat-messages', JSON.stringify(this.messages));
            } catch (e) {}
        }
    };
}

function toggleAiChatPanel() {
    var panel = document.getElementById('ai-chat-panel');
    if (!panel) return;
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
        setTimeout(function() {
            var container = panel.querySelector('[x-ref="messagesContainer"]');
            if (container) container.scrollTop = container.scrollHeight;
            var input = panel.querySelector('[x-ref="inputField"]');
            if (input) input.focus();
        }, 100);
    } else {
        panel.style.display = 'none';
    }
}
</script>
