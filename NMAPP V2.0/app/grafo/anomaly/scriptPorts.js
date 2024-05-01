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
        .on('mouseover', handleMouseOver) // Manejador para pasar el cursor sobre el nodo
        .on('mouseout', handleMouseOut);   // Manejador para alejar el cursor del nodo

    node.append('circle')
        .attr('r', 10)
        .attr('fill', d => getNodeColor(d.group))
        .attr('stroke', '#fff')
        .attr('stroke-width', 2);

    node.append('text')
        .attr('x', 12)
        .attr('dy', '.35em')
        .style('font-size', '12px')
        .style('font-family', 'Arial')
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

    // Añadir funcionalidad de búsqueda
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('input', function(event) {
        const searchText = event.target.value.toLowerCase();

        const filteredNodes = data.nodes.filter(node => node.id.toLowerCase().includes(searchText));

        // Ocultar nodos que no coinciden con el texto de búsqueda
        svg.selectAll('.node')
            .style('display', node => filteredNodes.some(filteredNode => filteredNode.id === node.id) ? 'inline' : 'none');

        // Ocultar enlaces que no están conectados a nodos visibles
        svg.selectAll('.link')
            .style('display', link => {
                const sourceVisible = filteredNodes.some(filteredNode => filteredNode.id === link.source.id);
                const targetVisible = filteredNodes.some(filteredNode => filteredNode.id === link.target.id);
                return sourceVisible && targetVisible ? 'inline' : 'none';
            });
    });
    
});

