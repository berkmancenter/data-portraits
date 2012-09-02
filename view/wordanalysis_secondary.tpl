<script type="text/javascript">{$words}</script>
<ul id="words_secondary">
</ul>
<script type="text/javascript">
    $(document).ready(function() {
        var max = {$max};
        var avg = {$avg};
        var time_taken = {$time_taken};
        var count = {$count};
        var size;
        var max_words = new Array;
        var max_words_limit = 20;
        var pos_left = ({$pos_left}-270)+"px";
        var pos_top = ({$pos_top}-130)+"px";
        var relation = "{$relation}";
    {literal}
        for (var word in words) {
            if (words[word]['total'] < avg) {
                continue;
            }
            var ele = new Object;
            ele.word = word;
            ele.count = words[word].total;
            max_words.push(ele);
            /*if (typeof (max_words[words[word].total] != undefined)) {
                max_words[words[word].total] += ", " + word;
            } else {
                max_words[words[word].total] = word;
            }
            max_words_count ++;*/
        }
        var box = "<div class=\""+relation+"\" style=\"width: 70px; height:70px; position: absolute; left: "+pos_left+"; top: "+pos_top+"\">";
        max_words.sort(compare);
        final = new Array;
        for (var i=0; i<max_words_limit; i++) {
            $("#words_secondary").append("<li>"+max_words[i].word+"</li>");
            if (i < 8) {
                box += max_words[i].word+" ";
            }
        }
        $("#words_all").append(box);
    });
    function compare(a,b) {
        if (a.count < b.count)
            return 1;
        else if (a.count > b.count)
            return -1;
        else
            return 0;
    }
    {/literal}
</script>