<script type="text/javascript">{$num}</script>
<script type="text/javascript">{$topic_text}</script>
<script type="text/javascript">{$topic_text_values}</script>
<script type="text/javascript">{$tweets}</script>
<script type="text/javascript">{$topic_tweets}</script>
<script type="text/javascript">
    console.log(num);
    console.log(tweets);
    console.log(topic_text);
    console.log(topic_text_values);
    
    var active_topics = new Array();
    var num_topics = num;
    var topic_text = topic_text;
    var topic_text_values = topic_text_values;
    
    function color(k) {
	if (active_topics[k]) {
            $(".tweet").removeClass("bgcolor"+k);
	    $("#topic"+k).removeClass("topic_border");
            active_topics[k] = 0;
        } else {
	    var len = tweets[k].length;
	    for (var i=0; i<len; i++) {
		$("#tweet"+tweets[k][i]).addClass("bgcolor"+k);
	    }
	    $("#topic"+k).addClass("topic_border");
	    active_topics[k] = 1;
	}
        console.log("EKANSH");
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
    
    function displayTopics(num, topic_text, topic_text_values, tweets) {
	var table_topics = '<table class="height97">';
	for (k=0; k<num;) {
	    table_topics += "<tr>";
	    for (var j=0; j<2 && k<num ; j++, k++) {
		var shuffled = shuffle(topic_text[k], topic_text_values[k]);
		topic_text[k] = shuffled[0];
		topic_text_values[k] = shuffled[1];
		table_topics += '<td class="width50">'+getTitle(k);
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
    
    function getTitle(id) {
        switch(id) {
            case 0: return "Direct Replies";
            case 1: return "Interactions";
            case 2: return "With URLs";
            case 3: return "With Hashtags";
            case 4: return "Personal";
            case 5: return "Others";
        }
    }
    
    $(document).ready(function() {
	for (var i=0; i<num_topics; i++) {
	    active_topics[i] = 0;
	}
	displayTopics(num, topic_text, topic_text_values, tweets);
    });
</script>

<div id="topiccloud"></div>
<br/>