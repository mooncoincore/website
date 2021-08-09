<?php 

 if( empty($all_news_data) || $all_news_data==="" ) {

	if(empty($rss_url_second )){
		$all_news_data=ccpw_rss_feed($rss_desc_length,$rss_url,$rss_no_of_news);
		}	
	else if(!empty($rss_url_second )){
		if( $rss_no_of_news == 1 ){
			$all_news_data=ccpw_rss_feed($rss_desc_length,$rss_url, 1);
		}else{
			$all_news_data=ccpw_rss_feed($rss_desc_length,$rss_url,$rss_no_of_news/2);
			$second_source=ccpw_rss_feed($rss_desc_length,$rss_url_second,($rss_no_of_news/2));
			if(is_array($second_source) && is_array($all_news_data)){
				$all_news_data=array_merge($all_news_data,$second_source);
			}
		}
	 }	
		set_transient('ccpw-new-feed-data-'.$post_id,$all_news_data, 2* HOUR_IN_SECONDS);
 	 }

	
	$rss_html='';
	// sorted news by date time
	if($all_news_data){
	 uasort($all_news_data,'cccpw_date_compare');
	}
	if( is_array($all_news_data)) {

		foreach( $all_news_data as $news ) 
		{
			if($rss_style=="ticker-rss"){
				$rss_html.='<li class="ccpw-news">
			<a target="__blank" rel="nofollow" href="'.$news['link'].'">'.$news['title'].'</a></li>';
			}else{
			$rss_html.='<li class="ccpw-news">
			<h2 class=ccpw-news-link><a target="__blank" rel="nofollow" href="'.$news['link'].'">'.$news['title'].'</a></h2>';
			$rss_html.='<div class="feed-publish-date"><span>'.$news['channel'].'</span> - '.$news['date_posted'].'</div>';
			if($rss_style=="list-rss"){
				if(isset($news['image-url'])){	
					$rss_html.='<div class="rss-desc"><a target="__blank" rel="nofollow" href="'.$news['link'].'"><img src="'.$news['image-url'].'"></a><p class="news-desc">'.$news['description'].'';
					$rss_html.='<span class="more-text"><a target="__blank"  href="'.$news['link'].'">'.$rss_excerpt_text.'</a></span></p></div></li>';
				}
				else if(isset($news['first-image'])&& !empty($news['first-image'])){					
					$rss_html.='<div class="rss-desc"><a target="__blank" rel="nofollow" href="'.$news['link'].'"><img src="'.$news['first-image'].'"></a><p class="news-desc">'.$news['description'].'';
					$rss_html.='<span class="more-text"><a target="__blank" rel="nofollow"  href="'.$news['link'].'">'.$rss_excerpt_text.'</a></span></p></div></li>';
				}
				else {
				$rss_html.='<div class="rss-desc full"><p class="news-desc">'.$news['description'].'';
				$rss_html.='<span class="more-text"><a target="__blank" rel="nofollow" href="'.$news['link'].'">'.$rss_excerpt_text.'</a></span></p></div></li>';	
				}
				}
			}

		}
		}else{
			 return _e('There is something wrong with Feed URL','ccpw');
		}	
		
		// ticker layout 
	   if($rss_style=="ticker-rss"){
	   	$container_cls='';
		   if($rss_ticker_position=="rss-footer"||$rss_ticker_position=="rss-header"){

			 		if($rss_ticker_position=="rss-footer"){
						$container_cls='ccpw-footer-ticker-fixedbar ccpw-news-ticker';
			 		}else{
			 			$container_cls='ccpw-header-ticker-fixedbar ccpw-news-ticker';
					}
			 		
			 	}else{
			 		$container_cls='ccpw-news-ticker';
			 	}

		
		$output .= '<div style="display:none;" class=" ccpw-ticker-cont '.$container_cls.'">
			<div class="tickercontainer">
			<ul id="'.$id.'">';
			$output.=$rss_html;
			$output .= '</ul></div></div>'; 
       
	   }
       else{	
       	//wrapper for news list widget 
	   $output .= '<div class="ccpw-container-rss-view ccpw-ticker-rss-view"><ul  id="'.$id.'">';
	   $output.=$rss_html;
	   $output .= '</ul></div>';
	  }

	  return $output;