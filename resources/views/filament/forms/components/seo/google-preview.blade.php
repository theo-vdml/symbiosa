    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm text-left">
        <div class="flex items-center gap-2 mb-1">
            <!-- Google Favicon Service -->
            <div class="w-7 h-7 flex items-center justify-center overflow-hidden rounded-full bg-gray-100">
                <img src="https://www.google.com/s2/favicons?sz=64&domain=google.com" alt="Favicon" class="size-full"
                    onerror="this.src='https://ui-avatars.com/api/?name=S&color=7F9CF5&background=EBF4FF'">
            </div>

            <div class="flex flex-col">
                <div class="text-[14px] text-[#202124] leading-tight font-sans">Symbiosa</div>
                <div class="text-[12px] text-[#5f6368] flex items-center gap-1 font-sans">
                    https://symbiosa.be <span class="text-[8px]">▼</span>
                </div>
            </div>
        </div>
        <div class="text-[#1a0dab] text-[20px] font-medium hover:underline cursor-pointer mb-1 font-sans">
            {{ $title }}
        </div>
        <div class="text-[#4d5156] text-[14px] leading-snug line-clamp-2 font-sans">
            {{ $description }}
        </div>
    </div>
