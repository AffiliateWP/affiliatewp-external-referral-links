jQuery(document).ready(function($) {

	// eg "ref"
	var referral_variable = affwp_erl_vars.referral_variable;

	// get the cookie value
	var cookie = $.cookie( 'affwp_erl_id' );

	// get the value of the referral variable from the query string
	var ref = affiliatewp_arl_get_query_vars()[referral_variable];

	// if ref exists but cookie doesn't, set cookie with value of ref
	if ( ref && ! cookie ) {
		var cookie_value = ref;

		// Set the cookie and expire it after 24 hours
		$.cookie( 'affwp_erl_id', cookie_value, { expires: 2, path: '/' } );
	}
	
	// split up the query string and return the parts
	function affiliatewp_arl_get_query_vars() {
		var vars = [], hash;
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for (var i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	}

	// the affiliate ID will usually be the value of the cookie, but on first page load we'll grab it from the query string
	if ( cookie ) {
		affiliate_id = cookie;
	} else {
		affiliate_id = ref;
	}

	function updateQueryStringParameter(uri, key, value) {
	  var re = new RegExp("([?|&])" + key + "=.*?(&|#|$)", "i");
	  if (uri.match(re)) {
	    return uri.replace(re, '$1' + key + "=" + value + '$2');
	  } else {
	    var hash =  '';
	    var separator = uri.indexOf('?') !== -1 ? "&" : "?";    
	    if( uri.indexOf('#') !== -1 ){
	        hash = uri.replace(/.*#/, '#');
	        uri = uri.replace(/#.*/, '');
	    }
	    return uri + separator + key + "=" + value + hash;
	  }
	}

	if ( affiliate_id ) {
		var url = affwp_erl_vars.url;

		// get all the targeted URLs on the page that start with the specific URL
		var target_urls = $("a[href^='" + url + "']");

		// modify each target URL on the page
		$(target_urls).each( function() {
			
			// get the current href of the link
			current_url = $(this).attr('href');

			// append a slash to the URL if it doesn't exist
			current_url = current_url.replace(/\/?$/, '/');

			$(this).attr('href', updateQueryStringParameter( current_url, referral_variable, affiliate_id ) );
			
		});

	}
	
});