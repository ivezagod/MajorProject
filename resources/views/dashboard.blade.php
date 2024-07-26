


<x-app-layout>





    <div class="py-12 ">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-primary-light p-[10px] ">
                <h2 class="font-semibold text-xl text-primary-dark leading-tight ">
                    Learning Assistant
                </h2>
            </div>
            <div class="overflow-hidden sm:rounded-lg">
                <div class="max-w-7xl mx-auto">
                    <div class="bg-white rounded-lg overflow-hidden shadow-lg">
                        <div class="grid grid-cols-2 gap-4 px-6 py-4 bg-primary-light">
                            <div id="concepts-panel" class="p-4 bg-highlight-light shadow-md rounded-lg hidden fade-in">
                                <div class="font-bold mb-2 text-white cursor-pointer" onclick="togglePanel('concepts-panel')">Key Concepts</div>
                                <div id="concepts" class="text-white"></div>
                            </div>
                            <div id="real-world-panel" class="p-4 bg-highlight-light shadow-md rounded-lg hidden fade-in">
                                <div class="font-bold mb-2 text-white cursor-pointer" onclick="togglePanel('real-world-panel')">Real-World Applications</div>
                                <div id="real-world" class="text-white"></div>
                            </div>
                            <div id="misconceptions-panel" class="p-4 bg-highlight-light shadow-md rounded-lg hidden fade-in">
                                <div class="font-bold mb-2 text-white cursor-pointer" onclick="togglePanel('misconceptions-panel')">Common Misconceptions</div>
                                <div id="misconceptions" class="text-white"></div>
                            </div>
                            <div id="resources-panel" class="p-4 bg-highlight-light shadow-md rounded-lg hidden fade-in">
                                <div class="font-bold mb-2 text-white cursor-pointer" onclick="togglePanel('resources-panel')">Resources</div>
                                <div id="resources" class="text-white"></div>
                            </div>
                            <div id="questions-panel" class="p-4 bg-highlight-light shadow-md rounded-lg hidden fade-in">
                                <div class="font-bold mb-2 text-white cursor-pointer" onclick="togglePanel('questions-panel')">Critical Questions</div>
                                <div id="questions" class="text-white"></div>
                            </div>
                            <div id="additional-insights-panel" class="p-4 bg-highlight-light shadow-md rounded-lg hidden fade-in">
                                <div class="font-bold mb-2 text-white cursor-pointer" onclick="togglePanel('additional-insights-panel')">Additional Insights</div>
                                <div id="additional-insights" class="text-white"></div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-primary-dark">
                            <form id="chat-form" class="flex" action="{{ route('chat') }}" method="post">
                                <input id="chat-input" name="prompt" type="text" class="flex-grow border rounded-l-lg px-4 py-6 focus:outline-none" placeholder="Type your message...">
                                <div class="flex items-center ml-4">
                                    <input type="checkbox" id="prewrittenPrompt" name="prewrittenPrompt" value="yes">
                                    <label for="prewrittenPrompt" class="ml-2 text-white">Send prewritten prompt</label>
                                </div>
                                <button type="submit" class="bg-primary-dark text-white px-4 py-2 rounded-r-lg hover:bg-highlight-light ml-4">Send</button>
                            </form>
                        </div>
                    </div>
                </div>
                <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const chatForm = document.getElementById('chat-form');
                        const chatInput = document.getElementById('chat-input');
                        const prewrittenPromptCheckbox = document.getElementById('prewrittenPrompt');

                        const conversationId = Date.now().toString();
                        localStorage.setItem('conversationId', conversationId);

                        chatForm.addEventListener('submit', (e) => {
                            e.preventDefault();
                            const message = chatInput.value.trim();
                            const prewrittenPrompt = prewrittenPromptCheckbox.checked;

                            if (message !== '') {
                                axios.post('{{ route("chat") }}', {
                                    prompt: message,
                                    conversation_id: conversationId,
                                    role: 'system',
                                    prewritten_prompt: prewrittenPrompt,
                                }, {
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                })
                                    .then(response => {
                                        if (response.data.response) {
                                            handleResponse(response.data.response);
                                        } else if (response.data.error) {
                                            handleError(response.data.error);
                                            console.error('Error:', response.data.error);
                                        }
                                    })
                                    .catch(error => {
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
                            try {
                                const responseObject = JSON.parse(response);
                                updateSection('concepts-panel', 'concepts', responseObject.concepts);
                                updateSection('real-world-panel', 'real-world', responseObject.realWorld);
                                updateSection('misconceptions-panel', 'misconceptions', responseObject.misconceptions);
                                updateSection('resources-panel', 'resources', responseObject.resources);
                                updateSection('questions-panel', 'questions', responseObject.questions);
                                updateSection('additional-insights-panel', 'additional-insights', responseObject.additionalInsights);
                            } catch (e) {
                                handlePlainTextResponse(response);
                                console.error('JSON Parse Error:', e);
                            }
                        }

                        function handlePlainTextResponse(response) {
                            const sections = [
                                'concepts',
                                'real-world',
                                'misconceptions',
                                'resources',
                                'questions',
                                'additional-insights'
                            ];

                            const contentArray = response.split('\n\n');
                            contentArray.forEach((content, index) => {
                                if (sections[index]) {
                                    updateSection(`${sections[index]}-panel`, sections[index], content);
                                }
                            });
                        }

                        function updateSection(containerId, sectionId, content) {
                            const container = document.getElementById(containerId);
                            const section = document.getElementById(sectionId);
                            container.classList.remove('hidden');
                            showContentWithAnimation(containerId);

                            if (sectionId === 'questions') {
                                const questions = content.split('?').filter(q => q.trim() !== '');
                                section.innerHTML = questions.map(q => `<p>${q.trim()}?</p>`).join('<br>');
                            } else {
                                section.innerHTML = formatContent(content);
                            }
                        }

                        function handleError(error) {
                            const errorSection = document.getElementById('additional-insights-panel');
                            errorSection.classList.remove('hidden');
                            errorSection.querySelector('#additional-insights').innerHTML = `<div class="text-red-600">${error}</div>`;
                        }

                        function formatContent(content) {
                            return content
                                .replace(/^### (.+)$/gm, '<h3 class="text-lg font-semibold mb-2">$1</h3>')
                                .replace(/^## (.+)$/gm, '<h2 class="text-xl font-semibold mb-2">$1</h2>')
                                .replace(/^# (.+)$/gm, '<h1 class="text-2xl font-bold mb-4">$1</h1>')
                                .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
                                .replace(/\*(.+?)\*/g, '<em>$1</em>')
                                .replace(/`(.+?)`/g, '<code class="bg-palette-lighter p-1 rounded">$1</code>')
                                .replace(/\n/g, '<br>');
                        }

                        function showContentWithAnimation(sectionId) {
                            const section = document.getElementById(sectionId);
                            section.classList.add('fade-in');
                            setTimeout(() => section.classList.add('show'), 100);
                        }
                    });
                </script>
                <style>
                    .fade-in {
                        opacity: 0;
                        transition: opacity 0.5s ease-in;
                    }

                    .fade-in.show {
                        opacity: 1;
                    }

                    .card:hover {
                        transform: scale(1.05);
                        transition: transform 0.3s ease;
                    }

                    .p-4 {
                        position: relative;
                    }

                    .p-4:before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(0, 0, 0, 0.05);
                        transition: opacity 0.3s ease;
                        opacity: 0;
                    }

                    .p-4:hover:before {
                        opacity: 1;
                    }

                    /* Animation */
                    .fade-in {
                        opacity: 0;
                        transition: opacity 0.5s ease-in;
                    }

                    .fade-in.show {
                        opacity: 1;
                    }
                </style>
            </div>
        </div>
    </div>
</x-app-layout>
