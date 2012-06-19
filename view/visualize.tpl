{include file="_header.tpl"}
<script type="text/javascript">{$user_data}</script>
<script type="text/javascript">{$words}</script>
<script type="text/javascript" src="{$site_root_path}extlib/jQCloud/jqcloud-1.0.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$site_root_path}extlib/jQCloud/jqcloud.css" />

<div class="bubbleInfo">
    <div><img id="avatar" class="trigger" src=""/></div>
    <div class="popup" id="dpop">
        <div>
            <table id="popup-contents">
                <tr>
                    <td valign="top"><img id="avatar_popup" src=""/></td>
                    <td id="popup_data" valign="top">
                        <h4><p id="username_popup"></p></h4>
                        <p id="location_popup" />
                        <p id="description_popup" />
                        <p>Status Count: <span id="status_popup" class="stats_popup"></span></p>
                    </td>
                </tr>
            </table>
        </div>
        <div id="tailShadow"></div>
        <div id="tail1"></div>
        <div id="tail2"></div>
    </div>
</div><br/>
<div id="wordcloud">
</div>
<script type="text/javascript">
    var max = {$max};
    var avg = {$avg};
    var size;
{literal}
    var text;
    var color;
    var span;
    var ele;
    var word_list = [];
    for (var word in words) {
        if (words[word]['total'] < avg) {
            continue;
        }
        color = Math.floor((words[word]['url']*100)/words[word]['total']);
        size = Math.floor((words[word]['total']*100)/max);
        var item = new Array();
        item['text'] = word;
        item['weight'] = words[word]['total'];
        var html = new Array();
        if (color <= 10) { html['style']= "color: #68a1ff"; }
        else if (color <= 20) { html['style']= "color: #4088ff"; }
        else if (color <= 30) { html['style']= "color: #2477ff"; }
        else if (color <= 40) { html['style']= "color: #0060ff"; }
        else if (color <= 50) { html['style']= "color: #0057e6"; }
        else if (color <= 60) { html['style']= "color: #004ece"; }
        else if (color <= 70) { html['style']= "color: #0044b5"; }
        else if (color <= 80) { html['style']= "color: #003996"; }
        else if (color <= 90) { html['style']= "color: #002c75"; }
        else { html['style']= "color: #002562;"; }
        item['html'] = html;
        word_list.push(item);
    }
    $(document).ready(function() {
        $("#wordcloud").jQCloud(word_list);
    });
</script>
<style type="text/css">
      #wordcloud {
        margin: 30px auto;
        width: 800px;
        height: 471px;
        border: none;
      }
      #wordcloud span.w10, #wordcloud span.w9, #wordcloud span.w8, #wordcloud span.w7 {
        text-shadow: 0px 1px 1px #ccc;
      }
      #wordcloud span.w3, #wordcloud span.w2, #wordcloud span.w1 {
        text-shadow: 0px 1px 1px #fff;
      }
</style>
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
    })
</script>
{/literal}
<link rel="stylesheet" type="text/css" href="{$site_root_path}assets/css/popup.css" />
<script type="text/javascript" src="{$site_root_path}assets/js/popup.js"></script>
{include file="_footer.tpl"}