;(function($) {
	$(function(){
		var flashvars = {};
		flashvars.video_url = "$VideoURL";
		flashvars.autoplay = $Autoplay;
		var params = {};
		params.scale = "noscale";
		params.salign = "tl";
		params.allowfullscreen = "true";
		params.menu = "false";
		var attributes = {};
		swfobject.embedSWF('$Module/flash/cxPlayer.swf', 'videoholder_$ID', '$Width', '$Height', '9.0.115', 'swf/expressinstall.swf', flashvars, params, attributes);
	});
})(jQuery);
