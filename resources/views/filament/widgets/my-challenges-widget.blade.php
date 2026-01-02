<div class="my-challenges-widget w-full bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mt-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
            🎯 Langkah Harianku
        </h2>

        @if(count($challenges ?? []) > 3)
        <div class="flex gap-2">
            <button onclick="slideCards(-1)"
                class="p-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition dark:text-white">
                ‹
            </button>
            <button onclick="slideCards(1)"
                class="p-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition dark:text-white">
                ›
            </button>
        </div>
        @endif
    </div>

    <!-- Slider Wrapper -->
    <div class="mt-6 slider-container">
        <div id="cards-track" class="slider-track">
            @forelse($challenges ?? [] as $challenge)
                <!-- CARD -->
                @php
                    $slug = $challenge->slug ?? \Illuminate\Support\Str::slug($challenge->title);
                @endphp
                <div class="slider-card rounded-lg overflow-hidden shadow-lg bg-white dark:bg-gray-700 cursor-pointer hover:scale-105 transition-transform duration-200"
                     onclick="window.location.href='{{ route('challenges.show', $slug) }}'">
                    @if($challenge->cover_image)
                        <img class="w-full h-48 object-cover" src="{{ asset('storage/' . $challenge->cover_image) }}" alt="{{ $challenge->title }}">
                    @else
                        <img class="w-full h-48 object-cover" src="https://picsum.photos/seed/{{ $challenge->id }}/400/300" alt="{{ $challenge->title }}">
                    @endif

                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2 text-gray-900 dark:text-white hover:text-amber-600 dark:hover:text-amber-500 transition">
                            {{ $challenge->title }}
                        </div>
                        @if($challenge->description)
                        <p class="text-white-600 dark:text-gray-300 text-sm line-clamp-2">
                            {!! \Illuminate\Support\Str::limit($challenge->description, 100) !!}
                        </p>
                        @endif

                        @if($challenge->start_date && $challenge->end_date)
                        <div class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                            <span>{{ $challenge->start_date->format('M d') }}</span>
                            <span> → </span>
                            <span>{{ $challenge->end_date->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 text-gray-500 dark:text-gray-400">
                    <p>No challenges found. Start your first challenge today!</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('styles')
    <style>
        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        .slider-track {
            display: flex;
            gap: 1.5rem;
            transition: transform 0.3s ease-in-out;
        }
        .slider-card {
            width: 320px;
            flex-shrink: 0;
        }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Mobile: full width card */
        @media (max-width: 767px) {
            .slider-container {
                max-width: 100%;
            }
            .slider-card {
                width: 100%;
            }
            .slider-track {
                gap: 1rem;
            }
        }

        /* Tablet: show 2 cards at a time */
        @media (min-width: 768px) and (max-width: 1023px) {
            .slider-container {
                max-width: calc(320px * 2 + 1.5rem);
            }
        }

        /* Desktop: show 3 cards at a time */
        @media (min-width: 1024px) {
            .slider-card {
                width: 380px;
            }
            .slider-container {
                max-width: calc(380px * 3 + 1.5rem * 2);
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        let currentIndex = 0;
        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;
        let animationID = 0;

        function getCardsPerView() {
            const width = window.innerWidth;
            if (width < 768) return 1; // Mobile
            if (width < 1024) return 2; // Tablet
            return 3; // Desktop
        }

        function slideCards(direction) {
            const track = document.getElementById('cards-track');
            if (!track) return;

            const cards = track.querySelectorAll('.slider-card');
            const totalCards = cards.length;
            const cardsPerView = getCardsPerView();

            if (totalCards <= cardsPerView) return;

            const maxIndex = Math.max(0, totalCards - cardsPerView);

            currentIndex += direction;

            if (currentIndex < 0) {
                currentIndex = 0;
            } else if (currentIndex > maxIndex) {
                currentIndex = maxIndex;
            }

            const cardWidth = cards[0]?.offsetWidth || 380;
            const gap = 24;
            const slideAmount = currentIndex * (cardWidth + gap);

            setPosition(slideAmount);
        }

        function setPosition(position) {
            const track = document.getElementById('cards-track');
            if (track) {
                track.style.transform = `translateX(-${position}px)`;
            }
        }

        // Touch and mouse events for drag functionality
        document.addEventListener('DOMContentLoaded', function() {
            const track = document.getElementById('cards-track');
            if (!track) return;

            const container = track.closest('.slider-container');
            const cards = track.querySelectorAll('.slider-card');
            const cardsPerView = getCardsPerView();

            if (cards.length > cardsPerView) {
                // Touch events (mobile only)
                track.addEventListener('touchstart', touchStart);
                track.addEventListener('touchend', touchEnd);
                track.addEventListener('touchmove', touchMove);

                // Only enable mouse drag on mobile/tablet
                if (window.innerWidth < 1024) {
                    track.addEventListener('mousedown', touchStart);
                    track.addEventListener('mouseup', touchEnd);
                    track.addEventListener('mouseleave', touchEnd);
                    track.addEventListener('mousemove', touchMove);
                    track.addEventListener('contextmenu', (e) => e.preventDefault());

                    // Add cursor styles for mobile/tablet
                    track.style.cursor = 'grab';
                    track.addEventListener('mousedown', () => track.style.cursor = 'grabbing');
                    track.addEventListener('mouseup', () => track.style.cursor = 'grab');
                    track.addEventListener('mouseleave', () => track.style.cursor = 'grab');
                }
            }

            // Reset position on window resize
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    currentIndex = 0;
                    prevTranslate = 0;
                    currentTranslate = 0;
                    setPosition(0);

                    // Update drag events based on new screen size
                    if (window.innerWidth >= 1024) {
                        // Remove mouse events on desktop
                        track.removeEventListener('mousedown', touchStart);
                        track.removeEventListener('mouseup', touchEnd);
                        track.removeEventListener('mouseleave', touchEnd);
                        track.removeEventListener('mousemove', touchMove);
                        track.style.cursor = 'default';
                    } else if (cards.length > getCardsPerView()) {
                        // Add mouse events on mobile/tablet
                        track.addEventListener('mousedown', touchStart);
                        track.addEventListener('mouseup', touchEnd);
                        track.addEventListener('mouseleave', touchEnd);
                        track.addEventListener('mousemove', touchMove);
                        track.style.cursor = 'grab';
                    }
                }, 250);
            });
        });

        function touchStart(event) {
            isDragging = true;
            startPos = getPositionX(event);
            animationID = requestAnimationFrame(animation);
            const track = document.getElementById('cards-track');
            if (track) {
                track.style.transition = 'none';
                track.style.cursor = window.innerWidth < 1024 ? 'grabbing' : 'default';
            }
        }

        function touchMove(event) {
            if (isDragging) {
                const currentPosition = getPositionX(event);
                const diff = currentPosition - startPos;
                currentTranslate = prevTranslate + diff;

                // Add boundary resistance
                const cards = document.querySelectorAll('.slider-card');
                if (cards.length > 0) {
                    const cardWidth = cards[0]?.offsetWidth || 380;
                    const trackStyle = window.getComputedStyle(document.getElementById('cards-track'));
                    const gap = parseFloat(trackStyle.gap) || 24;
                    const cardsPerView = getCardsPerView();
                    const totalCards = cards.length;
                    const maxTranslate = -(totalCards - cardsPerView) * (cardWidth + gap);

                    // Apply resistance at boundaries
                    if (currentTranslate > 0) {
                        currentTranslate = currentTranslate * 0.3; // Resistance at start
                    } else if (currentTranslate < maxTranslate) {
                        currentTranslate = maxTranslate + (currentTranslate - maxTranslate) * 0.3; // Resistance at end
                    }
                }
            }
        }

        function touchEnd() {
            isDragging = false;
            cancelAnimationFrame(animationID);

            const track = document.getElementById('cards-track');
            if (!track) return;

            track.style.transition = 'transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
            track.style.cursor = window.innerWidth < 1024 ? 'grab' : 'default';

            const movedBy = currentTranslate - prevTranslate;
            const cards = track.querySelectorAll('.slider-card');
            const totalCards = cards.length;
            const cardsPerView = getCardsPerView();
            const cardWidth = cards[0]?.offsetWidth || 380;
            const trackStyle = window.getComputedStyle(track);
            const gap = parseFloat(trackStyle.gap) || 24;
            const maxTranslate = -(totalCards - cardsPerView) * (cardWidth + gap);

            // Threshold to trigger slide (lower threshold for better mobile experience)
            const threshold = cardWidth * 0.2; // 20% of card width

            if (movedBy < -threshold && currentIndex < totalCards - cardsPerView) {
                slideCards(1);
            } else if (movedBy > threshold && currentIndex > 0) {
                slideCards(-1);
            } else {
                // Return to current position
                const slideAmount = currentIndex * (cardWidth + gap);
                setPosition(slideAmount);
            }

            // Update prevTranslate to match current position
            prevTranslate = -currentIndex * (cardWidth + gap);
            currentTranslate = prevTranslate;
        }

        function getPositionX(event) {
            return event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
        }

        function animation() {
            if (isDragging) {
                const track = document.getElementById('cards-track');
                if (track) {
                    track.style.transform = `translateX(${currentTranslate}px)`;
                }
                requestAnimationFrame(animation);
            }
        }
    </script>
    @endpush
</div>
