fetch('./data.json')
  .then(response => response.json())
  .then(data => {

    var nodes = data.nodes;
    var links = data.links;

    var nodesDict = {};
    nodes.forEach(function(node) {
      nodesDict[node.ip] = {
        ip: node.ip,
        ports: node.ports
      };
    });

    links = links.map(function(link) {
      return {
        source: nodesDict[link.source],
        target: nodesDict[link.target]
      };
    });

    var margin = { top: 20, right: 20, bottom: 20, left: 20 };
    var width = window.innerWidth - margin.left - margin.right;
    var height = window.innerHeight - margin.top - margin.bottom;
    var radius = 8;

    var zoom = d3.behavior.zoom()
        .scaleExtent([1, 10])
        .on("zoom", zoomed);

    var uniqueSubnets = [...new Set(nodes.map(node => node.ip.split('.').slice(0, 3).join('.')))];
    var colorScale = d3.scale.category10().domain(uniqueSubnets);

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
        .attr("preserveAspectRatio", "xMidYMid meet")
        .call(zoom);

    var container = svg.append("g");

    var link = container.selectAll(".link")
        .data(force.links())
        .enter().append("line")
        .attr("class", "link");

    var node = container.selectAll(".node")
        .data(force.nodes())
        .enter().append("g")
        .attr("class", "node")
        .on("mouseover", mouseover)
        .on("mouseout", mouseout)
        .on("click", click)
        .call(force.drag);

    node.append("circle")
        .attr("r", radius)
        .style("fill", function(d) {
            return colorScale(d.ip.split('.').slice(0, 3).join('.'));
        });

    node.append("text")
        .attr("x", 14)
        .attr("dy", ".35em")
        .text(function(d) { return d.ip; });

    var infoBox = d3.select("body")
      .append("div")
      .attr("class", "infobox")
      .style("position", "absolute")
      .style("top", "20px")
      .style("right", "20px");

    var legend = d3.select("body").append("div")
        .attr("class", "legend")
        .on("click", filterNodes);

    // Añade la leyenda con los colores de las subredes
    legend.selectAll("div")
        .data(uniqueSubnets)
        .enter().append("div")
        .style("color", d => colorScale(d))
        .style("border-bottom", "1px solid #ccc")
        .text(d => d);

    function zoomed() {
      container.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
    }

    function filterNodes(d) {
      if (d) {
          // Filtrar nodos basados en la subred seleccionada
          node.style("display", function(n) {
              return n.ip.startsWith(d) ? "block" : "none";
          });
  
          // Filtrar enlaces basados en la subred seleccionada
          link.style("display", function(l) {
              return l.source.ip.startsWith(d) || l.target.ip.startsWith(d) ? "block" : "none";
          });
  
          // Actualizar el estilo de la leyenda
          legend.selectAll("div").style("font-weight", function(subnet) {
              return subnet === d ? "bold" : "normal";
          });
      } else {
          // Mostrar todos los nodos y enlaces si no se selecciona ninguna subred
          node.style("display", "block");
          link.style("display", "block");
  
          // Resetear el estilo de la leyenda
          legend.selectAll("div").style("font-weight", "normal");
      }
  }

    document.getElementById("searchIpInput").addEventListener("input", searchNodes);
    document.getElementById("searchPortInput").addEventListener("input", searchNodes);
    document.getElementById("searchServiceInput").addEventListener("input", searchNodes);

    function searchNodes() {
      var searchTextIp = document.getElementById("searchIpInput").value.toLowerCase();
      var searchTextPort = document.getElementById("searchPortInput").value.toLowerCase();
      var searchTextService = document.getElementById("searchServiceInput").value.toLowerCase();

      node.style("display", function(d) {
        return d.ip.toLowerCase().indexOf(searchTextIp) > -1 &&
               d.ports.some(function(port) {
                 return port.port.toString().indexOf(searchTextPort) > -1 &&
                        port.service.toLowerCase().indexOf(searchTextService) > -1;
               }) ? "block" : "none";
      });

      link.style("display", function(d) {
        return d.source.ip.toLowerCase().indexOf(searchTextIp) > -1 &&
               d.target.ip.toLowerCase().indexOf(searchTextIp) > -1 ? "block" : "none";
      });
    }

    function click(d) {
      var nodeDetails = d3.select("#nodeDetails");
      nodeDetails.html(
          "<tr><td>" + d.ip + "</td><td>" + 
          d.ports.map(port => port.port).join(", ") + 
          "</td><td>" + 
          d.ports.map(port => port.service).join(", ") + 
          "</td></tr>"
      );
  
      node.select("circle")
          .attr("r", function(node) {
              return node === d ? 12 : 8;
          });
  
      link.style("stroke-opacity", function(link) {
          return link.source === d || link.target === d ? 0.9 : 0.6;
      });
    }

    function tick() {
        link.attr("x1", function(d) { return d.source.x; })
            .attr("y1", function(d) { return d.source.y; })
            .attr("x2", function(d) { return d.target.x; })
            .attr("y2", function(d) { return d.target.y; });

        node.attr("transform", function(d) { return "translate(" + d.x + "," + d.y + ")"; });
    }

    function mouseover() {
        d3.select(this).select("circle").transition()
            .duration(200)
            .attr("r", 12);
    }

    function mouseout() {
        d3.select(this).select("circle").transition()
            .duration(200)
            .attr("r", 8);
    }
})
.catch(error => console.error('Error cargando el JSON:', error));
