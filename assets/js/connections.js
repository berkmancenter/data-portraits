
var redraw, g, renderer;

function calculateSize(count) {
    var size;
    if (count <= 100) {
	size = 2;
    } else if (count < 250) {
	size = 4;
    } else if (count < 500) {
	size = 7;
    } else if (count <= 1000) {
	size = 10;
    } else if (count <= 2000) {
	size = 12;
    } else if (count <= 3000) {
	size = 14;
    } else if (count <= 4000) {
	size = 16;
    } else if (count <= 5000) {
	size = 18;
    } else if (count <= 10000) {
	size = 22;
    } else if (count <= 25000) {
	size = 25;
    } else {
	size = 30;
    }
    return size;
}
/* only do all this when document has finished loading (needed for RaphaelJS) */
$(document).ready(function () {
    var width = 0.8*$("#mainstage").width();
    var height = $("#mainstage").height()*1.5;
    
    g = new Graph();
    g.addNode(user['id'], {label: user['username']});
    for (var i in connections) {
	if (type == "follower") {
	    friend_circle_size = calculateSize(connections[i].user.friends_count);
	    follower_circle_size = calculateSize(connections[i].user.followers_count);
	    g.addNode(connections[i].user.id, {label: connections[i].user.username, relation: connections[i].relation,
		      friend_count: friend_circle_size, follower_count: follower_circle_size});
	} else {
	    status_circle_size = calculateSize(connections[i].user.statuses_count);
	    g.addNode(connections[i].user.id, {label: connections[i].user.username, relation: connections[i].relation,
		      status_count: status_circle_size});
	}
	
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