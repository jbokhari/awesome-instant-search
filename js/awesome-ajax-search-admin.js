jQuery(document).ready(function($) {
  // body...
var opt = Awesome_AJAX_Search_Options,
	prefix = opt.prefix;

	var $el = $('#' + prefix + "theme");


	var quick_themes = function(el){
			ts = new Array(),
			ts["input"] = $( "#" + prefix + "input"),
			ts["content"] = $( "#" + prefix + "content"),
			ts["results"] = $( "#" + prefix + "results"),
			ts["alsohide"] = $( "#" + prefix + "alsohide"),
			ts["before"] = $( "#" + prefix + "before");

		el.on('change', function(){
			var $this = $(this),
				val = $this.val();
			if (val == '2011'){
				ts["input"].val('input[name=s]');
				ts["content"].val('#content');
				ts["results"].val('#content article.hentry');
				ts["alsohide"].val('');
				ts["before"].val('<header class="page-header"><h1 class="page-title">Search Results for: <span> %%SEARCH_TERM%%</span></h1></header>');
			}
			if (val == '2012'){
				ts["input"].val('input[name=s]');
				ts["content"].val('#content');
				ts["results"].val('#content article.hentry');
				ts["alsohide"].val('#content header, #comments, #nav-below');

				ts["before"].val('<header class="page-header"><h1 class="page-title">Search Results for: <span>%%SEARCH_TERM%%</span></h1></header>');
			}
			if (val == '2013'){
				ts["input"].val('input[name=s]');
				ts["content"].val('#content ');
				ts["results"].val('#content article.hentry');
				ts["alsohide"].val('#comments, #content header');
				ts["before"].val("<header class='page-header'><h1 class='page-title'>Instant Search Results for: %%SEARCH_TERM%%</h1></header>");
			} else{
				ts.each(function(){
					$(this).val('');
				});
			}
		});
	}

	quick_themes($el);
});