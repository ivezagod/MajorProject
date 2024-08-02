<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Learning Assistant</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.2/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <style>
        .swiper-container {
            width: 100%;
            height: 100%;
            padding-top: 50px;
            padding-bottom: 70px; /* Adjust padding to make space for pagination */
        }

        .swiper-slide {
            background-color: #11a043;
            color: white;
            border-radius: 10px;
            padding: 20px;
            height: auto;
            max-width: 90%; /* Make the cards wider */
            transform: scale(0.8);
            transition: transform 0.3s ease;
            overflow: hidden; /* Ensure content stays within the card */
        }

        .swiper-slide-active {
            transform: scale(1);
        }

        .swiper-pagination {
            margin-top: 450px;
            left: 50%;
            transform: translateX(-50%);
        }

        /* Custom card styles */
        .custom-card {
            background-color: #11a043;
            color: white;
            border-radius: 10px;
            padding: 20px;
            height: auto;
            width: 100%; /* Full width on small screens */
            transform: scale(0.8);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden; /* Ensure content stays within the card */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add shadow to look more like cards */
        }

        /* Styles for larger screens */
        @media (min-width: 640px) {
            .custom-card {
                max-width: 90%; /* Make the cards wider */
                width: auto; /* Override the width to auto for larger screens */
                transform: scale(1); /* Scale back to original size */
            }
        }

        /* Add animation on hover */
        .custom-card:hover {
            transform: scale(1.05); /* Slightly increase the size on hover */
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3); /* Increase shadow on hover */
        }
    </style>
</head>
<body>
<x-app-layout>
    <div class="relative bg-[url('/public/images/bgImage.jpg')] bg-center bg-cover bg-blend-normal h-screen pt-16">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center text-white">
            <div class="max-w-3xl mx-auto p-6">
                <h1 class="text-4xl font-bold mb-4">Transform your learning experience with our AI-powered teaching assistant—personalized, interactive, and tailored just for you!</h1>
                <form id="chat-form" class="flex justify-center mt-8 space-x-4" action="{{ route('chat') }}" method="post">
                    <input id="chat-input" name="prompt" type="text" class="p-4 rounded-lg w-96 text-black" placeholder="Your prompt">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-8 py-4 rounded-lg">Send</button>
                </form>
                <div id="loading-indicator" class="hidden flex items-center justify-center gap-2 text-white mt-4">
                    <img src="{{ asset('images/loading.gif') }}" alt="Loading" class="w-6 h-6">
                    <span>Loading...</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#141411] text-white overflow-hidden">
        <div class="p-10 max-w-7xl mx-auto">
            <div id="chat-prompt" class="text-white text-center text-3xl font-bold mb-4"></div>
            <div id="content-panel" class="swiper-container hidden">
                <div class="swiper-wrapper">
                    <div class="swiper-slide custom-card">
                        <!-- Your card content here -->
                    </div>
                    <!-- Add more swiper slides as needed -->
                </div>
                <div class="swiper-button-next text-white mt-[450px]"></div>
                <div class="swiper-button-prev text-white mt-[450px]"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const loadingIndicator = document.getElementById('loading-indicator');
            const contentPanel = document.getElementById('content-panel');
            const chatPrompt = document.getElementById('chat-prompt');
            const swiperWrapper = document.querySelector('.swiper-wrapper');

            const conversationId = Date.now().toString();
            localStorage.setItem('conversationId', conversationId);

            chatForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const message = chatInput.value.trim();

                if (message !== '') {
                    chatPrompt.textContent = message;
                    chatPrompt.classList.remove('hidden');
                    loadingIndicator.classList.remove('hidden');
                    axios.post('{{ route("chat") }}', {
                        prompt: message,
                        conversation_id: conversationId,
                        role: 'system',
                        prewritten_prompt: true,  // Always use prewritten prompt
                    }, {
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => {
                            loadingIndicator.classList.add('hidden');
                            if (response.data.response) {
                                handleResponse(response.data.response);
                            } else if (response.data.error) {
                                handleError(response.data.error);
                                console.error('Error:', response.data.error);
                            }
                        })
                        .catch(error => {
                            loadingIndicator.classList.add('hidden');
                            if (error.response) {
                                handleError(`Error ${error.response.status}: ${error.response.data.error}`);
                                console.error('Error Response:', error.response.data);
                            } else {
                                handleError('An unexpected error occurred.');
                                console.error('Unexpected Error:', error);
                            }
                        });
                }
            });

            function handleResponse(response) {
                contentPanel.classList.remove('hidden');
                swiperWrapper.innerHTML = ''; // Clear previous content

                const responseDivs = response.match(/<div[\s\S]*?<\/div>/g);
                if (responseDivs) {
                    responseDivs.forEach((div) => {
                        const slide = document.createElement('div');
                        slide.classList.add('swiper-slide', 'custom-card');
                        slide.innerHTML = div;
                        swiperWrapper.appendChild(slide);
                    });
                }

                setTimeout(() => {
                    new Swiper('.swiper-container', {
                        loop: false,
                        spaceBetween: 10,
                        centeredSlides: true,
                        slidesPerView: 'auto',
                        navigation: {
                            nextEl: '.swiper-button-next',
                            prevEl: '.swiper-button-prev',
                        },
                        pagination: {
                            el: '.swiper-pagination',
                            clickable: true,
                        },
                    });
                }, 0);
            }

            function handleError(error) {
                const errorSection = document.getElementById('content-panel');
                errorSection.classList.remove('hidden');
                swiperWrapper.innerHTML = `<div class="swiper-slide p-6 bg-gray-800 rounded-lg shadow-md text-red-600">${error}</div>`;
            }
        });
    </script>
</x-app-layout>
</body>
</html>
