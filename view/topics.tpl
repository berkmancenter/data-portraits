<link type="text/css" href="{$site_root_path}extlib/GibbsSamplerLDA/css/lda.css" rel="stylesheet" />
<script type="text/javascript" src="{$site_root_path}extlib/jQuery/jquery1.7.2.min.js"></script>
<script type="text/javascript">{$statuses}</script>
<script type="text/javascript">

    function performTopicModelling() {
	var worker = new Worker("{$site_root_path}assets/js/topicmodelling/topicise.js");
	worker.postMessage(statuses);
	worker.onmessage = function (event) {
	    var data = event.data;
	    console.log(data.num);
	    console.log(data.topicText);
	    displayTopics(data.num, data.topicText, data.tweets);
	};
    }
    
    function displayTopics(num, topicText, tweets) {
	$("#output").html(tweets);
	for (k=0; k<num; k++) {
	    $('#topiccloud').append('<div class="color'+k+'"> ' + topicText[k] + "<br/><br/> </div>");
	}
	$("#spinner").hide();
    }
    
    $(document).ready(function() {
        performTopicModelling();
    });
</script>
<div id="topiccloud"></div>
<br/>
<div id="output">
</div>