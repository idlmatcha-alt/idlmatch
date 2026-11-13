@if(session('match_animation'))
<div id="match-celebration" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/80 backdrop-blur-sm animate-fade-in" x-data="{ open: true }" x-show="open">
    <div class="relative max-w-lg w-full px-4 text-center">
        <!-- Confetti Canvas -->
        <canvas id="match-confetti" class="absolute inset-0 pointer-events-none w-full h-full"></canvas>

        <div class="relative bg-white rounded-3xl p-8 shadow-2xl overflow-hidden border-4 border-pink-500 transform transition-all animate-bounce-subtle">
            <h2 class="text-4xl font-display font-bold text-pink-600 mb-2">It's a Match!</h2>
            <p class="text-slate-600 mb-8 font-medium">You and {{ session('match_animation')['match_name'] }} have liked each other!</p>

            <div class="flex items-center justify-center gap-4 mb-8">
                <!-- User Photo -->
                <div class="relative">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg overflow-hidden ring-4 ring-pink-100 transform -rotate-12 hover:rotate-0 transition-transform duration-500">
                        <img src="{{ session('match_animation')['user_photo'] }}" alt="You" class="w-full h-full object-cover">
                    </div>
                </div>

                <!-- Heart Icon -->
                <div class="z-10 bg-pink-500 rounded-full p-3 shadow-lg animate-pulse">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.657 0L10 6.343l1.172-1.171a4 4 0 115.657 5.657L10 17.657l-6.828-6.829a4 4 0 010-5.657z" clip-rule="evenodd" />
                    </svg>
                </div>

                <!-- Match Photo -->
                <div class="relative">
                    <div class="w-24 h-24 md:w-32 md:h-32 rounded-full border-4 border-white shadow-lg overflow-hidden ring-4 ring-pink-100 transform rotate-12 hover:rotate-0 transition-transform duration-500">
                        <img src="{{ session('match_animation')['match_photo'] }}" alt="{{ session('match_animation')['match_name'] }}" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <a href="{{ route('threads.index') }}" class="block w-full py-4 rounded-full font-bold text-white shadow-lg transform hover:scale-105 transition-all" style="background: linear-gradient(135deg, #FF4081, #C2185B);">
                    Send a Message
                </a>
                <button @click="open = false" class="block w-full py-3 rounded-full font-bold text-slate-500 hover:text-slate-700 transition-colors">
                    Maybe Later
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const duration = 5 * 1000;
        const animationEnd = Date.now() + duration;
        const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 0 };

        function randomInRange(min, max) {
            return Math.random() * (max - min) + min;
        }

        const interval = setInterval(function() {
            const timeLeft = animationEnd - Date.now();

            if (timeLeft <= 0) {
                return clearInterval(interval);
            }

            const particleCount = 50 * (timeLeft / duration);
            // since particles fall down, start a bit higher than random
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
            confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
        }, 250);

        // Sound effect if needed (optional)
        // new Audio('/sounds/match.mp3').play().catch(() => {});
    });
</script>

<style>
    @keyframes bounce-subtle {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .animate-bounce-subtle {
        animation: bounce-subtle 3s ease-in-out infinite;
    }
    .animate-fade-in {
        animation: fadeIn 0.5s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endif
