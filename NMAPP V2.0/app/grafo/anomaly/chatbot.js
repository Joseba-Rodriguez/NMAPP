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

// Function to get information about node groups
function getGroupInfo(data) {
    const groups = {};
    data.nodes.forEach(node => {
        if (!groups[node.group]) {
            groups[node.group] = [];
        }
        groups[node.group].push(node.id);
    });
    return groups;
}

// Function to get information about link values
function getLinkValueInfo(data) {
    const values = data.links.map(link => link.value);
    return {
        total: values.length,
        average: values.reduce((acc, curr) => acc + curr, 0) / values.length,
        max: Math.max(...values),
        min: Math.min(...values)
    };
}

// Function to get information about node relations
function getNodeRelations(nodeId, data) {
    const connections = [];
    data.links.forEach(link => {
        if (link.source === nodeId) {
            connections.push(link.target);
        } else if (link.target === nodeId) {
            connections.push(link.source);
        }
    });
    return connections;
}

// Function to get information about graph topology
function getTopologyInfo(data) {
    const nodeConnections = {};
    data.nodes.forEach(node => {
        nodeConnections[node.id] = getNodeRelations(node.id, data);
    });
    return nodeConnections;
}

// Function to process user question using NLP
function processQuestionNLP(question, data) {
    // Analyze the question using compromise.js
    console.log("Analyzing the question using compromise.js...");
    const doc = nlp(question);
    
    // Get the intention of the question
    console.log("Extracting the intention of the question...");
    const intention = doc.match('#Verb').text();
    console.log("Question intention:", intention);
    
    // Get the relevant nouns from the question
    console.log("Extracting relevant nouns...");
    const nouns = doc.nouns().out('array');
    console.log("Relevant nouns:", nouns);
    
    // Logic to identify the question intention and extract relevant information
    if (intention === 'groups') {
        console.log("Identified intention: groups");
        return getGroupInfo(data);
    } else if (intention === 'values') {
        console.log("Identified intention: values");
        return getLinkValueInfo(data);
    } else if (intention === 'relations') {
        console.log("Identified intention: relations");
        return getNodeRelations(nouns[0], data);
    } else if (intention === 'topology') {
        console.log("Identified intention: topology");
        return getTopologyInfo(data);
    } else {
        // If intention cannot be identified, use NLP information to generate a response
        const nounsStr = nouns.length > 0 ? nouns.join(', ') : "no nouns";
        console.log("Could not identify question intention. Showing relevant nouns:", nounsStr);
        return `I cannot understand the intention of the question, but here are the relevant nouns: ${nounsStr}`;
    }
}

// Function to get information about nodes based on relevant nouns
function getNodeInfo(nouns, data) {
    // Check if a specific node is mentioned
    if (nouns.length > 0) {
        const node = data.nodes.find(node => node.id.includes(nouns[0]));
        if (node) {
            return `Node ${node.id} belongs to group ${node.group}.`;
        } else {
            return `Sorry, I couldn't find information about node ${nouns[0]}.`;
        }
    } else {
        // If no node is specified, return a list of all nodes
        return data.nodes.map(node => node.id).join(', ');
    }
}

// Function to get information about links based on relevant nouns
function getLinkInfo(nouns, data) {
    // Check if a specific link is mentioned
    if (nouns.length > 0) {
        const link = data.links.find(link => link.source.includes(nouns[0]) || link.target.includes(nouns[0]));
        if (link) {
            return `Link ${link.source} -> ${link.target} has a value of ${link.value}.`;
        } else {
            return `Sorry, I couldn't find information about the link ${nouns[0]}.`;
        }
    } else {
        // If no link is specified, return a list of all links
        return data.links.map(link => `${link.source} -> ${link.target}`).join(', ');
    }
}

