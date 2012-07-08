{include file="_header.tpl"}
<script type="text/javascript">{$user_data}</script>
<script type="text/javascript">{$statuses}</script>

<script type="text/javascript">
    var json_statuses = null;
    
    function removeTopics() {
        for (var i=0; i<10; i++) {
            $(".tweet").removeClass("bgcolor"+i);
        }
    }
    
    function sentiment() {
        $("#spinner").show();
        removeTopics();
        $.ajax({
            type: "POST",
            url: "{$site_root_path}pages/sentiment.php",
            data: "statuses="+json_statuses,
            success: function(msg){
                $("#mainstage").html(msg);
                $("#spinner").hide();
            }
        });
    }
    
    function wordAnalysis() {
        $("#spinner").show();
        removeTopics();
        $.ajax({
            type: "POST",
            url: "{$site_root_path}pages/wordanalysis.php",
            data: "statuses="+json_statuses,
            success: function(msg){
                $("#mainstage").html(msg);
                $("#spinner").hide();
            }
        });
    }
    
    function topicModelling() {
        $("#spinner").show();
        removeTopics();
        $.ajax({
            type: "POST",
            url: "{$site_root_path}pages/topics.php",
            data: "statuses="+json_statuses,
            success: function(msg){
                $("#mainstage").html(msg);
            }
        });
    }
    
    $(document).ready(function() {
        json_statuses = JSON.stringify(statuses);
    });
</script>

<table id="contentTable">
    <tr>
        <td rowspan="2" class="width25">
            <div id="tweets">
                <ul id="timeline"></ul>
            </div>
        </td>
        <td class="height3 margintopM10">
            <div class="right">
                <ul class="mainMenu">
                    <li class="right"><a href="#" class="grey-button pcb"><span>Analyse new User</span></a></li>
                    <li class="right"><a href="#" class="grey-button pcb" onclick="sentiment()"><span>Sentiment Analysis</a></span></li>
                    <li class="right"><a href="#" class="grey-button pcb" onclick="topicModelling()"><span>Topic Modelling</a></span></li>
                    <li class="right"><a href="#" class="grey-button pcb" onclick="wordAnalysis()"><span>Word Analysis</a></span></li>
                </ul>
            </div>
        </td>
    </tr>
    <tr valign="center">
        <td class="center">
            <div id="mainstage" align="center" style="border: 1px solid #000; margin: auto;">
                {include file="wordanalysis.tpl"}
            </div>
        </td>
    </tr>
</table>
<div id="spinner"></div>

{literal}
<script type="text/javascript">
    $(document).ready(function() {
        var dp = user['avatar'];
        var username = user['username'];
        var full_name = user['full_name'];
        var location = user['location'];
        var description = user['description'];
        //var followers_count = user['followers_count'];
        //var friends_count = user['friends_count'];
        var status_count = user['statuses_count'];
        
        var status;
        var id;
        for (var i in statuses) {
            date = statuses[i].created.split(" ");
            date = date[1] + ' ' + date[2] + ' ' + date[5];
            id = "tweet"+i;
            status = '<li class="tweet" id="'+id+'">' +
                        '<table>' +
                            '<tr>' +
                                '<td valign="top">' +
                                    '<img class="status_image" src="' + dp +
                                    '" width="32"/>' +
                                '</td>' +
                                '<td valign="top">' +
                                    statuses[i].text + '<br/>' +
                                    '<span class="status_date">' + date +
                                    '</span>' +
                                '</td>' +
                            '</tr>' +
                        '</table>' +
                     '</li><br/>';
            $('#timeline').append(status);
        }
        $('#avatar').attr('src', dp);
        $('#avatar_popup').attr('src', dp);
        $('#username_popup').text(username + ' (' + full_name + ')');
        if (location) {
            $('#location_popup').text('Location: ' + location);
        }
        if (description) {
            $('#description_popup').text(description);
        }
        //$('#followers_popup').text(followers_count);
        //$('#friends_popup').text(friends_count);
        $('#status_popup').text(status_count);
    });
    </script>
{/literal}
{include file="_footer.tpl"}