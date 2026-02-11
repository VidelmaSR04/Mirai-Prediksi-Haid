{{-- ===================== FOOTER ===================== --}}
<footer class="bg-baby-pink py-16 text-gray-800 dark:text-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Footer Content Grid --}}
        <div class="grid md:grid-cols-4 gap-12 mb-12">
            
            {{-- Brand & Description --}}
            <div class="col-span-1 md:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <img alt="Logo" class="h-8 w-8 object-contain" src="{{ asset('img/logo fix.png') }}" />
                    <span class="font-display font-bold text-xl tracking-tight text-primary">CyclePredictor</span>
                </div>
                <p class="text-gray-600 max-w-sm">
                    Empowering women through technology and data-driven insights. Our mission is to make health tracking simple, beautiful, and private.
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h5 class="font-bold mb-6">Quick Links</h5>
                <ul class="space-y-4 text-gray-600">
                    <li><a class="hover:text-primary transition-colors" href="{{ url('/') }}">Home</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#about">About Us</a></li>
                    <li><a class="hover:text-primary transition-colors" href="#team">Our Team</a></li>
                    <li><a class="hover:text-primary transition-colors" href="{{ route('privacy') }}">Privacy Policy</a></li>
                </ul>
            </div>

            {{-- Social Media --}}
            <div>
                <h5 class="font-bold mb-6">Follow Us</h5>
                <div class="flex gap-4">
                    <a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary hover:scale-110 transition-all shadow-sm" href="#" aria-label="Social Media">
                        <span class="material-symbols-outlined text-xl">social_leaderboard</span>
                    </a>
                    <a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary hover:scale-110 transition-all shadow-sm" href="#" aria-label="Instagram">
                        <span class="material-symbols-outlined text-xl">camera_alt</span>
                    </a>
                    <a class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-primary hover:scale-110 transition-all shadow-sm" href="#" aria-label="Website">
                        <span class="material-symbols-outlined text-xl">public</span>
                    </a>
                </div>
            </div>

        </div>

        {{-- Copyright --}}
        <div class="pt-8 border-t border-primary/10 text-center text-gray-500 text-sm">
            © {{ date('Y') }} CyclePredictor. All rights reserved. Designed with love.
        </div>
        
    </div>
</footer>