// Function to get information about vulnerabilities based on relevant nouns
function getVulnerabilityInfo(nouns, data) {
    // Check if a node with vulnerabilities is mentioned
    const nodesWithVulnerabilities = data.nodes.filter(node => node.vulnerabilities && node.vulnerabilities.length > 0);
    if (nodesWithVulnerabilities.length > 0) {
        let vulnerabilityInfo = [];
        nodesWithVulnerabilities.forEach(node => {
            if (nouns.length === 0 || nouns.some(noun => node.id.includes(noun))) {
                vulnerabilityInfo.push(`Node ${node.id}:`);
                vulnerabilityInfo.push(...node.vulnerabilities);
            }
        });
        if (vulnerabilityInfo.length > 0) {
            return vulnerabilityInfo.join('\n');
        } else {
            return "I couldn't find information about vulnerabilities for the specified nodes.";
        }
    } else {
        return "There are no nodes with vulnerabilities in the graph.";
    }
}

// Function to process user question using NLP
function processQuestion(userInput, data) {
    console.log("Processing user question...");
    console.log("User input:", userInput);

    // Analyze the question using compromise.js
    console.log("Analyzing the question using compromise.js...");
    const doc = nlp(userInput);
    
    // Get the intention of the question
    console.log("Extracting the intention of the question...");
    const intention = doc.match('#Verb').text();
    console.log("Question intention:", intention);
    
    // Get the relevant nouns from the question
    console.log("Extracting relevant nouns...");
    const nouns = doc.nouns().out('array');
    console.log("Relevant nouns:", nouns);
    
    // Logic to identify the question intention and extract relevant information
    if (intention === 'groups') {
        console.log("Identified intention: groups");
        return getGroupInfo(data);
    } else if (intention === 'values') {
        console.log("Identified intention: values");
        return getLinkValueInfo(data);
    } else if (intention === 'relations') {
        console.log("Identified intention: relations");
        return getNodeRelations(nouns[0], data);
    } else if (intention === 'topology') {
        console.log("Identified intention: topology");
        return getTopologyInfo(data);
    } else {
        // If intention cannot be identified, use NLP information to generate a response
        console.log("Could not identify question intention. Processing generic question...");
        return processGenericQuestion(userInput, data);
    }
}

// Function to handle generic questions
function processGenericQuestion(userInput, data) {
    // Analyze the question using compromise.js
    console.log("Analyzing generic question using compromise.js...");
    const doc = nlp(userInput);
    
    // Get the keywords from the question
    console.log("Extracting keywords from the question...");
    const keywords = doc.match('(#Verb|#Noun)').out('array');
    console.log("Keywords:", keywords);

    // Logic to process different types of questions
    if (keywords.includes('nodes') || keywords.includes('node')) {
        console.log("Identified intention: nodes");
        return getNodeInfo([], data);
    } else if (keywords.includes('links') || keywords.includes('link')) {
        console.log("Identified intention: links");
        return getLinkInfo([], data);
    } else if (keywords.includes('vulnerabilities') || keywords.includes('vulnerability')) {
        console.log("Identified intention: vulnerabilities");
        return getVulnerabilityInfo([], data);
    } else if (keywords.includes('topology')) {
        console.log("Identified intention: topology");
        return getTopologyInfo(data);
    } else if (keywords.includes('relations')) {
        console.log("Identified intention: relations");
        return "Please specify a node ID to get its relations.";
    } else {
        // If the question doesn't match any patterns above, provide a generic response
        console.log("Could not identify specific intention. Providing generic response...");
        return "I couldn't understand your question. Can you please rephrase?";
    }
}

// Function to send user message and get chatbot response
function sendMessage() {
    const userInput = document.getElementById('input').value.trim();
    const chatbox = document.getElementById('chat-box');

    loadData(function(error, data) {
        if (error) {
            // Handle errors during data loading
            console.error('Error loading data:', error);
            chatbox.innerHTML += `<div><strong>Chatbot:</strong> Sorry, an error occurred.</div>`;
            return;
        }

        const response = processQuestion(userInput, data);

        // Show user question and chatbot response
        chatbox.innerHTML += `<div><strong>User:</strong> ${userInput}</div>`;
        chatbox.innerHTML += `<div><strong>Chatbot:</strong> ${response}</div>`;

        // Clear input field
        document.getElementById('input').value = '';

        // Scroll chat window to bottom
        chatbox.scrollTop = chatbox.scrollHeight;
    });
}
