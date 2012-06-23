{include file="_header.tpl"}
<script type="text/javascript">{$user_data}</script>
<script type="text/javascript">{$statuses}</script>

<script type="text/javascript">
    var json_statuses = null;
    
    function sentiment() {
        $("#spinner").show();
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

<div class="right">
    <ul class="mainMenu">
        <li class="right"><a href="#">Analyse new User</a></li>
        <li class="right"><a href="#" onclick="sentiment()">Sentiment Analysis</a> - </li>
        <li class="right"><a href="#" onclick="topicModelling()">Topic Modelling</a> - </li>
        <li class="right"><a href="#" onclick="wordAnalysis()">Word Analysis</a> - </li>
    </ul>
</div>

<div id="tweets">
    <ul id="timeline"></ul>
</div>
<div id="spinner"></div>
<div id="mainstage">
    {include file="wordanalysis.tpl"}
</div>

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
        for (var i in statuses) {
            date = statuses[i].created.split(" ");
            date = date[1] + ' ' + date[2] + ' ' + date[5];
            status = '<li class="tweet">' +
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
                        '</table><hr/>' +
                     '</li>';
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