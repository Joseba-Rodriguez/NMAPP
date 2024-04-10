// Cargar el JSON desde un archivo utilizando una solicitud HTTP (puedes usar fetch si estás en un entorno de navegador)
fetch('data.json')
  .then(response => response.json())
  .then(data => {
    // Ahora puedes usar los datos cargados en lugar de los datos manuales

    var nodes = data.nodes;
    var links = data.links;

    // Crear el diccionario de nodos por IP
    var nodesDict = {};
    nodes.forEach(function(node) {
      nodesDict[node.ip] = {
        ip: node.ip,
        ports: node.ports
      };
    });

    // Unir los enlaces (links)
    links = links.map(function(link) {
      return {
        source: nodesDict[link.source],
        target: nodesDict[link.target]
      };
    });

    var margin = { top: 20, right: 20, bottom: 20, left: 20 };
    var width = window.innerWidth - margin.left - margin.right;
    var height = window.innerHeight - margin.top - margin.bottom;
    var radius = 6;

    var force = d3.layout.force()
        .nodes(d3.values(nodesDict))
        .links(links)
        .size([width, height])
        .linkDistance(120)
        .charge(-200)
        .on("tick", tick)
        .start();

    var svg = d3.select("body").append("svg")
        .attr("width", "100%")
        .attr("height", "100%")
        .attr("viewBox", "0 0 " + width + " " + height)
        .attr("preserveAspectRatio", "xMidYMid meet");

    var link = svg.selectAll(".link")
        .data(force.links())
        .enter().append("line")
        .attr("class", "link");

    var node = svg.selectAll(".node")
        .data(force.nodes())
        .enter().append("g")
        .attr("class", "node")
        .on("mouseover", mouseover)
        .on("mouseout", mouseout)
        .on("click", click)
        .call(force.drag);

    node.append("circle")
        .attr("r", radius)
        .style("fill", "#1f77b4");

    node.append("text")
        .attr("x", 14)
        .attr("dy", ".35em")
        .style("fill", "#333")
        .text(function(d) { return d.ip; });

    var infoBox = d3.select("body")
      .append("div")
      .attr("class", "infobox")
      .style("position", "absolute")
      .style("top", "20px")
      .style("right", "20px");

    var searchInput = d3.select("body")
      .insert("input", "svg")
      .attr("type", "text")
      .attr("placeholder", "Filtrar...")
      .on("input", searchNodes);

function searchNodes() {
  var searchText = this.value.toLowerCase();
  if (searchText === "") {
    node.style("display", "block");
    link.style("display", "block");
  } else {
    node.style("display", function(d) {
      return d.ip.toLowerCase().indexOf(searchText) > -1 ||
             d.ports.some(function(port) {
               return port.port.toString().indexOf(searchText) > -1 || port.service.toLowerCase().indexOf(searchText) > -1;
             }) ? "block" : "none";
    });
    link.style("display", function(d) {
      return d.source.ip.toLowerCase().indexOf(searchText) > -1 && d.target.ip.toLowerCase().indexOf(searchText) > -1 ? "block" : "none";
    });
  }
}
    function click(d) {
      infoBox.html(
        "<strong>IP:</strong> " + d.ip + "<br>" +
        "<strong>Ports:</strong> " + d.ports.map(function(port) { return port.port; }).join(", ") + "<br>" +
        "<strong>Services:</strong> " + d.ports.map(function(port) { return port.service; }).join(", ")
      );
    }

    function tick() {
        link.attr("x1", function(d) { return d.source.x; })
            .attr("y1", function(d) { return d.source.y; })
            .attr("x2", function(d) { return d.target.x; })
            .attr("y2", function(d) { return d.target.y; });

        var dx = function(d) { return Math.max(radius, Math.min(width - radius, d.x)); }
        var dy = function(d) { return Math.max(radius, Math.min(height - radius, d.y)); }

        node.attr("transform", function(d) { return "translate(" + dx(d) + "," + dy(d) + ")"; });
    }

    function mouseover() {
        d3.select(this).select("circle").transition()
            .duration(750)
            .attr("r", 16);
            d3.select(this).select("text").transition()
            .duration(750)
            .style("font-size", 16);
    }

    function mouseout() {
        d3.select(this).select("circle").transition()
            .duration(750)
            .attr("r", 6);

            d3.select(this).select("text").transition()
            .duration(750)
            .style("font-size", 14);
    }
  })
  .catch(error => console.error('Error cargando el JSON:', error));
