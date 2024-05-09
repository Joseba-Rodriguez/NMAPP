// Función para cargar los datos del grafo desde un archivo JSON
function cargarDatos(callback) {
    const xhr = new XMLHttpRequest();
    xhr.overrideMimeType("application/json");
    xhr.open('GET', 'graph_data_anomaly.json', true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                const data = JSON.parse(xhr.responseText);
                callback(null, data);
            } else {
                callback('Error al cargar los datos del grafo', null);
            }
        }
    };
    xhr.send(null);
}

// Función para obtener información sobre los grupos de nodos
function obtenerInfoGrupos(data) {
    const grupos = {};
    data.nodes.forEach(node => {
        if (!grupos[node.group]) {
            grupos[node.group] = [];
        }
        grupos[node.group].push(node.id);
    });
    return grupos;
}

// Función para obtener información sobre los valores de los enlaces
function obtenerInfoValoresEnlaces(data) {
    const valores = data.links.map(link => link.value);
    return {
        total: valores.length,
        promedio: valores.reduce((acc, curr) => acc + curr, 0) / valores.length,
        maximo: Math.max(...valores),
        minimo: Math.min(...valores)
    };
}

// Función para obtener información sobre las relaciones de un nodo
function obtenerRelacionesNodo(nodoId, data) {
    const conexiones = [];
    data.links.forEach(link => {
        if (link.source === nodoId) {
            conexiones.push(link.target);
        } else if (link.target === nodoId) {
            conexiones.push(link.source);
        }
    });
    return conexiones;
}

// Función para obtener información sobre la topología del grafo
function obtenerInfoTopologia(data) {
    const nodosConexiones = {};
    data.nodes.forEach(node => {
        nodosConexiones[node.id] = obtenerRelacionesNodo(node.id, data);
    });
    return nodosConexiones;
}

// Función para procesar la pregunta del usuario utilizando NLP
function procesarPreguntaNLP(pregunta, data) {
    // Analizar la pregunta utilizando compromise.js
    const doc = nlp(pregunta);
    
    // Obtener la intención de la pregunta
    const intencion = doc.match('#Verbo').text();
    
    // Obtener los sustantivos relevantes de la pregunta
    const sustantivos = doc.nouns().out('array');
    
    // Lógica para identificar la intención de la pregunta y extraer información relevante
    if (intencion === 'grupos') {
        return obtenerInfoGrupos(data);
    } else if (intencion === 'valores') {
        return obtenerInfoValoresEnlaces(data);
    } else if (intencion === 'relaciones') {
        return obtenerRelacionesNodo(sustantivos[0], data);
    } else if (intencion === 'topologia') {
        return obtenerInfoTopologia(data);
    } else {
        // Si la intención no coincide con ninguna conocida, responder de manera genérica
        return "No estoy seguro de entender tu pregunta. ¿Puedes ser más específico?";
    }
}

// Función para obtener información sobre los nodos según los sustantivos relevantes
function obtenerInfoNodos(sustantivos, data) {
    // Verificar si se menciona un nodo específico
    if (sustantivos.length > 0) {
        const nodo = data.nodes.find(node => node.id.includes(sustantivos[0]));
        if (nodo) {
            return `El nodo ${nodo.id} pertenece al grupo ${nodo.group}.`;
        } else {
            return `Lo siento, no encontré información sobre el nodo ${sustantivos[0]}.`;
        }
    } else {
        // Si no se especifica un nodo, devolver una lista de todos los nodos
        return data.nodes.map(node => node.id).join(', ');
    }
}

// Función para obtener información sobre los enlaces según los sustantivos relevantes
function obtenerInfoEnlaces(sustantivos, data) {
    // Verificar si se menciona un enlace específico
    if (sustantivos.length > 0) {
        const enlace = data.links.find(link => link.source.includes(sustantivos[0]) || link.target.includes(sustantivos[0]));
        if (enlace) {
            return `El enlace ${enlace.source} -> ${enlace.target} tiene un valor de ${enlace.value}.`;
        } else {
            return `Lo siento, no encontré información sobre el enlace ${sustantivos[0]}.`;
        }
    } else {
        // Si no se especifica un enlace, devolver una lista de todos los enlaces
        return data.links.map(link => `${link.source} -> ${link.target}`).join(', ');
    }
}

// Función para obtener información sobre las vulnerabilidades según los sustantivos relevantes
function obtenerInfoVulnerabilidades(sustantivos, data) {
    // Verificar si se menciona un nodo con vulnerabilidades
    const nodosConVulnerabilidades = data.nodes.filter(node => node.vulnerabilities && node.vulnerabilities.length > 0);
    if (nodosConVulnerabilidades.length > 0) {
        let infoVulnerabilidades = [];
        nodosConVulnerabilidades.forEach(node => {
            if (sustantivos.length === 0 || sustantivos.some(sustantivo => node.id.includes(sustantivo))) {
                infoVulnerabilidades.push(`Nodo ${node.id}:`);
                infoVulnerabilidades.push(...node.vulnerabilities);
            }
        });
        if (infoVulnerabilidades.length > 0) {
            return infoVulnerabilidades.join('\n');
        } else {
            return "No encontré información sobre vulnerabilidades para los nodos especificados.";
        }
    } else {
        return "No hay nodos con vulnerabilidades en el grafo.";
    }
}

// Función para procesar la pregunta del usuario
function procesarPregunta(data, userInput) {
    // Convertir la entrada del usuario a minúsculas para facilitar la comparación
    userInput = userInput.toLowerCase();

    // Lógica para procesar diferentes tipos de preguntas
    if (userInput.includes('nodos') || userInput.includes('nodo')) {
        // Obtener información sobre los nodos
        return obtenerInfoNodos([], data);
    } else if (userInput.includes('enlaces') || userInput.includes('enlace')) {
        // Obtener información sobre los enlaces
        return obtenerInfoEnlaces([], data);
    } else if (userInput.includes('vulnerabilidades') || userInput.includes('vulnerabilidad')) {
        // Obtener información sobre las vulnerabilidades
        return obtenerInfoVulnerabilidades([], data);
    } else {
        // Si la pregunta no coincide con ninguno de los patrones anteriores, utilizar NLP para comprenderla mejor
        return procesarPreguntaNLP(userInput, data);
    }
}

// Función para enviar el mensaje del usuario y obtener la respuesta del chatbot
function sendMessage() {
    const userInput = document.getElementById('input').value.trim();
    const chatbox = document.getElementById('chat-box');

    cargarDatos(function(error, data) {
        if (error) {
            // Manejar errores durante la carga de datos
            console.error('Error al cargar los datos:', error);
            chatbox.innerHTML += `<div><strong>Chatbot:</strong> Lo siento, ha ocurrido un error.</div>`;
            return;
        }

        const respuesta = procesarPregunta(data, userInput);

        // Mostrar la pregunta del usuario y la respuesta del chatbot
        chatbox.innerHTML += `<div><strong>Usuario:</strong> ${userInput}</div>`;
        chatbox.innerHTML += `<div><strong>Chatbot:</strong> ${respuesta}</div>`;

        // Limpiar el campo de entrada
        document.getElementById('input').value = '';

        // Desplazar la ventana de chat al fondo
        chatbox.scrollTop = chatbox.scrollHeight;
    });
}
