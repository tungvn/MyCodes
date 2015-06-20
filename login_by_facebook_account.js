
jQuery(document).ready(function($){
    // Click a button/link have class "facebook-login"
    $('.facebook-login').click(function(event) {
        event.preventDefault();
        check_facebook_login_state();
    });
});

/*
Facebook needed functions
*/

// Check status when connect to facebook with param response. This function is callback one of other
function status_change_callback(response) {
    // If status is 'connected', get user info to set cookie|save user session|register for user|...
    if(response.status === 'connected') {
        facebook_login_api_callback(); 
    }
    // If 'not_authorized', redirect browser to link. You can change app_id and the uri (1) to facebook redirect after user press 'OK' or 'Cancel'. (Now is current uri, 'wp_vars.current' is my global variable ^_^).
    // Param 'way=login_fb' need to the logic when facebook call your (1)
    else if(response.status === 'not_authorized') {
        var current_permalink = wp_vars.current;
        current_permalink += '?way=login_fb';
        var url_dialog = 'https://www.facebook.com/dialog/oauth?client_id=<app_id>&scope=public_profile,email&redirect_uri='+ current_permalink;
        window.location.replace(url_dialog);
    }
    // Else, we need user login their facebook acc :3
    else {
        FB.login(function(response) {
            // If they close the popup
            if( response.authResponse == null )
                return false;

            check_facebook_login_state();
        }, {scope: 'email,public_profile'});
    }
}

// check facebook login state
function check_facebook_login_state() {
    FB.getLoginStatus(function(response) {
        status_change_callback(response);
    });
}

// And do something with user information. Here I get some information like name, email, gender and something else for an ajax request.
function facebook_login_api_callback(){
    FB.api('/me', {scope: 'public_profile,email'}, function(response) {
        var scopeid = response.id;
        var email = response.email;
        var name = '';
        if( typeof response.name != 'undefined' )
            name = response.name;
        else
            name = response.first_name +' '+ response.middle_name +' '+ response.last_name;
        var gender = response.gender;
        if(gender == 'male')
            gender = 1;
        else if(gender == 'female')
            gender = 0;
        
        jQuery.ajax({
            url: wp_vars.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'frontend__login_register_ajax',
                login_way: 'f',
                social_id: scopeid,
                email: email,
                name: name,
                gender: gender
            }
        })
        .done(function(res) {
            if(res.result == 1){
                location.reload();
                return false;
            }
        });
    });
}

// Initialize and check user press 'OK' or 'Cancel' at facebook dialog
window.fbAsyncInit = function() {
    FB.init({
        appId      : '<app_id>', // Change it to your facebook app id
        cookie     : true, 
        xfbml      : true,
        version    : 'v2.3'
    });

    // Get param in current url and fetch its to an array
    var query = window.location.search.substring(1);
    var vars = query.split("&");
    var user_agree = false;

    // Check 'OK' or 'Cancel'
    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        // If user agree login with their facebook account, current url will havent bellow params
        if((pair[0] == 'error' && pair[1] == 'access_denied') || (pair[0] == 'error_reason' && pair[1] == 'user_denied') || (pair[0] == 'error_code' && pair[1] == '200')){
            user_agree = false;
        }
        // It only have this bellow param. See! This is our param (line 24)
        if(pair[0] == 'way' && pair[1] == 'login_fb'){
            user_agree = true;
        }
    }

    // And check last state
    if(user_agree){
        FB.getLoginStatus(function(response) {
            status_change_callback(response);
        });
    }
};
// End
// This is free to use ^__^
