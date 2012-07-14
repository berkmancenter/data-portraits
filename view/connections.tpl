<script type="text/javascript">{$friends}</script>
<script type="text/javascript">{$followers}</script>
<script type="text/javascript">{$mutuals}</script>
<table border="1">
    <tr>
        <td valign="top" style="width: 33%"><ul id="friends"></ul></td>
        <td valign="top" style="width: 33%"><ul id="mutuals"></ul></td>
        <td valign="top" style="width: 33%"><ul id="followers"></ul></td>
    </tr>
</table>
<script type="text/javascript">
    $(document).ready(function(){
        connection_analysis.friends = friends;
        connection_analysis.followers = followers;
        connection_analysis.mutual = mutuals;
        console.log(connection_analysis);
        var friend = null;
        for (var i in friends) {
            friend = '<li><img width="48" src="'+friends[i].avatar+'"/>'+friends[i].username+"</li>";
            $("#friends").append(friend);
        }
        var mutual = null;
        for (var i in mutuals) {
            console.log(i);
            mutual = '<li><img width="48" src="'+mutuals[i].avatar+'"/>'+mutuals[i].username+"</li>";
            $("#mutuals").append(mutual);
        }
        var follower = null;
        for (var i in followers) {
            console.log(i);
            follower = '<li><img width="48" src="'+followers[i].avatar+'"/>'+followers[i].username+"</li>";
            $("#followers").append(follower);
        }
    })
</script>