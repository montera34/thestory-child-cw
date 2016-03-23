jQuery(document).ready(function($){
	$(".nohttps a").each(function() {
		url = $(this).attr("href").split("//");
		$(this).attr("href","http://"+url[1])
	});
});
