<div class="bg-white border border-gray-100 rounded-xl p-4 shadow-sm font-sans text-left">
    <div class=" max-w-125 mx-auto">
        <div class="flex items-start gap-3 mb-3">
            <img src="https://api.dicebear.com/9.x/big-smile/svg?backgroundType=gradientLinear&backgroundColor=d1d4f9,b6e3f4,c0aede,ffd5dc,ffdfbf,transparent&seed=Eden"
                alt="Avatar"
                class="w-12 h-12 rounded-full bg-[#06402B] overflow-hidden shrink-0 flex items-center justify-center">
            </img>
            <div class="flex flex-col">
                <div class="flex items-center gap-1">
                    <span class="font-bold text-black text-[15px]">Bruno</span>
                    <span class="text-gray-500 text-[15px]">@_bruno</span>
                </div>
                <div class="text-black text-[15px] mt-0.5 leading-tight">Quelque chose a vous partager ...</div>
            </div>
        </div>

        <div
            class="border border-gray-200 rounded-2xl overflow-hidden mb-3 hover:bg-gray-50 cursor-pointer transition-colors">
            @if ($card === 'summary_large_image')
                <div class="aspect-[1.91/1] w-full bg-gray-100 relative">
                    <img src="{{ $image }}" class="w-full h-full object-cover" />
                </div>
                <div class="p-3 border-t border-gray-100">
                    <div class="text-gray-500 text-[13px] mb-0.5">symbiosa.be</div>
                    <div class="text-black font-bold text-[14px] truncate">{{ $title }}</div>
                    <div class="text-gray-600 text-[14px] line-clamp-2 mt-0.5 leading-tight">{{ $description }}</div>
                </div>
            @else
                <div class="flex h-30">
                    <div class="w-30 h-30 shrink-0 bg-gray-100 border-r border-gray-100">
                        <img src="{{ $image }}" class="w-full h-full object-cover" />
                    </div>
                    <div class="p-3 flex flex-col justify-center min-w-0 w-full">
                        <div class="text-gray-500 text-[13px] mb-0.5">symbiosa.be</div>
                        <div class="text-black font-bold text-[14px] truncate">{{ $title }}</div>
                        <div class="text-gray-600 text-[14px] line-clamp-2 mt-0.5 leading-tight">{{ $description }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="text-gray-500 text-[14px] border-b border-gray-100 pb-3 mb-3">
            {{ now()->format('H:i · j M Y') }} · <span class="text-[#1d9bf0] hover:underline">Twitter for Web</span>
        </div>
        <div class="flex items-center gap-5 text-gray-500 text-[14px]">
            <span><span class="text-black font-bold">10</span> Retweets</span>
            <span><span class="text-black font-bold">125</span> Likes</span>
        </div>
    </div>
</div>
