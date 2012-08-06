<script type="text/javascript">{$connections}</script>
<script type="text/javascript">
    $(document).ready(function(){
        if (typeof connection_analysis.data != 'undefined') {
            connections = connection_analysis.data;
        }
        console.log(connections);
        connection_analysis.data = connections;
    });
    function handleClick() {
        var user = connections[this.id];
        var content =
            "<h3>About the User</h3>";
        if (user.relation == "friend") {
            $("#log").html("<p>Loading... Please Wait</p>");
            content = content +
                "<table><tr><td><img src=\"" + user.user.avatar+"\"/></td>" +
                "<td style=\"margin-left:5px\">" + user.user.username + "</td></tr></table>" +
                "<p>" + user.user.description + "</p>" +
                "<p>" + user.user.username + " mostly talks about: </p>" ;
            $.ajax({
               type: "POST",
               url: "{$site_root_path}pages/wordanalysis.php",
               data: "type=connection&username="+this.id,
               success: function (msg) {
                    $("#log").html(content+msg);
               }
            });
        } else if (user.relation == "follower") {
            content = content +
                "<table><tr><td><img src=\"" + user.user.avatar+"\"/></td>" +
                "<td style=\"margin-left:5px\">" + user.user.username + "</td></tr></table>" +
                "<p>" + user.user.description + "</p>" +
                "<p>Following count: <strong>" + user.user.friends_count + "</strong></p>";
            $("#log").html(content);
        } else {
            $("#log").html("<p>Loading... Please Wait</p>");
            content = content + 
                "<table><tr><td><img src=\"" + user.user.avatar+"\"/></td>" +
                "<td style=\"margin-left:5px\">" + user.user.username + "</td></tr></table>" +
                "<p>" + user.user.description + "</p>" +
                "<p>Following count: <strong>" + user.user.friends_count + "</strong></p>" + 
                "<p>" + user.user.username + " mostly talks about: </p>" ;
            $.ajax({
               type: "POST",
               url: "{$site_root_path}pages/wordanalysis.php",
               data: "type=connection&username="+this.id,
               success: function (msg) {
                    $("#log").html(content+msg);
               }
            });
        }
    }
</script>
<script type="text/javascript" src="{$site_root_path}extlib/raphael/raphael-min.js"></script>
<script type="text/javascript" src="{$site_root_path}extlib/DraculaGraphLibrary/dracula_graffle.js"></script>
<script type="text/javascript" src="{$site_root_path}extlib/DraculaGraphLibrary/dracula_graph.js"></script>
<script type="text/javascript" src="{$site_root_path}assets/js/connections.js"></script>

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