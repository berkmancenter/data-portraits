importScripts('stopwords.js');
importScripts('lda.js');

onmessage = function(e) {
    var sentences;
    var tweets;
    var i = 0;
    
    var data = e.data
    var statuses = data.status;
    var K = data.num;
    
    sentences = new Array();
    tweets=new Array();
    for (var i in statuses) {
	sentences.push(statuses[i].text_processed);
	tweets.push(statuses[i]);
    }
    var documents = new Array();
    var f = {};
    var vocab=new Array();

    // Build vocab array, frequency array and documents array
    // NOTE: documents is another name for sentences
    for(var i=0; i<sentences.length; i++) {
	documents[i] = new Array();
	if (sentences[i]=="") continue;
	var words = sentences[i].split(/[\s,\"]+/);
	if(!words) continue;
	for(var wc=0;wc<words.length;wc++) {
	    var w=words[wc].toLowerCase().replace(/[^a-z\'A-Z0-9 ]+/g, '');
	    if (w=="" || w.length==1 || stopwords[w] || w.indexOf("http")==0) continue;
	    if (f[w]) { 
		f[w]=f[w]+1;			
	    } 
	    else if(w) { 
		f[w]=1; 
		vocab.push(w); 
	    };		
	    documents[i].push(vocab.indexOf(w));
	}
    }

    var V = vocab.length;
    var M = documents.length;
    var alpha = .05;  // per-document distributions over topics
    var beta = .005;  // per-topic distributions over words

    lda.configure(documents,V,10000, 2000, 100, 10);
    lda.gibbs(K, alpha, beta);

    var theta = lda.getTheta();
    var phi = lda.getPhi();

    //topics
    var topicText = new Array();

    var topTerms=15;
    var topics = new Array();
    var topic_text = new Array();
    var topic_text_values = new Array();
    var topics_tweets = new Array();
    var topics_tweets_values = new Array();
	
    for (var i = 0; i < K; i++) {
	topics[i] = 0;
	topic_text[i] = new Array();
	topic_text_values[i] = new Array();
	topics_tweets[i] = new Array();
	topics_tweets_values[i] = new Array();
    }
	
    for (var k = 0; k < phi.length; k++) {
	var things = new Array();
	for (var w = 0; w < phi[k].length; w++) {
	    things.push(""+phi[k][w].toPrecision(2)+"_"+vocab[w]);
	}
	things.sort().reverse();
	if(topTerms>vocab.length) topTerms=vocab.length;
	var min = parseInt(things[topTerms-1].split("_")[0]*10000);
	topicText[k]='';
	for (var t = 0; t < topTerms; t++) {
	    var topicTerm=things[t].split("_")[1];
	    var prob=parseInt((things[t].split("_")[0]*10000)/min);
	    topic_text[k].push(topicTerm);
	    topic_text_values[k].push(prob);
	    topicText[k] += (topicTerm + " ");
	}
    }
	
    var size = 0;
    // parse through each tweet
    for (var m = 0; m < theta.length; m++) {
	for (var k = 0; k < theta[m].length; k++) {
	    size = parseInt(theta[m][k]*100);
	    topics[k] += size;
	    if (size >= 15) {
		topics_tweets[k].push("#tweet"+m);
		topics_tweets_values[k].push(size);
	    }
	}
    }
	
    postMessage({'num': K, 'topics':topics, 'topic_text':topic_text,
		'topic_text_values':topic_text_values, 'topics_tweets':topics_tweets,
		'topics_tweets_values':topics_tweets_values
		});
    self.close();
};