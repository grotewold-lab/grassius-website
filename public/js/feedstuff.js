function drawFeed(feedName) {
  var newsUrl = "http://128.146.132.152/Grassius/news.xml";
  var commUrl="http://128.146.132.152/Grassius/community.xml";

  if(feedName === 'news') {
    return drawFeedBase(newsUrl);
  } else if(feedName === 'community') {
    return drawFeedBase(commUrl);
  } else if(feedName === 'all') {
    var urlList = new Array();
    urlList.push(newsUrl);
    urlList.push(commUrl);
    return drawFeedSortingBase(urlList);
  }
}

function drawFeedSortingBase(urlList) {
  return function() {
    for(k=0;k<urlList.length;k++) {
      var news=new google.feeds.Feed(urlList[k]);
      news.setNumEntries(100);
      news.load(function(result) {
        if(!result.error) {
          for(var i=0;i<result.feed.entries.length; i++) {
            var entry=result.feed.entries[i];
            entryDate = new Date(entry.publishedDate);
            var div=$(document.createElement("div"));
            div.html("<a href='"+entry.link+"' style='font-weight:bolder'>"+entry.title+"</a>"+entry.content+entryDate.toString());
            div.data('date',entryDate.getTime()).addClass("newsItem");
            var children=$('#news').children();
            if(children.length == 0) {
              $('#news').append(div);
            } else {
              var j=0;
              while(j<children.length) {
                if(div.data('date') > $(children[j]).data('date')) {
                  $(children[j]).before(div);
                  break;
                } else {
                  j++;
                  if(j == children.length) {
                    $('#news').append(div);
                    break;
                  }
                }
              }
            }
          }
          $('#news > div p').css("border-bottom","none").css('padding-bottom','0px');
        }
      });
    }
  }
}
  
function drawFeedBase(url) {
  return function() {
    var feed=new google.feeds.Feed(url);
    feed.load(function(result) {
      if(!result.error) {
        for(var i=0; i<result.feed.entries.length; i++) {
          var entry=result.feed.entries[i];
          var div=$(document.createElement("div"));
          div.css("border-style","dotted").css("border-width","1px").css("border-color","grey").css('border-radius','5px').css('border-color','#535523');
          div.css('margin-bottom','5px').css('padding','5px');
          div.html("<a href=\""+entry.link+"\" style=\"font-weight:bolder\">"+entry.title+"</a>"+entry.content+entry.publishedDate);
          $('#news').append(div);
        }
        $('#news > div p').css("border-bottom","none").css('padding-bottom','0px');
      }
    });
  }
}
