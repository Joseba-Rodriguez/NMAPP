// Cargar datos desde el archivo JSON
d3.json('graph_data_anomaly.json').then(function(data) {
    const svg = d3.select('svg')
        .attr('width', '100%')
        .attr('height', '100%')
        .call(d3.zoom().on('zoom', function(event) {
            svg.attr('transform', event.transform);
        }))
        .append('g');

    const width = window.innerWidth;
    const height = window.innerHeight;

    const simulation = d3.forceSimulation(data.nodes)
        .force('link', d3.forceLink(data.links).id(d => d.id).distance(100))
        .force('charge', d3.forceManyBody().strength(-200))
        .force('center', d3.forceCenter(width / 2, height / 2));

    const link = svg.selectAll('.link')
        .data(data.links)
        .enter().append('line')
        .attr('class', 'link')
        .attr('stroke', '#aaa')  // Color de los enlaces
        .attr('stroke-width', 2);  // Ancho de los enlaces

    const node = svg.selectAll('.node')
        .data(data.nodes)
        .enter().append('g')
        .attr('class', 'node')
        .call(d3.drag()
            .on('start', dragstarted)
            .on('drag', dragged)
            .on('end', dragended))
        .on('click', handleClick);  // Añadir clic para ampliar nodo y mostrar información

    node.append('circle')
        .attr('r', 10)
        .attr('fill', d => getNodeColor(d.group));

    node.append('text')
        .attr('x', 12)
        .attr('dy', '.35em')
        .text(d => d.id);

    simulation.on('tick', () => {
        link.attr('x1', d => d.source.x)
            .attr('y1', d => d.source.y)
            .attr('x2', d => d.target.x)
            .attr('y2', d => d.target.y);

        node.attr('transform', d => `translate(${d.x},${d.y})`);
    });

    function getNodeColor(group) {
        const colorMap = {
            1: '#1f77b4',  // Azul para IP
            2: '#2ca02c',  // Verde para Servicio Común
            3: '#ff0000'   // Rojo para Anómalo
        };
        return colorMap[group];
    }

    function dragstarted(event, d) {
        if (!event.active) simulation.alphaTarget(0.3).restart();
        d.fx = d.x;
        d.fy = d.y;
    }

    function dragged(event, d) {
        d.fx = event.x;
        d.fy = event.y;
    }

    function dragended(event, d) {
        if (!event.active) simulation.alphaTarget(0);
        d.fx = null;
        d.fy = null;
    }

    function handleClick(event, d) {
        // Aumentar el tamaño del nodo al hacer clic
        d3.select(this).select('circle').attr('r', 15);

        // Mostrar información del puerto y servicio
        const infoDiv = d3.select('#info');
        infoDiv.html(`
            <h4>Información del Nodo</h4>
            <p>ID: ${d.id}</p>
            <p>Grupo: ${d.group === 1 ? 'IP' : (d.group === 2 ? 'Servicio Común' : 'Anómalo')}</p>
        `);
    }
    function generateReport(data) {
        const nodesCount = data.nodes.length;
        const linksCount = data.links.length;
    
        // Contar nodos por grupo
        const groupCounts = {};
        data.nodes.forEach(node => {
            if (!groupCounts[node.group]) {
                groupCounts[node.group] = 0;
            }
            groupCounts[node.group]++;
        });
    
        // Nodos más conectados
        const connectedNodes = {};
        data.links.forEach(link => {
            if (!connectedNodes[link.source]) {
                connectedNodes[link.source] = 0;
            }
            if (!connectedNodes[link.target]) {
                connectedNodes[link.target] = 0;
            }
            connectedNodes[link.source]++;
            connectedNodes[link.target]++;
        });
    
        // Encontrar nodo más conectado
        let mostConnectedNode = '';
        let maxConnections = 0;
        Object.keys(connectedNodes).forEach(nodeId => {
            if (connectedNodes[nodeId] > maxConnections) {
                mostConnectedNode = nodeId;
                maxConnections = connectedNodes[nodeId];
            }
        });
    
        // Crear el informe
        const report = `
            <h2>Informe de Análisis de Datos</h2>
            <p><strong>Número total de nodos:</strong> ${nodesCount}</p>
            <p><strong>Número total de enlaces:</strong> ${linksCount}</p>
            <h3>Nodos por Grupo</h3>
            <ul>
                ${Object.keys(groupCounts).map(group => `<li>Grupo ${group}: ${groupCounts[group]} nodos</li>`).join('')}
            </ul>
            <h3>Nodo más conectado</h3>
            <p><strong>ID:</strong> ${mostConnectedNode}</p>
            <p><strong>Número de conexiones:</strong> ${maxConnections}</p>
        `;
    
        // Mostrar el informe en una ventana o en un elemento HTML
        const reportWindow = window.open('', '_blank');
        reportWindow.document.body.innerHTML = report;
    }
    
    // Botón para generar el informe
    document.getElementById('generateReportBtn').addEventListener('click', function() {
        const jsonData = {
            // Aquí iría el JSON con los datos del grafo
        };
        generateReport(jsonData);
    });
    
});
