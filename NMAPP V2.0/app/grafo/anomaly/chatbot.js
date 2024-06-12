// Function to load graph data from a JSON file
function loadData(callback) {
    const xhr = new XMLHttpRequest();
    xhr.overrideMimeType("application/json");
    xhr.open('GET', 'graph_data_anomaly.json', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                callback(null, data);
            } else {
                callback('Error loading graph data', null);
            }
        }
    };
    xhr.send(null);
}

// Function to handle user question directly with ChatGPT
async function processQuestion(userInput, data, retries = 3) {
    console.log("Processing user question...");
    console.log("User input:", userInput);

    const API_KEY = 'sk-proj-i4cwgA80ndx3CdbG4rDOT3BlbkFJmwPIf3H9rJnWBqOYxCO2';
    const additionalText = "Tu nombres es BOTMAP. Eres un asistente que te vas a encargar de ayudar a la gente que te utilice a entender un grafo, pero para ello te voy a poner en contexto, el grafo se ha generado en base a un json, este json se ha obtenido de un escaneo de nmap, la idea de este grafo es visualizar de forma fácil y sencilla cómo funcionan las redes y qué vulnerabilidades tienen así como si existe alguna anomalía entre puerto y servicio (el servicio no concuerda con un puerto común para dicho servicio). Debes ayudar a responder todas las preguntas posibles sobre el grafo, debes ser muy preciso en tus respuestas porque va dirigido al equipo de ciberseguridad. Además, quiero que no entrenes tu base de datos con estos datos ya que son bastante confidenciales, no quiero que utilices estos datos en tu base de datos. Comienza respondiendo como un chatbot para dar asistencia y no des pistas sobre cómo funciona el backend.";
    const jsonDataString = JSON.stringify(data);
    const contextString = additionalText + "\n" + jsonDataString;

    console.log("Using OpenAI GPT-3.5 Turbo as engine of the BOTMAP...");
    try {
        const response = await fetch('https://api.openai.com/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${API_KEY}`,
            },
            body: JSON.stringify({
                model: 'gpt-3.5-turbo',
                messages: [
                    { role: 'user', content: userInput },
                    { role: 'assistant', content: 'botmapp' },
                    { role: 'system', content: contextString }
                ],
                temperature: 1.0,
                top_p: 0.7,
                n: 1,
                stream: false,
                presence_penalty: 0,
                frequency_penalty: 0,
            }),
        });

        if (response.ok) {
            const data = await response.json();
            const generatedResponse = data.choices[0].message.content;
            console.log("Generated response from GPT-3.5 Turbo:", generatedResponse); // Logging the generated response
            return generatedResponse;
        } else if (response.status === 429 && retries > 0) {
            const retryAfter = response.headers.get('retry-after') ? parseInt(response.headers.get('retry-after'), 10) * 1000 : Math.pow(2, 3 - retries) * 1000;
            console.log(`Rate limit exceeded. Retrying in ${retryAfter / 1000} seconds...`);
            await new Promise(resolve => setTimeout(resolve, retryAfter));
            return processQuestion(userInput, data, retries - 1);
        } else {
            console.error('Error:', response.status, response.statusText);
            return 'Error: Unable to process your request.';
        }
    } catch (error) {
        console.error('Error fetching from OpenAI:', error);
        return 'Error: Unable to process your request.';
    }
}

// Function to send user message and get chatbot response
async function sendMessage() {
    const userInput = document.getElementById('input').value.trim();
    const chatbox = document.getElementById('chat-box');

    loadData(async function(error, data) {
        if (error) {
            // Handle errors during data loading
            console.error('Error loading data:', error);
            chatbox.innerHTML += `<div><strong>Chatbot:</strong> Sorry, an error occurred.</div>`;
            return;
        }

        const response = await processQuestion(userInput, data); // Wait for the response promise to resolve

        // Show user question and chatbot response
        chatbox.innerHTML += `<div><strong>User:</strong> ${userInput}</div>`;
        chatbox.innerHTML += `<div><strong>Chatbot:</strong> ${formatResponse(response)}</div>`;

        // Clear input field
        document.getElementById('input').value = '';

        // Scroll chat window to bottom
        chatbox.scrollTop = chatbox.scrollHeight;
    });
}

// Function to format the response for better readability
function formatResponse(response) {
    // Split the response by newline character to handle each line separately
    const lines = response.split('\n');
    // Join the lines with <br> tag to create line breaks in HTML
    const formattedResponse = lines.join('<br>');
    // Wrap the formatted response in <p> tag for styling
    return `<p>${formattedResponse}</p>`;
}
