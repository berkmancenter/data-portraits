<script type="text/javascript">{$words}</script>
<script type="text/javascript" src="{$site_root_path}extlib/jQCloud/jqcloud-1.0.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="{$site_root_path}extlib/jQCloud/jqcloud.css" />

<!-- <div class="bubbleInfo">
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
    -->
<script type="text/javascript">
    $(document).ready(function() {
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
        $("#mainstage").jQCloud(word_list);
    });
</script>
<style type="text/css">
    #mainstage {
        margin: 10px auto;
        width: 750px;
        height: 80%;
        float: right;
        
    }
    #mainstage span.w10, #mainstage span.w9, #mainstage span.w8, #mainstage span.w7 {
        text-shadow: 0px 1px 1px #ccc;
    }
    #mainstage span.w3, #mainstage span.w2, #mainstage span.w1 {
        text-shadow: 0px 1px 1px #fff;
    }
</style>
{/literal}
<link rel="stylesheet" type="text/css" href="{$site_root_path}assets/css/popup.css" />
<script type="text/javascript" src="{$site_root_path}assets/js/popup.js"></script>