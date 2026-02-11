{{-- ===================== CONTACT SECTION ===================== --}}
<section class="py-24 bg-soft-mint/20 dark:bg-gray-900/50" id="contact">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16">

            {{-- Left Side - Contact Information --}}
            <div>
                {{-- Heading --}}
                <h2 class="font-display text-4xl font-bold mb-6">Let's Talk</h2>
                <p class="text-gray-600 dark:text-gray-400 text-lg mb-8">
                    Have questions or suggestions? We'd love to hear from you. Our community is built on your feedback.
                </p>

                {{-- Contact Details --}}
                <div class="space-y-6">
                    
                    {{-- Email Contact --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-primary shadow-sm">
                            <span class="material-symbols-outlined">mail</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email us at</p>
                            <p class="font-bold">hello@cyclepredictor.com</p>
                        </div>
                    </div>

                    {{-- WhatsApp Contact --}}
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-xl flex items-center justify-center text-primary shadow-sm">
                            <span class="material-symbols-outlined">chat</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">WhatsApp support</p>
                            <p class="font-bold">+62 82 2804 2840</p>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Right Side - Contact Form --}}
            <div class="bg-white dark:bg-gray-800 p-8 lg:p-10 rounded-[2.5rem] shadow-xl border border-baby-pink/10">
                
                <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Name & Email Row --}}
                    <div class="grid sm:grid-cols-2 gap-6">
                        {{-- Name Input --}}
                        <div>
                            <label class="block text-sm font-bold mb-2">Name</label>
                            <input 
                                type="text" 
                                name="name"
                                class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary focus:bg-white transition-all" 
                                placeholder="Your name"
                                required />
                        </div>

                        {{-- Email Input --}}
                        <div>
                            <label class="block text-sm font-bold mb-2">Email</label>
                            <input 
                                type="email" 
                                name="email"
                                class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary focus:bg-white transition-all" 
                                placeholder="your@email.com"
                                required />
                        </div>
                    </div>

                    {{-- Message Textarea --}}
                    <div>
                        <label class="block text-sm font-bold mb-2">Message</label>
                        <textarea 
                            name="message"
                            class="w-full bg-gray-50 dark:bg-gray-700 border-none rounded-2xl p-4 focus:ring-2 focus:ring-primary focus:bg-white transition-all" 
                            placeholder="How can we help?" 
                            rows="4"
                            required></textarea>
                    </div>

                    {{-- Submit Button --}}
                    <button 
                        type="submit"
                        class="w-full bg-primary text-white font-bold py-4 rounded-2xl hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                        Send Message
                    </button>

                </form>
            </div>

        </div>
    </div>
</section>