
var redraw, g, renderer;

var d = new Date;
var cur_date = new Object;
cur_date.date = d.getDate();
cur_date.month = d.getMonth();
cur_date.year = d.getFullYear();
var cur_utc = Date.UTC(cur_date.year,cur_date.month,cur_date.date)/1000;

function calculateSize(count) {
    var size;
    if (count <= 100) {
	size = 4;
    } else if (count < 250) {
	size = 6;
    } else if (count < 500) {
	size = 8;
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
    } else if (count <= 14000) {
	size = 24;
    } else if (count <= 20000) {
	size = 26;
    } else if (count <= 25000) {
	size = 28;
    } else {
	size = 30;
    }
    return size;
}

function getPostingFrequency(joined, status_count) {
    var arr = joined.split(' ');
    var joined_utc = Date.parse(arr[1] + ' ' + arr[2] + ', ' + arr[5])/1000;
    var since = Math.round((cur_utc - joined_utc)/(86400*7));
    return status_count/since;
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
		      friend_size: friend_circle_size, follower_size: follower_circle_size,
		      friend_count: connections[i].user.friends_count, follower_count: connections[i].user.followers_count});
	} else {
	    var freq = getPostingFrequency(connections[i].user.joined, connections[i].user.statuses_count);
	    freq_all[connections[i].user.username] = freq;
	    status_circle_size = calculateSize(freq*250);
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