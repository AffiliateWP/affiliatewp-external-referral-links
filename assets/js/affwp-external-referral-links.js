! function(factory) {
    "function" == typeof define && define.amd ? define(["jquery"], factory) : factory(jQuery)
}(function($) {
    function encode(s) {
        return config.raw ? s : encodeURIComponent(s)
    }

    function decode(s) {
        return config.raw ? s : decodeURIComponent(s)
    }

    function stringifyCookieValue(value) {
        return encode(config.json ? JSON.stringify(value) : String(value))
    }

    function parseCookieValue(s) {
        0 === s.indexOf('"') && (s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, "\\"));
        try {
            s = decodeURIComponent(s.replace(pluses, " "))
        } catch (e) {
            return
        }
        try {
            return config.json ? JSON.parse(s) : s
        } catch (e) {}
    }

    function read(s, converter) {
        var value = config.raw ? s : parseCookieValue(s);
        return $.isFunction(converter) ? converter(value) : value
    }
    var pluses = /\+/g,
        config = $.cookie = function(key, value, options) {
            if (void 0 !== value && !$.isFunction(value)) {
                if (options = $.extend({}, config.defaults, options), "number" == typeof options.expires) {
                    var days = options.expires,
                        t = options.expires = new Date;
                    t.setDate(t.getDate() + days)
                }
                return document.cookie = [encode(key), "=", stringifyCookieValue(value), options.expires ? "; expires=" + options.expires.toUTCString() : "", options.path ? "; path=" + options.path : "", options.domain ? "; domain=" + options.domain : "", options.secure ? "; secure" : ""].join("")
            }
            for (var result = key ? void 0 : {}, cookies = document.cookie ? document.cookie.split("; ") : [], i = 0, l = cookies.length; l > i; i++) {
                var parts = cookies[i].split("="),
                    name = decode(parts.shift()),
                    cookie = parts.join("=");
                if (key && key === name) {
                    result = read(cookie, value);
                    break
                }
                key || void 0 === (cookie = read(cookie)) || (result[name] = cookie)
            }
            return result
        };
    config.defaults = {}, $.removeCookie = function(key, options) {
        return void 0 !== $.cookie(key) ? ($.cookie(key, "", $.extend({}, options, {
            expires: -1
        })), !0) : !1
    }
}), jQuery(document).ready(function($) {
    function affiliatewp_arl_get_query_vars() {
        for (var hash, vars = [], hashes = window.location.href.slice(window.location.href.indexOf("?") + 1).split("&"), i = 0; i < hashes.length; i++) hash = hashes[i].split("="), vars.push(hash[0]), vars[hash[0]] = hash[1];
        return vars
    }

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?|&])" + key + "=.*?(&|#|$)", "i");
        if (uri.match(re)) return uri.replace(re, "$1" + key + "=" + value + "$2");
        var hash = "",
            separator = -1 !== uri.indexOf("?") ? "&" : "?";
        return -1 !== uri.indexOf("#") && (hash = uri.replace(/.*#/, "#"), uri = uri.replace(/#.*/, "")), uri + separator + key + "=" + value + hash
    }
    var referral_variable = affwp_erl_vars.referral_variable,
        cookie = $.cookie("affwp_erl_id"),
        ref = affiliatewp_arl_get_query_vars()[referral_variable];
    if (ref && !cookie) {
        var cookie_value = ref;
        // Set cookie expiration time
        var cookie_expiration = parseInt( affwp_erl_vars.cookie_expiration );
        $.cookie("affwp_erl_id", cookie_value, {
            expires: cookie_expiration,
            path: "/"
        })
    }
    if (cookie ? affiliate_id = cookie : affiliate_id = ref, affiliate_id) {
        var url = affwp_erl_vars.url,
            target_urls = $("a[href^='" + url + "']");
        $(target_urls).each(function() {
            current_url = $(this).attr("href"), current_url = current_url.replace(/\/?$/, "/"), $(this).attr("href", updateQueryStringParameter(current_url, referral_variable, affiliate_id))
        })
    }
});
