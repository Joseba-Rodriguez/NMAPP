var data = {
    "subnets": [
      {
        "id": "Subnet1",
        "color": "#1f77b4",
        "nodes": [
          { "ip": "192.168.0.1", "port": 80, "service": "HTTP" },
          { "ip": "192.168.0.2", "port": 22, "service": "SSH" },
          { "ip": "192.168.0.3", "port": 443, "service": "HTTPS" }
        ]
      },
      {
        "id": "Subnet2",
        "color": "#ff7f0e",
        "nodes": [
          { "ip": "192.168.1.1", "port": 80, "service": "HTTP" },
          { "ip": "192.168.1.2", "port": 443, "service": "HTTPS" },
          { "ip": "192.168.1.3", "port": 22, "service": "SSH" }
        ]
      }
    ],
    "links": [
      { "source": "192.168.0.1", "target": "192.168.0.2" },
      { "source": "192.168.0.2", "target": "192.168.0.3" },
      { "source": "192.168.1.1", "target": "192.168.1.2" },
      { "source": "192.168.1.2", "target": "192.168.1.3" }
    ]
  };
  
  var links = data.links;
  var nodes = [];
  
  // Unimos los nodos por subred
  data.subnets.forEach(function(subnet) {
    var subnetNodes = subnet.nodes.map(function(node) {
      return { id: node.ip, subnet: subnet.id, color: subnet.color, port: node.port, service: node.service };
    });
    nodes = nodes.concat(subnetNodes);
  });
  
  links = data.links.map(function(l) {
    var sourceNode = nodes.find(function(n) {
        return n.id === l.source;
      }),
      targetNode = nodes.find(function(n) {
        return n.id === l.target;
      });
    return {
      source: sourceNode,
      target: targetNode
    };
  });
  
  var margin = { top: 20, right: 20, bottom: 20, left: 20 };
  var width = window.innerWidth - margin.left - margin.right;
  var height = window.innerHeight - margin.top - margin.bottom;
  var radius = 6;
  
  var force = d3.layout.force()
      .nodes(d3.values(nodes))
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
      .style("fill", function(d) { return d.color; });
  
  node.append("text")
      .attr("x", 14)
      .attr("dy", ".35em")
      .style("fill", "#333")
      .text(function (d) {
      return d.id;
  });
  
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
        var visible = false;
        for (var key in d) {
          if (d.hasOwnProperty(key)) {
            if (d[key] && d[key].toString().toLowerCase().indexOf(searchText) > -1) {
              visible = true;
              break;
            }
          }
        }
        return visible ? "block" : "none";
      });
      link.style("display", function(d) {
        return d.source.style === "block" && d.target.style === "block" ? "block" : "none";
      });
    }
  }
  
  function click(d) {
    infoBox.html(
      "<strong>IP:</strong> " + d.id + "<br>" +
      "<strong>Port:</strong> " + d.port + "<br>" +
      "<strong>Service:</strong> " + d.service
    );
  }
  
  function tick() {
      link.attr("x1", function (d) {
          return d.source.x;
      })
          .attr("y1", function (d) {
          return d.source.y;
      })
          .attr("x2", function (d) {
          return d.target.x;
      })
          .attr("y2", function (d) {
          return d.target.y;
      });
  
      var dx = function(d) {return Math.max(radius, Math.min(width - radius, d.x))}
      var dy = function(d) {return Math.max(radius, Math.min(height - radius, d.y))}
  
      node.attr("transform", function (d) {
          return "translate(" + dx(d) + "," + dy(d) + ")";
      });
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
  