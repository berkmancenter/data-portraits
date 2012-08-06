
var redraw, g, renderer;

/* only do all this when document has finished loading (needed for RaphaelJS) */
$(document).ready(function () {
    var width = 0.8*$("#mainstage").width();
    var height = $("#mainstage").height()*1.5;
    
    g = new Graph();
    g.addNode(user['id'], {label: user['username']});
    for (var i in connections) {
	g.addNode(connections[i].user.id, {label: connections[i].user.username, relation: connections[i].relation});
	if (connections[i].weight > 0.3) {
	    heavy = true;
	} else {
	    heavy = false;
	}
	switch (connections[i].relation) {
	    case "follower":
		style = "red";
		directed_val = true;
		break;
	    case "friend":
		style = "green";
		directed_val = true;
		break;
	    case "mutual":
		directed_val = false;
		style = "blue";
	}
	if (connections[i].relation != "follower") {
	    if (heavy) {
		g.addEdge(user['id'], connections[i].user.id, { directed: directed_val, stroke: style, fill: style });
	    } else {
		g.addEdge(user['id'], connections[i].user.id, { directed: directed_val, stroke: style });
	    }
	} else {
	    if (heavy) {
		g.addEdge(connections[i].user.id, user['id'], { directed: directed_val, stroke: style, fill: style });
	    } else {
		g.addEdge(connections[i].user.id, user['id'], { directed: directed_val, stroke: style });
	    }
	}
	
    }
    
    /* layout the graph using the Spring layout implementation */
    var layouter = new Graph.Layout.Spring(g);
    
    /* draw the graph using the RaphaelJS draw implementation */
    renderer = new Graph.Renderer.Raphael('canvas', g, width, height);
});