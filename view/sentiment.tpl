<script type="text/javascript" src="{$site_root_path}extlib/tzineClock_modified/jquery.tzineClock.js"></script>
<link rel="stylesheet" type="text/css" href="{$site_root_path}extlib/tzineClock_modified/jquery.tzineClock.css" />
<script type="text/javascript">
    sentiment_analysis.count = {$count};
    sentiment_analysis.sentiment = {$sentiment};
    sentiment_analysis.max_vals = JSON.stringify({$max_json});
    sentiment_analysis.max_tweets = JSON.stringify({$max_tweets_json});
    sentiment_analysis.min_vals = JSON.stringify({$min_json});
    sentiment_analysis.min_tweets = JSON.stringify({$min_tweets_json});
    sentiment_analysis.pos_percent = {$pos_percent};
    $(document).ready(function() {
        // Second parameter is multiple colors, if false we use only green. Else we use
        // both blue and orange.
        $('#sentimentClock').tzineClock([{$sentiment}, true]);
        $('#posPercentClock').tzineClock([{$pos_percent}, false]);
        var table_height = $("#contentTable").height();
        var orig_table_height = 563;
        var diff = table_height - orig_table_height;
        var new_height = diff + $(".sentiment").height();
        $(".sentiment").height(new_height);
    });
</script>
<table class="width100">
    <tr>
        <td class="width50 bgcolor14">
            <table class="width100">
                <tr>
                    <td>
                        <div id="sentimentClock"></div>
                    </td>
                </tr>
                <tr>
                    <td class="color13 center">
                        <strong>Overall Sentiment</strong>
                    </td>
                </tr>
            </table>
        </td>
        <td class="width50 bgcolor14">
            <table class="width100">
                <tr>
                    <td>
                        <div id="posPercentClock"></div>
                    </td>
                </tr>
                <tr>
                    <td class="color13 center">
                        <strong>Positive Tweets to Total Tweets Ratio</strong>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr id="highslows">
        <td class="width50">
            <div class="sentiment bgcolor11">
                <h3 class="bgcolor12">Highs</h3>
                {for $counter=0 to $count-1}
                    <div>{$max_tweets[$counter]}<br/><br/></div>
                {/for}
            </div>
        </td>
        <td class="width50">
            <div class="sentiment bgcolor6">
                <h3 class="bgcolor10 color13">Lows</h3>
                {for $counter=0 to $count-1}
                    <div>{$min_tweets[$counter]}<br/><br/></div>
                {/for}
            </div>
        </td>
    </tr>
</table>
<br/><br/>