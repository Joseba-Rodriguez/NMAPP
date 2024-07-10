// Cargar datos desde el archivo JSON
d3.json('./grafo/anomaly/graph_data_anomaly.json').then(function(data) {
    const svg = d3.select('svg')
        .attr('width', '150%')
        .attr('height', '100%')
        .call(d3.zoom().on('zoom', function(event) {
            svg.attr('transform', event.transform);
        }))
        .append('g');

    const width = window.innerWidth;
    const height = window.innerHeight;

    const simulation = d3.forceSimulation(data.nodes)
        .force('link', d3.forceLink(data.links).id(d => d.id).distance(50))
        .force('charge', d3.forceManyBody().strength(-50))
        .force('center', d3.forceCenter(width / 3, height / 3));

    let link = svg.selectAll('.link')
        .data(data.links)
        .enter().append('line')
        .attr('class', 'link')
        .attr('stroke', '#aaa')  // Color de los enlaces
        .attr('stroke-width', 2);  // Ancho de los enlaces

    let node = svg.selectAll('.node')
        .data(data.nodes)
        .enter().append('g')
        .attr('class', 'node')
        .call(d3.drag()
            .on('start', dragstarted)
            .on('drag', dragged)
            .on('end', dragended))
        .on('mouseover', handleMouseOver) // Manejador para pasar el cursor sobre el nodo
        .on('mouseout', handleMouseOut)   // Manejador para alejar el cursor del nodo
        .on('click', handleClick);        // Manejador para hacer clic en el nodo

    node.append('circle')
        .attr('r', 10)
        .attr('fill', d => getNodeColor(d))
        .attr('stroke', '#fff')
        .attr('stroke-width', 2)
        .style('cursor', 'pointer');  // Cambiar el cursor al pasar sobre el nodo

    node.append('text')
        .attr('x', 12)
        .attr('dy', '.35em')
        .style('font-size', '12px')
        .style('font-family', 'Arial')
        .style('fill', d => {
            if (d.vulnerabilities && d.vulnerabilities.length > 0) {
                return '#FF0000'; // Color rojo para alerta de vulnerabilidad
            } else {
                return getNodeColor(d); // Obtener el color del nodo
            }
        })
        .text(d => d.id);

    simulation.on('tick', () => {
        link.attr('x1', d => d.source.x)
            .attr('y1', d => d.source.y)
            .attr('x2', d => d.target.x)
            .attr('y2', d => d.target.y);

        node.attr('transform', d => `translate(${d.x},${d.y})`);
    });

    function getNodeColor(node) {
        const colorMap = {
            1: '#1f77b4',  // Azul para IP
            2: '#2ca02c',  // Verde para Servicio Común
            3: '#FFFF00',  // Amarillo para Anómalo
            4: '#FF0000'   // Rojo para Vulnerabilidad
        };

        // Verificar si hay vulnerabilidades asociadas al nodo
        if (node.vulnerabilities && node.vulnerabilities.length > 0) {
            return '#FF0000'; // Rojo para alerta de vulnerabilidad
        }
        
        return colorMap[node.group];
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

    function handleMouseOver(event, d) {
        // Resaltar nodo y enlaces conectados directamente
        const connectedNodes = new Set();
        const connectedLinks = new Set();
        data.links.forEach(link => {
            if (link.source.id === d.id) {
                connectedNodes.add(link.target.id);
                connectedLinks.add(link);
            } else if (link.target.id === d.id) {
                connectedNodes.add(link.source.id);
                connectedLinks.add(link);
            }
        });

        // Cambiar el color de los nodos
        svg.selectAll('.node')
            .attr('fill', node => connectedNodes.has(node.id) || node.id === d.id ? getNodeColor(node.group) : '#ddd')
            .attr('opacity', node => connectedNodes.has(node.id) || node.id === d.id ? 1 : 0.3);

        // Cambiar el color de los enlaces
        svg.selectAll('.link')
            .attr('stroke', link => connectedLinks.has(link) ? '#aaa' : '#eee')
            .attr('opacity', link => connectedLinks.has(link) ? 1 : 0.3);
    }
    function handleMouseOut() {
        // Restaurar color y opacidad de todos los nodos y enlaces
        svg.selectAll('.node')
            .attr('fill', d => getNodeColor(d.group))
            .attr('opacity', 1);

        svg.selectAll('.link')
            .attr('stroke', '#aaa')
            .attr('opacity', 1);
    }

    function handleClick(event, d) {
        // Resaltar el nodo y sus conexiones al hacer clic en él
        const clickedNode = d;
    
        // Resaltar nodo y enlaces conectados directamente
        svg.selectAll('.node')
            .attr('fill', node => node === clickedNode ? '#ff0000' : '#ddd')
            .attr('opacity', node => node === clickedNode ? 1 : 0.3);
    
        svg.selectAll('.link')
            .attr('stroke', link => link.source === clickedNode || link.target === clickedNode ? '#ffa500' : '#eee')
            .attr('opacity', link => link.source === clickedNode || link.target === clickedNode ? 1 : 0.3);
    
        // Mostrar información detallada del nodo
        let infoHTML = `<h3>Información detallada de la IP ${clickedNode.id}</h3>`;
        if (clickedNode.vulnerabilities && clickedNode.vulnerabilities.length > 0) {
            // Si hay vulnerabilidades, mostrarlas como una lista
            infoHTML += '<p>Vulnerabilidades:</p>';
            infoHTML += '<ul>';
            clickedNode.vulnerabilities.forEach(vulnerability => {
                infoHTML += `<li>${vulnerability}</li>`;
            });
            infoHTML += '</ul>';
        }
    
        // Calcular el total de conexiones, servicios comunes y anomalías
        const totalConnections = data.links.filter(link => link.source === clickedNode || link.target === clickedNode).length;
        const commonServices = data.links.filter(link => (link.source === clickedNode || link.target === clickedNode) && link.group === 2).length;
        const anomalyServices = data.links.filter(link => (link.source === clickedNode || link.target === clickedNode) && link.group === 3).length;
    
        infoHTML += `
            <p>Total de conexiones: ${totalConnections}</p>
            <p>Servicios comunes: ${commonServices}</p>
            <p>Anomalías: ${anomalyServices}</p>
        `;
    
        const infoDiv = document.getElementById('info');
        infoDiv.innerHTML = infoHTML;
    }

    // Función de filtrado
// Función para filtrar el grafo
// Función para filtrar el grafo
function filterGraph(filter) {
    // Limpia la visualización actual
    d3.selectAll("circle").style("opacity", 1);
    d3.selectAll("text").style("opacity", 1); // Restaurar la opacidad del texto
    d3.selectAll("line").style("opacity", 1);

    // Aplica el filtro según el botón seleccionado
    if (filter === "all") {
        d3.selectAll("circle").style("opacity", 1);
        d3.selectAll("text").style("opacity", 1);
        d3.selectAll("line").style("opacity", 1);
    } else if (filter === "ip") {
        d3.selectAll("circle")
            .style("opacity", d => d.group === 1 ? 1 : 0.1);
        d3.selectAll("text")
            .style("opacity", d => d.group === 1 ? 1 : 0.1); // Establecer opacidad baja para el texto de nodos no seleccionados
    } else if (filter === "common") {
        d3.selectAll("circle")
            .style("opacity", d => d.group === 2 ? 1 : 0.1);
        d3.selectAll("text")
            .style("opacity", d => d.group === 2 ? 1 : 0.1);
    } else if (filter === "anomalous") {
        d3.selectAll("circle")
            .style("opacity", d => d.group === 3 ? 1 : 0.1);
        d3.selectAll("text")
            .style("opacity", d => d.group === 3 ? 1 : 0.1);
    } else if (filter === "vulnerable") {
        d3.selectAll("circle")
            .style("opacity", d => d.vulnerabilities ? 1 : 0.1);
        d3.selectAll("text")
            .style("opacity", d => d.vulnerabilities ? 1 : 0.1);
    }

    // Oculta los enlaces si los nodos están ocultos
    d3.selectAll("line").style("opacity", function() {
        var sourceVisible = d3.select(`#${this.getAttribute("source")}`).style("opacity") == 1;
        var targetVisible = d3.select(`#${this.getAttribute("target")}`).style("opacity") == 1;
        return sourceVisible && targetVisible ? 1 : 0.1;
    });
}

    // Hacer que la función de filtro esté disponible globalmente
    window.filterGraph = filterGraph;

    // Inicializar con todos los nodos visibles
    filterGraph('all');
});
