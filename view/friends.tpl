<script type="text/javascript">{$connections}</script>
<script type="text/javascript">{$mutuals}</script>
<script type="text/javascript">{$type}</script>
<script type="text/javascript">
    var freq_all = new Object;
    $(document).ready(function(){
        /*if (typeof connection_analysis.mutuals == 'undefined') {
            connection_analysis.mutuals = mutuals;
            console.log(connection_analysis.mutuals);
        }*/
        $("#words_all").height($("#mainstage").height()*1.5);
        $("#words_all").width($("#mainstage").width()*0.8);
        if (type == "friend") {
            if (typeof connection_analysis.friends != 'undefined') {
                connections = connection_analysis.friends;
            } else {
                connection_analysis.friends = connections;
            }
        } else {
            if (typeof connection_analysis.followers != 'undefined') {
                connections = connection_analysis.followers;
            } else {
                connection_analysis.followers = connections;
            }
        }
    });
    function handleClick() {
        var user = connections[this.id];
        var pos = $(this).offset();
        var pos_left = pos.left;
        var pos_top = pos.top;
        var content =
            "<h3>About the User</h3>";
        if (user.relation == "friend") {
            $("#log").html("<p>Loading... Please Wait</p>");
            content = content +
                "<table><tr><td><img src=\"" + user.user.avatar+"\"/></td>" +
                "<td style=\"margin-left:5px\">" + user.user.username + "</td></tr></table>" +
                "<p>" + user.user.description + "</p>" +
                "<p>Following count: <strong>" + user.user.friends_count + "</strong></p>" +
                "<p>Followers count: <strong>" + user.user.followers_count + "</strong></p>" +
                "<p>Status count: <strong>" + user.user.statuses_count + "</strong></p>" 
                "<p>" + user.user.username + " has recently been talking about: </p>" ;
            $.ajax({
               type: "POST",
               url: "{$site_root_path}pages/wordanalysis.php",
               data: "type=connection&left="+pos_left+"&top="+pos_top+"&relation=friend&username="+this.id,
               success: function (msg) {
                    $("#log").html(content+msg);
               }
            });
            hideElement(this);
        } else {
            $("#log").html("<p>Loading... Please Wait</p>");
            content = content + 
                "<table><tr><td><img src=\"" + user.user.avatar+"\"/></td>" +
                "<td style=\"margin-left:5px\">" + user.user.username + "</td></tr></table>" +
                "<p>" + user.user.description + "</p>" +
                "<p>Following count: <strong>" + user.user.friends_count + "</strong></p>" +
                "<p>Followers count: <strong>" + user.user.followers_count + "</strong></p>" +
                "<p>Status count: <strong>" + user.user.statuses_count + "</strong></p>" +
                "<p>" + user.user.username + " has recently been talking about: </p>" ;
            $.ajax({
               type: "POST",
               url: "{$site_root_path}pages/wordanalysis.php",
               data: "type=connection&left="+pos_left+"&top="+pos_top+"&relation=mutual&username="+this.id,
               success: function (msg) {
                    $("#log").html(content+msg);
                    $(this).css("display", "none");
               }
            });
            hideElement(this);
        }
    }
    
    function hideElement(ele) {
        $(ele).hide();
    }
</script>
<script type="text/javascript" src="{$site_root_path}extlib/raphael/raphael-min.js"></script>
<script type="text/javascript" src="{$site_root_path}extlib/DraculaGraphLibrary/dracula_graffle.js"></script>
<script type="text/javascript" src="{$site_root_path}extlib/DraculaGraphLibrary/dracula_graph.js"></script>
<script type="text/javascript" src="{$site_root_path}assets/js/connections.js"></script>
{literal}
<style>
    #words_all {position: absolute; z-index: -100; list-style-type: none; float: left; font-size: 0.7em;}
    #words_all ul {display: inline; float:left;}
    .friend {color: green;}
    .mutual {color: blue;}
</style>
{/literal}
<div id="words_all">
</div>
<table>
    <tr>
        <td style="width:80%" valign="top">
            <div id="canvas"></div>
        </td>
        <td style="width:18%; overflow: hidden" valign="top">
            <div id="log" style="margin-top: 20px;">Click on a node to see more details</div>
        </td>
    </tr>
</table>