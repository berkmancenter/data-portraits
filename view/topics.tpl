<script type="text/javascript" src="{$site_root_path}extlib/jQuery/jquery1.7.2.min.js"></script>
<script type="text/javascript">{$statuses}</script>
<script type="text/javascript">
    var active_topics = new Array();
    var num_topics = null;
    var topics = null;
    var topic_text = null;
    var topic_text_values = null;
    var topics_tweets = null;
    var topics_tweets_values = null;
    
    function color(k) {
	if (active_topics[k]) {
            $(".tweet").removeClass("bgcolor"+k);
	    $("#topic"+k).removeClass("topic_border");
            active_topics[k] = 0;
        } else {
	    var len = topics_tweets[k].length;
	    for (var i=0; i<len; i++) {
		$(topics_tweets[k][i]).addClass("bgcolor"+k);
	    }
	    $("#topic"+k).addClass("topic_border");
	    active_topics[k] = 1;
	}
    }
    
    function shuffle(array1, array2) {
	var tmp, current, top = array1.length;
	if(top) while(--top) {
	    current = Math.floor(Math.random() * (top + 1));
	    tmp = array1[current];
	    array1[current] = array1[top];
	    array1[top] = tmp;
	    
	    tmp = array2[current];
	    array2[current] = array2[top];
	    array2[top] = tmp;
	}
	return [array1, array2];
    }
    
    function performTopicModelling() {
	var worker = new Worker("{$site_root_path}assets/js/topicmodelling/topicise.js");
	worker.postMessage(statuses);
	worker.onmessage = function (event) {
	    var data = event.data;
	    num_topics = data.num;
	    topics = data.topics;
	    topic_text = data.topic_text;
	    topic_text_values = data.topic_text_values;
	    topics_tweets = data.topics_tweets;
	    topics_tweets_values = data.topics_tweets_values;
	    for (var i=0; i<num_topics; i++) {
		active_topics[i] = 0;
	    }
	    console.log(topics_tweets_values);
	    displayTopics(data.num, data.topic_text, data.topic_text_values, data.tweets);
	};
    }
    
    function displayTopics(num, topic_text, topic_text_values, tweets) {
	var table_topics = '<table class="height97">';
	for (k=0; k<num;) {
	    table_topics += "<tr>";
	    for (var j=0; j<2; j++) {
		var shuffled = shuffle(topic_text[k], topic_text_values[k]);
		topic_text[k] = shuffled[0];
		topic_text_values[k] = shuffled[1];
		table_topics += "<td>";
		table_topics += '<div id="topic'+k+'" class="tagCloud bgcolor'+k+'" onclick="color('+k+')"><ul class="tagList">';
		for (var i in topic_text[k]) {
		    if (topic_text_values[k][i] == 1) {
			table_topics += '<li>'+topic_text[k][i]+"</li>";
		    } else {
			var fontsize = (1+(topic_text_values[k][i]-1)/4);
			if (fontsize > 2) {
			    fontsize = 2;
			}
			fontsize += "em";
			table_topics += '<li style="font-size: '+fontsize+'">'+topic_text[k][i]+"</li>";
		    }
		}
		table_topics +="</ul></div></td>";
		k++;
	    }
	    table_topics +="</tr>";
	}
	table_topics += "</table>";
        $("#topiccloud").append(table_topics);
	var font_size = null;
	var word_height = null;
	var orig_table_size = 563;
	var table_size = $("#contentTable").height();
	var ratio = table_size/orig_table_size;
	$(".tagCloud").each(function () {
	    font_size = Math.round(Math.round(parseFloat($(this).css("font-size")))*ratio);
	    $(this).css("font-size", font_size);
	});
	$(".tagCloud li").each(function () {
	    var word_height = Math.round(Math.round(parseFloat($(this).height()))*ratio);
	    $(this).css("height", word_height);
	});
	$("#spinner").hide();
    }
    
    $(document).ready(function() {
        performTopicModelling();
    });
</script>
<div id="topiccloud"></div>
<br/>