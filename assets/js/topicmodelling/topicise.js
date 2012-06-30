importScripts('stopwords.js');
importScripts('lda.js');

onmessage = function(e) {
    var sentences;
var tweets;
var i = 0;
    statuses = e.data;
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
	for(var i=0;i<sentences.length;i++) {
		documents[i] = new Array();
		if (sentences[i]=="") continue;
		//console.log(sentences[i]);
		var words = sentences[i].split(/[\s,\"]+/);
		//console.log(words);
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
	var K = 10;
	var alpha = .05;  // per-document distributions over topics
	var beta = .005;  // per-topic distributions over words

	lda.configure(documents,V,10000, 2000, 100, 10);
	lda.gibbs(K, alpha, beta);

	var theta = lda.getTheta();
	var phi = lda.getPhi();

	//topics
	var topTerms=15;
	var topicText = new Array();
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
			topicText[k] += (topicTerm+":"+prob+" ");
		}
	}
	
	text='<div class="spacer"> </div>';
	for (var m = 0; m < theta.length; m++) {
	    	var len = '<br/>';
		text+='<div class="tweet">';
		text+=tweets[m].text+'<br/>';
		for (var k = 0; k < theta[m].length; k++) {
			text+=('<div class="box bgcolor'+k+'" style="width:'+parseInt(""+(theta[m][k]*100))+'px" title="'+topicText[k]+'"></div>');
			len += '<span class="color' + k + '"> '+ parseInt(""+(theta[m][k]*100)) + ' </span>';
		}
		text+=len+'</div>';
	}
	
        postMessage({'num': K, 'topicText': topicText, 'tweets':text});
        self.close();
        };