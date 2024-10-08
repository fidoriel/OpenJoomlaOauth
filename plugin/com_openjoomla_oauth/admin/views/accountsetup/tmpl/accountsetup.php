<?php

// Copyright (C) 2005 - 2015 Open Source Matters
// Copyright (C) 2015 miniOrange
// Copyright (C) 2024 fidoriel

// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>

// @license		http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE

defined('_JEXEC') or die('Restricted Access');
JHtml::_('jquery.framework');
JHtml::_('script', JURI::base() . 'components/com_openjoomla_oauth/assets/js/bootstrap.js');
JHtml::_('stylesheet', JURI::base() . 'components/com_openjoomla_oauth/assets/css/openjoomla_oauth.css');
JHtml::_('stylesheet', JURI::base() . 'components/com_openjoomla_oauth/assets/css/openjoomla_boot.css');
JHtml::_('script', JURI::base() . 'components/com_openjoomla_oauth/assets/js/myscript.js');
JHtml::_('stylesheet', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
?>  
<?php
if (MoOAuthUtility::is_curl_installed() == 0) { ?>
    <p style="color:red;">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL
            extension</a> is not installed or disabled) Please go to Troubleshooting for steps to enable curl.</p>
    <?php
}
$active_tab = JFactory::getApplication()->input->get->getArray();
$oauth_active_tab = isset($active_tab['tab-panel']) && !empty($active_tab['tab-panel']) ? $active_tab['tab-panel'] : 'configuration';
global $license_tab_link;
$license_tab_link="index.php?option=com_openjoomla_oauth&view=accountsetup&tab-panel=license";
$current_user = JFactory::getUser();
if (!JPluginHelper::isEnabled('system', 'openjoomlaoauth')) {
    ?>
    <div id="system-message-container">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <div class="alert alert-error">
            <h4 class="alert-heading">Warning!</h4>
            <div class="alert-message">
                <h4>
                    This component requires System Plugin to be activated. Please activate the following plugin
                    to proceed further: System - OpenJoomlaOAuth Client
                </h4>
                <h4>Steps to activate the plugins:</h4>
                <ul>
                    <li>In the top menu, click on Extensions and select Plugins.</li>
                    <li>Search for miniOrange in the search box and press 'Search' to display the plugins.</li>
                    <li>Now enable the System plugin.</li>
                </ul>
            </div>
        </div>
    </div>
<?php } ?>
<style>
    .close {
        color: red;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }   
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
 
<script>
    function MyClose(){
        jQuery(".TC_modal").css("display","none");
    }
    function show_TC_modal(){
        jQuery(".TC_modal").css("display","block");
    }
</script>

<div class="oj_boot_container-fluid p-0">
    <div class="oj_boot_row p-0 oj_boot_mx-2" style="background-color:white;">
        <div id="oj_oauth_nav_parent" class="oj_boot_col-sm-12 p-0 m-0" style="display:flex;">
            <a id="configtab" class="p-3  oj_nav-tab oj_nav_tab_<?php echo $oauth_active_tab == 'configuration' ? 'active' : ''; ?>" href="#configuration" onclick="add_css_tab('#configtab');" data-toggle="tab">
                <?php echo JText::_('COM_OPENJOOMLA_OAUTH_TAB1_CONFIGURE_OAUTH');?>
            </a>
            <a id="attributetab" class="p-3 oj_nav-tab oj_nav_tab_<?php echo $oauth_active_tab == 'attrrolemapping' ? 'active' : ''; ?>" href="#attrrolemapping" onclick="add_css_tab('#attributetab');" data-toggle="tab">
                User Attribute Mapping
            </a>
        </div>
    </div>
</div>
<div class="tab-content oj_boot_mx-2 oj_boot_my-2 oj_container" id="myTabContent">
        <div id="configuration" class="tab-pane <?php echo $oauth_active_tab == 'configuration' ? 'active' : ''; ?>">
            <div class="oj_boot_row">
                <div class="oj_boot_col-sm-12">
                    <?php
                        moOAuthConfiguration();
?>
                </div>
            </div>
        </div>
        <div id="attrrolemapping" class="tab-pane <?php echo $oauth_active_tab == 'attrrolemapping' ? 'active' : ''; ?>">
            <div class="oj_boot_row">
                <div class="oj_boot_col-sm-12">
                    <?php attributerole(); ?>
                </div>
            </div>
        </div>
<?php
function getAppJson()
{
    return '
    {
        "azure": {
            "label": "Azure AD",
            "type": "oauth",
            "scope": "openid email profile",
            "authorize": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize",
            "token": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token",
            "userinfo": "https://graph.microsoft.com/beta/me",
            "logo_class": "fa fa-windowslive"
        },
        "azureb2c": {
            "label": "Azure B2C",
            "type": "openidconnect",
            "scope": "openid email",
            "authorize": "https://{tenant}.b2clogin.com/{tenant}.onmicrosoft.com/{policy}/oauth2/v2.0/authorize",
            "token": "https://{tenant}.b2clogin.com/{tenant}.onmicrosoft.com/{policy}/oauth2/v2.0/token",
            "userinfo": "",
            "logo_class": "fa fa-windowslive"
        },
        "cognito": {
            "label": "AWS Cognito",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://{domain}/oauth2/authorize",
            "token": "https://{domain}/oauth2/token",
            "userinfo": "https://{domain}/oauth2/userInfo",
            "logo_class": "fa fa-amazon"
        },
        "adfs": {
            "label": "ADFS",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://{domain}/adfs/oauth2/authorize/",
            "token": "https://{domain}/adfs/oauth2/token/",
            "userinfo": "",
            "logo_class": "fa fa-windowslive"
        },
        "whmcs": {
            "label": "WHMCS",
            "type": "oauth",
            "scope": "openid profile email",
            "authorize": "https://{domain}/oauth/authorize.php",
            "token": "https://{domain}/oauth/token.php",
            "userinfo": "https://{domain}/oauth/userinfo.php?access_token=",
            "logo_class": "fa fa-lock"
        },
        "keycloak": {
            "label": "keycloak",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://{domain}/realms/{realm}/protocol/openid-connect/auth",
            "token": "https://{domain}/realms/{realm}/protocol/openid-connect/token",
            "userinfo": "{domain}/realms/{realm}/protocol/openid-connect/userinfo",
            "logo_class": "fa fa-lock"
        },
        "slack": {
            "label": "Slack",
            "type": "oauth",
            "scope": "users.profile:read",
            "authorize": "https://slack.com/oauth/authorize",
            "token": "https://slack.com/api/oauth.access",
            "userinfo": "https://slack.com/api/users.profile.get",
            "logo_class": "fa fa-slack"
        },
        "discord": {
            "label": "Discord",
            "type": "oauth",
            "scope": "identify email",
            "authorize": "https://discordapp.com/api/oauth2/authorize",
            "token": "https://discordapp.com/api/oauth2/token",
            "userinfo": "https://discordapp.com/api/users/@me",
            "logo_class": "fa fa-lock"
        },
        "invisioncommunity": {
            "label": "Invision Community",
            "type": "oauth",
            "scope": "email",
            "authorize": "{domain}/oauth/authorize/",
            "token": "https://{domain}/oauth/token/",
            "userinfo": "https://{domain}/oauth/me",
            "logo_class": "fa fa-lock"
        },
        "bitrix24": {
            "label": "Bitrix24",
            "type": "oauth",
            "scope": "user",
            "authorize": "https://{accountid}.bitrix24.com/oauth/authorize",
            "token": "https://{accountid}.bitrix24.com/oauth/token",
            "userinfo": "https://{accountid}.bitrix24.com/rest/user.current.json?auth=",
            "logo_class": "fa fa-clock-o"
        },
        "wso2": {
            "label": "WSO2",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://{domain}/wso2/oauth2/authorize",
            "token": "https://{domain}/wso2/oauth2/token",
            "userinfo": "https://{domain}/wso2/oauth2/userinfo",
            "logo_class": "fa fa-lock"
        },
        "okta": {
            "label": "Okta",
            "type": "openidconnect",
            "scope": "openid email profile",
            "authorize": "https://{domain}/oauth2/default/v1/authorize",
            "token": "https://{domain}/oauth2/default/v1/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "onelogin": {
            "label": "OneLogin",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://{domain}/oidc/auth",
            "token": "https://{domain}/oidc/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "gapps": {
            "label": "Google",
            "type": "oauth",
            "scope": "email",
            "authorize": "https://accounts.google.com/o/oauth2/auth",
            "token": "https://www.googleapis.com/oauth2/v4/token",
            "userinfo": "https://www.googleapis.com/oauth2/v1/userinfo",
            "logo_class": "fa fa-google-plus"
        },
        "fbapps": {
            "label": "Facebook",
            "type": "oauth",
            "scope": "public_profile email",
            "authorize": "https://www.facebook.com/dialog/oauth",
            "token": "https://graph.facebook.com/v2.8/oauth/access_token",
            "userinfo": "https://graph.facebook.com/me/?fields=id,name,email,age_range,first_name,gender,last_name,link",
            "logo_class": "fa fa-facebook"
        },
        "gluu": {
            "label": "Gluu Server",
            "type": "oauth",
            "scope": "openid",
            "authorize": "http://{domain}/oxauth/restv1/authorize",
            "token": "http://{domain}/oxauth/restv1/token",
            "userinfo": "http:///{domain}/oxauth/restv1/userinfo",
            "logo_class": "fa fa-lock"
        },
        "linkedin": {
            "label": "LinkedIn",
            "type": "oauth",
            "scope": "openid email profile",
            "authorize": "https://www.linkedin.com/oauth/v2/authorization",
            "token": "https://www.linkedin.com/oauth/v2/accessToken",
            "userinfo": "https://api.linkedin.com/v2/me",
            "logo_class": "fa fa-linkedin-square"
        },
        "strava": {
            "label": "Strava",
            "type": "oauth",
            "scope": "public",
            "authorize": "https://www.strava.com/oauth/authorize",
            "token": "https://www.strava.com/oauth/token",
            "userinfo": "https://www.strava.com/api/v3/athlete",
            "logo_class": "fa fa-lock"
        },
        "fitbit": {
            "label": "FitBit",
            "type": "oauth",
            "scope": "profile",
            "authorize": "https://www.fitbit.com/oauth2/authorize",
            "token": "https://api.fitbit.com/oauth2/token",
            "userinfo": "https://www.fitbit.com/1/user",
            "logo_class": "fa fa-lock"
        },
        "box": {
            "label": "Box",
            "type": "oauth",
            "scope": "root_readwrite",
            "authorize": "https://account.box.com/api/oauth2/authorize",
            "token": "https://api.box.com/oauth2/token",
            "userinfo": "https://api.box.com/2.0/users/me",
            "logo_class": "fa fa-lock"
        },
        "github": {
            "label": "GitHub",
            "type": "oauth",
            "scope": "user repo",
            "authorize": "https://github.com/login/oauth/authorize",
            "token": "https://github.com/login/oauth/access_token",
            "userinfo": "https://api.github.com/user",
            "logo_class": "fa fa-github"
        },
        "gitlab": {
            "label": "GitLab",
            "type": "oauth",
            "scope": "read_user",
            "authorize": "https://gitlab.com/oauth/authorize",
            "token": "http://gitlab.com/oauth/token",
            "userinfo": "https://gitlab.com/api/v4/user",
            "logo_class": "fa fa-gitlab"
        },
        "clever": {
            "label": "Clever",
            "type": "oauth",
            "scope": "read:students read:teachers read:user_id",
            "authorize": "https://clever.com/oauth/authorize",
            "token": "https://clever.com/oauth/tokens",
            "userinfo": "https://api.clever.com/v1.1/me",
            "logo_class": "fa fa-lock"
        },
        "salesforce": {
            "label": "Salesforce",
            "type": "oauth",
            "scope": "email",
            "authorize": "https://login.salesforce.com/services/oauth2/authorize",
            "token": "https://login.salesforce.com/services/oauth2/token",
            "userinfo": "https://login.salesforce.com/services/oauth2/userinfo",
            "logo_class": "fa fa-lock"
        },
        "reddit": {
            "label": "Reddit",
            "type": "oauth",
            "scope": "identity",
            "authorize": "https://www.reddit.com/api/v1/authorize",
            "token": "https://www.reddit.com/api/v1/access_token",
            "userinfo": "https://www.reddit.com/api/v1/me",
            "logo_class": "fa fa-reddit"
        },
        "paypal": {
            "label": "PayPal",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://www.paypal.com/signin/authorize",
            "token": "https://api.paypal.com/v1/oauth2/token",
            "userinfo": "",
            "logo_class": "fa fa-paypal"
        },
        "swiss-rx-login": {
            "label": "Swiss RX Login",
            "type": "openidconnect",
            "scope": "anonymous",
            "authorize": "https://www.swiss-rx-login.ch/oauth/authorize",
            "token": "https://swiss-rx-login.ch/oauth/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "yahoo": {
            "label": "Yahoo",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://api.login.yahoo.com/oauth2/request_auth",
            "token": "https://api.login.yahoo.com/oauth2/get_token",
            "userinfo": "",
            "logo_class": "fa fa-yahoo"
        },
        "spotify": {
            "label": "Spotify",
            "type": "oauth",
            "scope": "user-read-private user-read-email",
            "authorize": "https://accounts.spotify.com/authorize",
            "token": "https://accounts.spotify.com/api/token",
            "userinfo": "https://api.spotify.com/v1/me",
            "logo_class": "fa fa-spotify"
        },
        "eveonlinenew": {
            "label": "Eve Online",
            "type": "oauth",
            "scope": "publicData",
            "authorize": "https://login.eveonline.com/oauth/authorize",
            "token": "https://login.eveonline.com/oauth/token",
            "userinfo": "https://esi.evetech.net/verify",
            "logo_class": "fa fa-lock"
        },
        "vkontakte": {
            "label": "VKontakte",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://oauth.vk.com/authorize",
            "token": "https://oauth.vk.com/access_token",
            "userinfo": "https://api.vk.com/method/users.get?fields=id,name,email,age_range,first_name,gender,last_name,link&access_token=",
            "logo_class": "fa fa-vk"
        },
        "pinterest": {
            "label": "Pinterest",
            "type": "oauth",
            "scope": "read_public",
            "authorize": "https://api.pinterest.com/oauth/",
            "token": "https://api.pinterest.com/v1/oauth/token",
            "userinfo": "https://api.pinterest.com/v1/me/",
            "logo_class": "fa fa-pinterest"
        },
        "vimeo": {
            "label": "Vimeo",
            "type": "oauth",
            "scope": "public",
            "authorize": "https://api.vimeo.com/oauth/authorize",
            "token": "https://api.vimeo.com/oauth/access_token",
            "userinfo": "https://api.vimeo.com/me",
            "logo_class": "fa fa-vimeo"
        },
        "deviantart": {
            "label": "DeviantArt",
            "type": "oauth",
            "scope": "browse",
            "authorize": "https://www.deviantart.com/oauth2/authorize",
            "token": "https://www.deviantart.com/oauth2/token",
            "userinfo": "https://www.deviantart.com/api/v1/oauth2/user/profile",
            "logo_class": "fa fa-deviantart"
        },
        "dailymotion": {
            "label": "Dailymotion",
            "type": "oauth",
            "scope": "email",
            "authorize": "https://www.dailymotion.com/oauth/authorize",
            "token": "https://api.dailymotion.com/oauth/token",
            "userinfo": "https://api.dailymotion.com/user/me?fields=id,username,email,first_name,last_name",
            "logo_class": "fa fa-lock"
        },
        "meetup": {
            "label": "Meetup",
            "type": "oauth",
            "scope": "basic",
            "authorize": "https://secure.meetup.com/oauth2/authorize",
            "token": "https://secure.meetup.com/oauth2/access",
            "userinfo": "https://api.meetup.com/members/self",
            "logo_class": "fa fa-lock"
        },
        "autodesk": {
            "label": "Autodesk",
            "type": "oauth",
            "scope": "user:read user-profile:read",
            "authorize": "https://developer.api.autodesk.com/authentication/v1/authorize",
            "token": "https://developer.api.autodesk.com/authentication/v1/gettoken",
            "userinfo": "https://developer.api.autodesk.com/userprofile/v1/users/@me",
            "logo_class": "fa fa-lock"
        },
        "zendesk": {
            "label": "Zendesk",
            "type": "oauth",
            "scope": "read write",
            "authorize": "https://{domain}/oauth/authorizations/new",
            "token": "https://{domain}/oauth/tokens",
            "userinfo": "https://{domain}/api/v2/users",
            "logo_class": "fa fa-lock"
        },
        "laravel": {
            "label": "Laravel",
            "type": "oauth",
            "scope": "",
            "authorize": "http://{domain}/oauth/authorize",
            "token": "http://{domain}/oauth/token",
            "userinfo": "http://{domain}}/api/user/get",
            "logo_class": "fa fa-lock"
        },
        "identityserver": {
            "label": "Identity Server",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://{domain}/connect/authorize",
            "token": "https://{domain}/connect/token",
            "userinfo": "https://{domain}/connect/introspect",
            "logo_class": "fa fa-lock"
        },
        "nextcloud": {
            "label": "Nextcloud",
            "type": "oauth",
            "scope": "user:read:email",
            "authorize": "https://{domain}/index.php/apps/oauth2/authorize",
            "token": "https://{domain}/index.php/apps/oauth2/api/v1/token",
            "userinfo": "https://{domain}/ocs/v2.php/cloud/user?format=json",
            "logo_class": "fa fa-lock"
        },
        "twitch": {
            "label": "Twitch",
            "type": "oauth",
            "scope": "Analytics:read:extensions",
            "authorize": "https://id.twitch.tv/oauth2/authorize",
            "token": "https://id.twitch.tv/oauth2/token",
            "userinfo": "https://id.twitch.tv/oauth2/userinfo",
            "logo_class": "fa fa-lock"
        },
        "wildApricot": {
            "label": "Wild Apricot",
            "type": "oauth",
            "scope": "auto",
            "authorize": "https://{domain}/sys/login/OAuthLogin",
            "token": "https://oauth.wildapricot.org/auth/token",
            "userinfo": "https://api.wildapricot.org/v2.1/accounts/{accountid}/contacts/me",
            "logo_class": "fa fa-lock"
        },
        "connect2id": {
            "label": "Connect2id",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://c2id.com/login",
            "token": "https://{domain}/token",
            "userinfo": "https://{domain}/userinfo",
            "logo_class": "fa fa-lock"
        },
        "miniorange": {
            "label": "miniOrange",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://login.xecurify.com/moas/idp/openidsso",
            "token": "https://login.xecurify.com/moas/rest/oauth/token",
            "userinfo": "https://logins.xecurify.com/moas/rest/oauth/getuserinfo",
            "logo_class": "fa fa-lock"
        },
        "orcid": {
            "label": "ORCID",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://orcid.org/oauth/authorize",
            "token": "https://orcid.org/oauth/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "diaspora": {
            "label": "Diaspora",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://{domain}/api/openid_connect/authorizations/new",
            "token": "https://{domain}/api/openid_connect/access_tokens",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "timezynk": {
            "label": "Timezynk",
            "type": "oauth",
            "scope": "read:user",
            "authorize": "https://api.timezynk.com/api/oauth2/v1/auth",
            "token": "https://api.timezynk.com/api/oauth2/v1/token",
            "userinfo": "https://api.timezynk.com/api/oauth2/v1/userinfo",
            "logo_class": "fa fa-lock"
        },
        "Amazon": {
            "label": "Amazon",
            "type": "oauth",
            "scope": "profile",
            "authorize": "https://www.amazon.com/ap/oa",
            "token": "https://api.amazon.com/auth/o2/token",
            "userinfo": "https://api.amazon.com/user/profile",
            "logo_class": "fa fa-lock"
        },
        "Office 365": {
            "label": "Office 365",
            "type": "oauth",
            "scope": "openid email profile",
            "authorize": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/authorize",
            "token": "https://login.microsoftonline.com/{tenant}/oauth2/v2.0/token",
            "userinfo": "https://graph.microsoft.com/beta/me",
            "logo_class": "fa fa-lock"
        },
        "Instagram": {
            "label": "Instagram",
            "type": "oauth",
            "scope": "user_profile user_media",
            "authorize": "https://api.instagram.com/oauth/authorize",
            "token": "https://api.instagram.com/oauth/access_token",
            "userinfo": "https://graph.instagram.com/me?fields=id,username&access_token=",
            "logo_class": "fa fa-lock"
        },
        "Line": {
            "label": "Line",
            "type": "oauth",
            "scope": "profile openid email",
            "authorize": "https://access.line.me/oauth2/v2.1/authorize",
            "token": "https://api.line.me/oauth2/v2.1/token",
            "userinfo": "https://api.line.me/v2/profile",
            "logo_class": "fa fa-lock"
        },
        "PingFederate": {
            "label": "PingFederate",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://{domain}/as/authorization.oauth2",
            "token": "https://{domain}/as/token.oauth2",
            "userinfo": "https://{domain}/idp/userinfo.oauth2",
            "logo_class": "fa fa-lock"
        },
        "OpenAthens": {
            "label": "OpenAthens",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://sp.openathens.net/oauth2/authorize",
            "token": "https://sp.openathens.net/oauth2/token",
            "userinfo": "https://sp.openathens.net/oauth2/userInfo",
            "logo_class": "fa fa-lock"
        },
        "Intuit": {
            "label": "Intuit",
            "type": "oauth",
            "scope": "openid email profile",
            "authorize": "https://appcenter.intuit.com/connect/oauth2",
            "token": "https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer",
            "userinfo": "https://accounts.platform.intuit.com/v1/openid_connect/userinfo",
            "logo_class": "fa fa-lock"
        },
        "Twitter": {
            "label": "Twitter",
            "type": "oauth",
            "scope": "email",
            "authorize": "https://api.twitter.com/oauth/authorize",
            "token": "https://api.twitter.com/oauth2/token",
            "userinfo": "https://api.twitter.com/1.1/users/show.json?screen_name=here-comes-twitter-screen-name",
            "logo_class": "fa fa-lock"
        },
        "WordPress": {
            "label": "WordPress",
            "type": "oauth",
            "scope": "profile openid email custom",
            "authorize": "http://{site_base_url}/wp-json/moserver/authorize",
            "token": "http://{site_base_url}/wp-json/moserver/token",
            "userinfo": "http://{site_base_url}/wp-json/moserver/resource",
            "logo_class": "fa fa-lock"
        },
        "Subscribestar": {
            "label": "Subscribestar",
            "type": "oauth",
            "scope": "user.read user.email.read",
            "authorize": "https://www.subscribestar.com/oauth2/authorize",
            "token": "https://www.subscribestar.com/oauth2/token",
            "userinfo": "https://www.subscribestar.com/api/graphql/v1?query={user{name,email}}",
            "logo_class": "fa fa-lock"
        },
        "Classlink": {
            "label": "Classlink",
            "type": "oauth",
            "scope": "email profile oneroster full",
            "authorize": "https://launchpad.classlink.com/oauth2/v2/auth",
            "token": "https://launchpad.classlink.com/oauth2/v2/token",
            "userinfo": "https://nodeapi.classlink.com/v2/my/info",
            "logo_class": "fa fa-lock"
        },
        "HP": {
            "label": "HP",
            "type": "oauth",
            "scope": "read",
            "authorize": "https://{hp_domain}/v1/oauth/authorize",
            "token": "https://{hp_domain}/v1/oauth/token",
            "userinfo": "https://{hp_domain}/v1/userinfo",
            "logo_class": "fa fa-lock"
        },
        "Basecamp": {
            "label": "Basecamp",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://launchpad.37signals.com/authorization/new?type=web_server",
            "token": "https://launchpad.37signals.com/authorization/token?type=web_server",
            "userinfo": "https://launchpad.37signals.com/authorization.json",
            "logo_class": "fa fa-lock"
        },
        "Feide": {
            "label": "Feide",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://auth.dataporten.no/oauth/authorization",
            "token": "https://auth.dataporten.no/oauth/token",
            "userinfo": "https://auth.dataporten.no/openid/userinfo",
            "logo_class": "fa fa-lock"
        },
        "Freja EID": {
            "label": "Freja EID",
            "type": "openidconnect",
            "scope": "openid profile email",
            "authorize": "https://oidc.prod.frejaeid.com/oidc/authorize",
            "token": "https://oidc.prod.frejaeid.com/oidc/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "ServiceNow": {
            "label": "ServiceNow",
            "type": "oauth",
            "scope": "email profile",
            "authorize": "https://{your-servicenow-domain}/oauth_auth.do",
            "token": "https://{your-servicenow-domain}/oauth_token.do",
            "userinfo": "https://{your-servicenow-domain}/{base-api-path}?access_token=",
            "logo_class": "fa fa-lock"
        },
        "IMIS": {
            "label": "IMIS",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://{your-imis-domain}/sso-pages/Aurora-SSO-Redirect.aspx",
            "token": "https://{your-imis-domain}/token",
            "userinfo": "https://{your-imis-domain}/api/iqa?queryname=$/Bearer_Info_Aurora",
            "logo_class": "fa fa-lock"
        },
        "OpenedX": {
            "label": "OpenedX",
            "type": "oauth",
            "scope": "email profile",
            "authorize": "https://{your-domain}/oauth2/authorize",
            "token": "https://{your-domain}/oauth2/access_token",
            "userinfo": "https://{your-domain}/api/mobile/v1/my_user_info",
            "logo_class": "fa fa-lock"
        },
        "Elvanto": {
            "label": "Elvanto",
            "type": "openidconnect",
            "scope": "ManagePeople",
            "authorize": "https://api.elvanto.com/oauth?",
            "token": "https://api.elvanto.com/oauth/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "DigitalOcean": {
            "label": "DigitalOcean",
            "type": "oauth",
            "scope": "read",
            "authorize": "https://cloud.digitalocean.com/v1/oauth/authorize",
            "token": "https://cloud.digitalocean.com/v1/oauth/token",
            "userinfo": "https://api.digitalocean.com/v2/account",
            "logo_class": "fa fa-lock"
        },
        "UNA": {
            "label": "UNA",
            "type": "openidconnect",
            "scope": "basic",
            "authorize": "https://{site-url}.una.io/oauth2/authorize?",
            "token": "https://{site-url}.una.io/oauth2/access_token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "MemberClicks": {
            "label": "MemberClicks",
            "type": "oauth",
            "scope": "read write",
            "authorize": "https://{orgId}.memberclicks.net/oauth/v1/authorize",
            "token": "https://{orgId}.memberclicks.net/oauth/v1/token",
            "userinfo": "https://{orgId}.memberclicks.net/api/v1/profile/me",
            "logo_class": "fa fa-lock"
        },
        "MineCraft": {
            "label": "MineCraft",
            "type": "openidconnect",
            "scope": "openid",
            "authorize": "https://login.live.com/oauth20_authorize.srf",
            "token": "https://login.live.com/oauth20_token.srf",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "Neon CRM": {
            "label": "Neon CRM",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://{your Neon CRM organization id}.z2systems.com/np/oauth/auth",
            "token": "https://{your Neon CRM organization id}.z2systems.com/np/oauth/token",
            "userinfo": "https://api.neoncrm.com/neonws/services/api/account/retrieveIndividualAccount?accountId=",
            "logo_class": "fa fa-lock"
        },
        "Canvas": {
            "label": "Canvas",
            "type": "oauth",
            "scope": "openid profile",
            "authorize": "https://{your-site-url}/login/oauth2/auth",
            "token": "https://{your-site-url}/login/oauth2/token",
            "userinfo": "https://{your-site-url}/login/v2.1/users/self",
            "logo_class": "fa fa-lock"
        },
        "Ticketmaster": {
            "label": "Ticketmaster",
            "type": "openidconnect",
            "scope": "openid email",
            "authorize": "https://auth.ticketmaster.com/as/authorization.oauth2",
            "token": "https://auth.ticketmaster.com/as/token.oauth2",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "Mindbody": {
            "label": "Mindbody",
            "type": "openidconnect",
            "scope": "email profile openid",
            "authorize": "https://signin.mindbodyonline.com/connect/authorize",
            "token": "https://signin.mindbodyonline.com/connect/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "iGov": {
            "label": "iGov",
            "type": "openidconnect",
            "scope": "openid profile",
            "authorize": "https://idp.government.gov/oidc/authorization",
            "token": "https://idp.government.gov/token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "LearnWorlds": {
            "label": "LearnWorlds",
            "type": "openidconnect",
            "scope": "openid profile",
            "authorize": "https://api.learnworlds.com/oauth",
            "token": "https://api.learnworlds.com/oauth2/access_token",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "Otoy": {
            "label": "Otoy",
            "type": "oauth",
            "scope": "openid",
            "authorize": "https://account.otoy.com/oauth/authorize",
            "token": "https://account.otoy.com/oauth/token",
            "userinfo": "https://account.otoy.com/api/v1/user.json",
            "logo_class": "fa fa-lock"
        },
        "other": {
            "label": "Custom OAuth",
            "type": "oauth",
            "scope": "",
            "authorize": "",
            "token": "",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        },
        "openidconnect": {
            "label": "Custom OpenID Connect App",
            "type": "openidconnect",
            "scope": "",
            "authorize": "",
            "token": "",
            "userinfo": "",
            "logo_class": "fa fa-lock"
        }
    }
    ';
}
function getAppData()
{
    return '{
        "azure": {
            "0":"both","1":"Tenant"
        },
        "azureb2c": {
            "0":"both","1":"Tenant,Policy"
        },
        "cognito": {
            "0":"both","1": "Domain"
        },
        "adfs": {
            "0":"both","1":"Domain"
        },
        "whmcs": {
            "0":"both","1":"Domain"
        },
        "keycloak": {
            "0":"both","1":"Domain,Realm"
        },
        "invisioncommunity": {
            "0":"both","1":"Domain"
        },
        "bitrix24": {
            "0":"both","1":"Domain"
        },
        "wso2": {
            "0":"both","1":"Domain"
        },
        "okta": {
            "0":"header","1":"Domain"
        },
        "onelogin": {
            "0":"both","1":"Domain"
        },
        "gluu": {
            "0":"both","1": "Domain" 
        },
        "zendesk": {
            "0":"both","1":"Domain"
        },
        "laravel": {
            "0":"both","1":"Domain"
        },
        "identityserver": {
            "0":"both","1":"Domain"
        },
        "nextcloud": {
            "0":"both","1":"Domain"
        },
        "wildApricot": {
            "0":"both","1":"Domain,AccountId"
        },
        "connect2id": {
            "0":"both","1":"Domain"
        },
        "diaspora": {
            "0":"both","1":"Domain" 
        },
        "Office 365": {
            "0":"both","1":"Tenant" 
        },
        "PingFederate": {
            "0":"both","1":"Domain"
        },
        "HP": {
            "0":"both","1":"Domain"
        },
        "Neon CRM": {
            "0":"both","1":"Domain"
        },
        "Canvas": {
            "0":"both","1":"Domain"
        },
        "UNA": {
            "0":"both","1":"Domain"
        },
        "OpenedX": {
            "0":"both","1":"Domain"
        },
        "ServiceNow": {
            "0":"both","1":"Domain"
        },
        "WordPress": {
            "0":"both","1":"Domain"
        },
        "MemberClicks": {
            "0":"both","1":"Domain"
        },
        "IMIS": {
            "0":"both","1":"Domain"
        }
    }';
}

function selectAppByIcon()
{
    $appArray = json_decode(getAppJson(), true);
    $listHtml = "<ul id='ojAuthAppsList'>";
    $PreConfiguredApps = array_slice($appArray, 0, count($appArray) - 2);
    foreach ($PreConfiguredApps as $key => $value) {
        $listHtml .= "<li class='oj_boot_border' ojAuthAppSelector='" . $value['label'] . "'>";
        $listHtml .= "<a class='oj_boot_select_app' href='" . JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&ojAuthAddApp=' . $key) . "'>";
        $listHtml .= "<div><p>" . $value['label'] . "</p></div>";
        $listHtml .= "</a></li>";
    }
    $listHtml .= '</ul>';
    ?> 
    <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3">
        <div class="oj_boot_col-sm-12 oj_boot_mt-4">
            <div class="oj_boot_row">
                <div class="oj_boot_col-sm-11 m-0 p-0">
                    <input type="text" class="oj_boot_form-control" name="appsearch" id="ojAuthAppsearchInput" value="" placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_SELECT_APP');?>">
                </div>
                <div class="oj_boot_col-sm-1 m-0 p-0 oj_boot_border oj_boot_btn-primary oj_boot_text-center oj_boot_align-middle">
                    <span class=""><i class="fa fa-search"></i></span>
                </div>
            </div>
        </div>
        <div class="oj_boot_col-sm-12 oj_boot_mt-4">
            <?php echo $listHtml; ?>
        </div>
        <div class="oj_boot_col-sm-12 oj_boot_mt-4">
            <div class="oj_boot_row">
                <div class="oj_boot_col-sm-12 oj_boot_my-2">
                    <h6><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CUSTOM_APPLICATIONS');?></h6>
                    <br>
                    <span class="oj_boot_p-1 oj_boot_text-dark"><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CUSTOM_APPLICATIONS_NOTE');?></span>
                </div>
                <div class="oj_boot_col-sm-6 oj_boot_my-5 oj_boot_text-center" ojAuthAppSelector='moCustomOuth2App'>
                    <a class="oj_boot_select_app" href="<?php echo JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&ojAuthAddApp=other');?>">
                        <div class="oj_boot_border" style="background:#fff;border: 1px solid #ddd;">
                            <p><?php echo $appArray['other']['label'];?></p>
                        </div>
                    </a>
                </div>
                <div class="oj_boot_col-sm-6 oj_boot_my-5 oj_boot_text-center" ojAuthAppSelector='moCustomOpenIdConnectApp'>
                    <a class="oj_boot_select_app" href="<?php echo JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&ojAuthAddApp=openidconnect');?>">
                        <div>
                            <p><?php echo $appArray['openidconnect']['label'];?></p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}

function selectCustomApp()
{
    $appArray = json_decode(getAppJson(), true);
    ?> 
    <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3">
        <div class="oj_boot_col-sm-12 oj_boot_my-2">
            <h6><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CUSTOM_APPLICATIONS');?></h6>
            <br>
            <span class="oj_boot_p-1 oj_boot_text-dark"><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CUSTOM_APPLICATIONS_NOTE');?></span>
        </div>
        <div class="oj_boot_col-sm-6 oj_boot_my-5 oj_boot_text-center" ojAuthAppSelector='moCustomOuth2App'>
            <a class="oj_boot_select_app" href="<?php echo JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&ojAuthAddApp=other');?>">
                <ul>
                    <li><?php echo $appArray['other']['label'];?></li>
                </ul>
            </a>
        </div>
        <div class="oj_boot_col-sm-6 oj_boot_my-5 oj_boot_text-center" ojAuthAppSelector='moCustomOpenIdConnectApp'>
            <a class="oj_boot_select_app" href="<?php echo JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&ojAuthAddApp=openidconnect');?>">
                <ul>
                    <li><?php echo $appArray['openidconnect']['label'];?></li>
                </ul>
            </a>
        </div>
    </div>
    <?php
}

function getAppDetails()
{
    $db = JFactory::getDbo();
    $query = $db->getQuery(true);
    $query->select('*');
    $query->from($db->quoteName('#__openjoomlaoauth_config'));
    $query->where($db->quoteName('id') . " = 1");
    $db->setQuery($query);
    return $db->loadAssoc();
}
function configuration($OauthApp, $appLabel)
{
    global $license_tab_link;
    $attribute = getAppDetails();
    $appJson = json_decode(getAppJson(), true);
    $appData = json_decode(getAppData(), true);

    $oj_oauth_app = $appLabel;
    $custom_app = "";
    $client_id = "";
    $client_secret = "";
    $redirecturi = JURI::root();
    $email_attr = "";
    $full_name_attr = "";
    $user_name_attr = "";
    $isAppConfigured = false;
    $oj_oauth_in_header = "checked=true";
    $oj_oauth_in_body   = "";
    $login_link_check="1";
    if (isset($attribute['in_header_or_body'])) {
        if ($attribute['in_header_or_body']=='inBody') {
            $oj_oauth_in_header = "";
            $oj_oauth_in_body   = "checked=true";
        } elseif ($attribute['in_header_or_body']=='inHeader') {
            $oj_oauth_in_header = "checked=true";
            $oj_oauth_in_body   = "";
        } elseif ($attribute['in_header_or_body']=='both') {
            $oj_oauth_in_header = "checked=true";
            $oj_oauth_in_body   = "checked=true";
        }
    } else {
        if (isset($appData[$appLabel]) && $appData[$appLabel][0]=='both') {
            $oj_oauth_in_header = "checked=true";
            $oj_oauth_in_body   = "checked=true";
        } elseif (isset($appData['appLabel']) && $appData['appLabel'][0]=='inBody') {
            $oj_oauth_in_header = "";
            $oj_oauth_in_body   = "checked=true";
        } elseif (isset($appData['appLabel']) && $appData['appLabel'][0]=='inHeader') {
            $oj_oauth_in_header = "checked=true";
            $oj_oauth_in_body   = "";
        }
    }
    if (isset($attribute['client_id'])) {
        $oj_oauth_app = empty($attribute['appname'])?$appLabel:$attribute['appname'];
        $custom_app = $attribute['custom_app'];
        $client_id = $attribute['client_id'];
        $client_secret = $attribute['client_secret'];
        $isAppConfigured = empty($client_id) || empty($client_secret) || empty($custom_app)||empty($attribute['redirecturi'])?false:true;
        $step1Check = empty($attribute['redirecturi'])?false:true;
        $step2Check = empty($client_id) || empty($client_secret) || empty($custom_app)||empty($attribute['redirecturi'])?false:true;
        $app_scope = empty($attribute['app_scope'])?$OauthApp['scope']:$attribute['app_scope'];
        $authorize_endpoint = empty($attribute['authorize_endpoint'])?null:$attribute['authorize_endpoint'];
        $access_token_endpoint = empty($attribute['access_token_endpoint'])?null:$attribute['access_token_endpoint'];
        $user_info_endpoint = empty($attribute['user_info_endpoint'])?null:$attribute['user_info_endpoint'];
        $email_attr = $attribute['email_attr'];
        $full_name_attr = $attribute['full_name_attr'];
        $user_name_attr = $attribute['user_name_attr'];
        $attributesNames = $attribute['test_attribute_name'];
        $step3Check = empty($email_attr)?false:true;
        $redirecturi = explode('//', JURI::root())[1];
        $attributesNames = explode(",", $attributesNames);

    }
    $get =JFactory::getApplication()->input->get->getArray();
    $progress = isset($get['progress'])?$get['progress']:"step1";

    ?>
    <div class="oj_boot_row m-0 p-1" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="oj_boot_col-sm-2 m-0 p-0" style="border-right:1px solid #001b4c">
            <div class="oj_boot_row m-0 p-0">
                <div class="oj_boot_col-sm-12 m-0 p-0">
                    <div onclick = "changeSubMenu(this , '#oj_redirectUrl_setting')" <?php echo(($progress=='step1')?'class="oj_sub_menu oj_sub_menu_active"':'class="oj_sub_menu"'); ?> >
                        <span>Step 1 <small>[Redirect URI]</small></span> <span class="oj_boot_float-right"><i class="oj_boot_text-success fa-solid fa-circle-check" <?php echo($step1Check?'style="display:block"':'style="display:none"'); ?> ></i></span>
                    </div>
                </div>
            </div>
            <div class="oj_boot_row m-0 p-0">
                <div class="oj_boot_col-sm-12 m-0 p-0">
                    
                    <div <?php if (1) {
                        echo "onclick = \"changeSubMenu(this,'#oj_client_setting')\" ";
                    } else {
                        echo "style='cursor:not-allowed;'";
                    }?> title="Configure the Step 1 First" <?php echo(($progress=='step2')?'class="oj_sub_menu oj_sub_menu_active"':'class="oj_sub_menu"'); ?>>
                        <span>Step 2 <small> [Client ID & Secret]</small></span></span> <span class="oj_boot_float-right"><i class=" oj_boot_text-success fa-solid fa-circle-check" <?php echo($step2Check?'style="display:block"':'style="display:none"'); ?>></i></span>
                    </div>
                </div>
            </div>
            <div class="oj_boot_row m-0 p-0">
                <div class="oj_boot_col-sm-12 m-0 p-0">
                    <div <?php if ($client_secret!="") {
                        echo "onclick = \"changeSubMenu(this,'#oj_attribute_setting')\" ";
                    } else {
                        echo "style='cursor:not-allowed'";
                    }?> title="Configure the Step 2 First" <?php echo(($progress=='step3')?'class="oj_sub_menu oj_sub_menu_active"':'class="oj_sub_menu"'); ?>>
                        <span>Step 3 <small>[Attribute Mapping]</small></span></span> <span class="oj_boot_float-right"><i class=" oj_boot_text-success fa-solid fa-circle-check" <?php echo($step3Check?'style="display:block"':'style="display:none"'); ?>></i></span>
                    </div>
                </div>
            </div>
            <div class="oj_boot_row m-0 p-0">
                <div  class="oj_boot_col-sm-12 m-0 p-0">
                    <div <?php if ($email_attr!="") {
                        echo "onclick = \"changeSubMenu(this,'#oj_sso_url')\" ";
                    } else {
                        echo "style='cursor:not-allowed'";
                    }?> title="Configure the Step 3 first" <?php echo(($progress=='step4')?'class="oj_sub_menu oj_sub_menu_active"':'class="oj_sub_menu"'); ?>>
                        <span>Step 4 <small>[SSO URL]</small></span></span>
                    </div>
                </div>
            </div>
            <hr style="background-color:black">
            <div class="oj_boot_row m-0 p-0">
                <div  class="oj_boot_col-sm-12 m-0 p-0">
                    <div onclick = "changeSubMenu(this,'#oj_importexport_setting')" class="oj_sub_menu">
                        <span>Import / Export Configuration </span>
                    </div>
                </div>
            </div>
            <div class="oj_boot_row m-0 mt-3 p-0">
                <div  class="oj_boot_col-sm-12 m-0 p-0">
                    <div class="oj_boot_text-center">
                        <?php  echo "<a href='index.php?option=com_openjoomla_oauth&view=accountsetup&task=accountsetup.clearConfig'
                                    class='oj_boot_btn oj_boot_pb-1 oj_boot_btn-danger' style='padding:2px 5px'>".JText::_('COM_OPENJOOMLA_OAUTH_DELETE_APPLICATION')."</a>";
    ?> 
                    </div>
                </div>
            </div>
        </div>
        <div class="oj_boot_col-sm-10">
            <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3" <?php echo(($progress=='step1')?'style="display:block"':'style="display:none"'); ?> id="oj_redirectUrl_setting">
                <div class="oj_boot_col-sm-12" id="oj_oauth_attributemapping">
                    <div class="oj_boot_row">
                        <div class="oj_boot_col-sm-12">
                            <div class="oj_boot_row oj_boot_mt-3" style="padding:10px;">
                                <div class="oj_boot_col-sm-12">
                                    <div class="oj_boot_row oj_boot_mt-3">
                                        <div class="oj_boot_col-sm-3">
                                            <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_APPLICATION');?></strong>
                                        </div>
                                        <div class="oj_boot_col-sm-8">
                                            <?php echo "<span style='background:#e9ecef;cursor:not-allowed;padding:2px; border:1px solid #e9ecef'>".$OauthApp['label']."</span>";?>
                                            <input type="hidden" name="oj_oauth_app_name" value="<?php echo $oj_oauth_app; ?>">
                                        </div>
                                    </div>
                                    <div class="oj_boot_row oj_boot_mt-3">
                                        <div class="oj_boot_col-sm-3">
                                            <strong><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CALLBACK_URL');?></strong>
                                        </div>
                                        <div class="oj_boot_col-sm-7">
                                            <form id="oauth_config_form_step1" method="post" action="<?php echo JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&task=accountsetup.saveConfig'); ?>">  
                                                <input type="hidden" name="oj_oauth_app_name" value="<?php echo $oj_oauth_app; ?>">
                                                <input type="hidden" name="oauth_config_form_step1" value="true">
                                                <div class="oj_boot_row m-0 p-0">
                                                    <div class="oj_boot_col-sm-2 m-0 p-0">
                                                        <select class="d-inline-block oj_boot_form-control" name="callbackurlhttp" id="callbackurlhttp">
                                                            <option value="http://" selected>http</option>
                                                            <option value="https://">https</option>
                                                        </select>
                                                    </div>
                                                    <div class="oj_boot_col-sm-10 m-0 p-0">
                                                        <input class="oj_boot_form-control" id="callbackurl" name="callbackurl" type="text" readonly  value='<?php echo $redirecturi; ?>'>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="oj_boot_col-sm-1">
                                            <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" onclick="copyToClipboard('#callbackurl','#callbackurlhttp');" style="color:red;background:#ccc;" ;>
                                                <span class="copytooltiptext">Copied!</span> 
                                            </em>
                                        </div>
                                        <div class="oj_boot_col-sm-12 oj_boot_mt-2">
                                            <small><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CALLBACK_URL_NOTE');?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="oj_boot_col-sm-12">
                            <div class="oj_boot_row oj_boot_mt-4">
                                <div class="oj_boot_col-sm-12 oj_boot_mt-3 oj_boot_text-right">
                                    <button name="send_query" onclick="step1Submit()" style="margin-bottom:3%;" class="oj_boot_btn oj_boot_btn-primary p-2 px-4">Save & Next <i class="fa-solid"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
                <script>
                    function step1Submit()
                    {
                        jQuery("#oauth_config_form_step1").submit();
                    }
                </script>
            </div>
            <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3" <?php echo(($progress=='step2')?'style="display:block"':'style="display:none"'); ?> id="oj_client_setting">
                <div class="oj_boot_col-sm-12 oj_boot_mt-5">
                    <form id="oauth_config_form_step2" name="" method="post" action="<?php echo JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&task=accountsetup.saveConfig'); ?>">  
                        <input type="hidden" name="oauth_config_form_step2" value="true">                   
                        <div class="oj_boot_row oj_boot_m-1 oj_boot_mt-3">
                            <div class="oj_boot_col-sm-12">
                                <div class="oj_boot_row">
                                    <div class="oj_boot_col-sm-12">
                                        <input type="hidden" id="oj_oauth_custom_app_name" name="oj_oauth_custom_app_name" value='<?php echo $OauthApp['label']; ?>' required>
                                        <input type="hidden" name="moOauthAppName" value="<?php echo $appLabel; ?>">
                                        <input type="hidden" name="oj_oauth_app_name" value="<?php echo $oj_oauth_app; ?>">
                                    </div>
                                </div>
                                <div class="oj_boot_row oj_boot_mt-3">
                                    <div class="oj_boot_col-sm-3">
                                        <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CLIENT_ID'); ?></strong>
                                    </div>
                                    <div class="oj_boot_col-sm-7">
                                        <input placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_CLIENT_ID_PLACEHOLDER');?>" class="oj_boot_form-control" required="" type="text" name="oj_oauth_client_id" id="oj_oauth_client_id" value='<?php echo $client_id; ?>'>
                                    </div>
                                </div>
                                <div class="oj_boot_row oj_boot_mt-3">
                                    <div class="oj_boot_col-sm-3">
                                        <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_CLIENT_SECRET'); ?></strong>
                                    </div>
                                    <div class="oj_boot_col-sm-7">
                                        <input placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_CLIENT_SECRET_PLACEHOLDER');?>" class="oj_boot_form-control" type="text" id="oj_oauth_client_secret" name="oj_oauth_client_secret" value='<?php echo $client_secret; ?>'>
                                    </div>
                                </div>
                                <?php
        if ($authorize_endpoint==null) {
            if (isset($appData[$appLabel])) {
                $fields = explode(",", $appData[$appLabel]['1']);
                foreach ($fields as $key => $value) {
                    if ($value == 'Tenant') {
                        $placeholder = JText::_('COM_OPENJOOMLA_OAUTH_ENTER_THE_TENANT_ID');
                    } elseif ($value=='Domain') {
                        $placeholder = JText::_('COM_OPENJOOMLA_OAUTH_ENTER_THE_DOMAIN');
                    } else {
                        $placeholder = JText::_('COM_OPENJOOMLA_OAUTH_ENTER_THE_DETAILS').$value ;
                    }
                    echo '<div class="oj_boot_row oj_boot_mt-3"><div class="oj_boot_col-sm-3">
                                                <strong><span class="oj_oauth_highlight">*</span>'.$value.'</strong>
                                                </div>
                                                <div class="oj_boot_col-sm-7">
                                                    <input class="oj_boot_form-control" placeholder="'.$placeholder.'" type="text" id="" name="'.$value.'" value="" required>
                                                </div></div>';
                }
            } else { ?>
                                            <div class="oj_boot_row oj_boot_mt-3">
                                                <div class="oj_boot_col-sm-3">
                                                    <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_APP_SCOPE');?></strong>
                                                </div>
                                                <div class="oj_boot_col-sm-7">
                                                    <input class="oj_boot_form-control" placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_APP_SCOPE_PLACEHOLDER');?>" type="text" id="oj_oauth_scope" name="oj_oauth_scope" value='<?php echo $app_scope ?>' required>
                                                </div>
                                            </div>
                                            <div class="oj_boot_row oj_boot_mt-3">
                                                <div class="oj_boot_col-sm-3">
                                                    <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_AUTHORIZE_ENDPOINT');?></strong>
                                                </div>
                                                <div class="oj_boot_col-sm-7">
                                                    <input class="oj_boot_form-control" placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_AUTHORIZE_ENDPOINT_PLACEHOLDER');?>" type="text" id="oj_oauth_authorizeurl" name="oj_oauth_authorizeurl" value='<?php echo $appJson[$appLabel]["authorize"] ?>' required>
                                                </div>
                                                <div class="oj_boot_col-sm-1">
                                                    <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" ; onclick="copyToClipboard('#oj_oauth_authorizeurl');" style="color:red;background:#ccc;" ;>
                                                        <span class="copytooltiptext">Copied!</span>
                                                    </em>
                                                </div>
                                            </div>
                                            <div class="oj_boot_row oj_boot_mt-3">
                                                <div class="oj_boot_col-sm-3">
                                                    <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_TOKEN_ENDPOINT'); ?></strong>
                                                </div>
                                                <div class="oj_boot_col-sm-7">
                                                    <input class="oj_boot_form-control" placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_TOKEN_ENDPOINT_PLACEHOLDER');?>" type="text" id="oj_oauth_accesstokenurl" name="oj_oauth_accesstokenurl" value='<?php echo $appJson[$appLabel]['token']; ?>' required>
                                                </div>
                                                <div class="oj_boot_col-sm-1">
                                                    <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" onclick="copyToClipboard('#oj_oauth_accesstokenurl');" style="color:red;background:#ccc;" ;>
                                                        <span class="copytooltiptext">Copied!</span>
                                                    </em>
                                                </div>
                                            </div>                           
                                            <?php
                    if (!isset($OauthApp['type']) || $OauthApp['type']=='oauth') {?>
                                                    <div class="oj_boot_row oj_boot_mt-3" id="oj_oauth_resourceownerdetailsurl_div">
                                                        <div class="oj_boot_col-sm-3">
                                                            <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_INFO_ENDPOINT'); ?></strong>
                                                        </div>
                                                        <div class="oj_boot_col-sm-7">
                                                            <input class="oj_boot_form-control" placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_INFO_ENDPOINT_PLACEHOLDER');?>" type="text" id="oj_oauth_resourceownerdetailsurl" name="oj_oauth_resourceownerdetailsurl" value='<?php echo $appJson[$appLabel]['userinfo']; ?>' required>
                                                        </div>
                                                        <div class="oj_boot_col-sm-1">
                                                            <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" onclick="copyToClipboard('#oj_oauth_resourceownerdetailsurl');" style="color:red;background:#ccc;" ;>
                                                                <span class="copytooltiptext">Copied!</span>
                                                            </em>
                                                        </div>
                                                    </div>
                                            <?php }
                    }
        } else { ?>
                                        <div class="oj_boot_row oj_boot_mt-3">
                                            <div class="oj_boot_col-sm-3">
                                                <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_APP_SCOPE');?></strong>
                                            </div>
                                            <div class="oj_boot_col-sm-7">
                                                <input class="oj_boot_form-control" placeholder="<?php echo JText::_('COM_OPENJOOMLA_OAUTH_APP_SCOPE_PLACEHOLDER');?>" type="text" id="oj_oauth_scope" name="oj_oauth_scope" value='<?php echo $app_scope ?>' required>
                                            </div>
                                        </div>
                                        <div class="oj_boot_row oj_boot_mt-3">
                                            <div class="oj_boot_col-sm-3">
                                                <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_AUTHORIZE_ENDPOINT'); ?></strong>
                                            </div>
                                            <div class="oj_boot_col-sm-7">
                                                <input class="oj_boot_form-control" type="text" id="oj_oauth_authorizeurl" name="oj_oauth_authorizeurl" value='<?php echo $authorize_endpoint; ?>' required>
                                            </div>
                                            <div class="oj_boot_col-sm-1">
                                                <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" ; onclick="copyToClipboard('#oj_oauth_authorizeurl');" style="color:red;background:#ccc;" ;>
                                                    <span class="copytooltiptext">Copied!</span>
                                                </em>
                                            </div>
                                        </div>
                                        <div class="oj_boot_row oj_boot_mt-3">
                                            <div class="oj_boot_col-sm-3">
                                                <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_TOKEN_ENDPOINT'); ?></strong>
                                            </div>
                                            <div class="oj_boot_col-sm-7">
                                                <input class="oj_boot_form-control" type="text" id="oj_oauth_accesstokenurl" name="oj_oauth_accesstokenurl" value='<?php echo $access_token_endpoint; ?>' required>
                                            </div>
                                            <div class="oj_boot_col-sm-1">
                                                <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" onclick="copyToClipboard('#oj_oauth_accesstokenurl');" style="color:red;background:#ccc;" ;>
                                                    <span class="copytooltiptext">Copied!</span>
                                                </em>
                                            </div>
                                        </div>
                                        <?php
                if (!isset($OauthApp['type']) || $OauthApp['type']=='oauth') {?>
                                                <div class="oj_boot_row oj_boot_mt-3" id="oj_oauth_resourceownerdetailsurl_div">
                                                    <div class="oj_boot_col-sm-3">
                                                        <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_INFO_ENDPOINT'); ?></strong>
                                                    </div>
                                                    <div class="oj_boot_col-sm-7">
                                                        <input class="oj_boot_form-control" type="text" id="oj_oauth_resourceownerdetailsurl" name="oj_oauth_resourceownerdetailsurl" value='<?php echo $user_info_endpoint; ?>' required>
                                                    </div>
                                                    <div class="oj_boot_col-sm-1">
                                                        <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" onclick="copyToClipboard('#oj_oauth_resourceownerdetailsurl');" style="color:red;background:#ccc;" ;>
                                                            <span class="copytooltiptext">Copied!</span>
                                                        </em>
                                                    </div>
                                                </div>
                                        <?php }
                }
    ?>    
                                <div class="oj_boot_row oj_boot_mt-3">
                                    <div class="oj_boot_col-sm-3">
                                        <b><?php echo JText::_('COM_OPENJOOMLA_OAUTH_SET_CLIENT_CREDENTIALS');?></b>
                                    </div>
                                    <div class="oj_boot_col-sm-7">
                                        <input type="checkbox" style='vertical-align: -2px;' name="oj_oauth_in_header" value="1" <?php echo " ".$oj_oauth_in_header; ?>>&nbsp;<?php echo JText::_('COM_OPENJOOMLA_OAUTH_SET_CREDENTIAL_IN_HEADER');?>
                                        <input type="checkbox" style='vertical-align: -2px;' class="oj_table_textbox" name="oj_oauth_body" value="1" <?php echo " ".$oj_oauth_in_body; ?> >&nbsp; <?php echo JText::_('COM_OPENJOOMLA_OAUTH_SET_CREDENTIAL_IN_BODY');?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>        
                    <div class="oj_boot_row oj_boot_mt-2">
                        <div class="oj_boot_col-sm-12 oj_boot_mt-3 oj_boot_text-right">
                            <button style="margin-bottom:3%;" class="oj_boot_btn oj_boot_btn-primary p-2 px-4" onclick="step2Submit()">Save Configuration</button>
                        </div>
                    </div>
                    <script>
                        function step2Submit()
                        {
                            jQuery("#oauth_config_form_step2").submit();
                        }
                        
                    </script>
                </div>
            </div>
            <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3" <?php echo(($progress=='step3')?'style="display:block"':'style="display:none"'); ?> id="oj_attribute_setting">
                <div class="oj_boot_col-sm-12 oj_boot_mt-5">
                   <div class="oj_boot_row oj_boot_mt-3">
                        <div class="oj_boot_col-sm-3">
                            <strong>Test Configuration</strong>
                        </div>
                        <div class="oj_boot_col-sm-7">
                            <button style="margin-bottom:3%;" class="oj_boot_btn oj_boot_btn-primary p-2 px-4" onclick="testConfiguration()">Test Configuration</button>
                        </div>
                        <div class="oj_boot_col-sm-12 oj_boot_mb-5">
                            <br>
                            <span>
                               <strong>Note : </strong> Click the "Test Configuration" button to confirm the attributes obtained from the OAuth Provider. Once the test configuration is successful, proceed to configure the attribute mapping below. This ensures that the mapping is based on accurate and validated data from the OAuth Provider.
                            </span>
                        </div>
                    </div>
                    <form id="oauth_mapping_form" name="oauth_config_form" method="post" action="<?php echo JRoute::_('index.php?option=com_openjoomla_oauth&view=accountsetup&task=accountsetup.saveMapping'); ?>">
                        <div class="oj_boot_row oj_boot_mt-3">
                            <div class="oj_boot_col-sm-3">
                                <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_EMAIL_ATTR'); ?></strong>
                            </div>
                            <div class="oj_boot_col-sm-7">
                                <?php
        if (count($attributesNames) != 0 && count($attributesNames) != 1) {
            ?>
                                        <select required class="oj_boot_form-control oj_boot_h-100" name="oj_oauth_email_attr" id="oj_oauth_email_attr">
                                            <option value="none" selected><?php echo JText::_('COM_OPENJOOMLA_OAUTH_EMAIL_ATTR_NOTE');?></option>
                                            <?php
                    foreach ($attributesNames as $key => $value) {
                        if ($value == $email_attr) {
                            $checked = "selected";
                        } else {
                            $checked = "";
                        }
                        if ($value!="") {
                            echo"<option ".$checked." value='".$value."'>".$value."</option>";
                        }
                    }
            ?>
                                        </select>
                                        <?php
        } else {
            ?>
                                        <input type="text" name="" class="oj_boot_form-control" disabled placeholder="Click on Test Configuration button above in order to get the attributes" id="">
                                        <?php
        }
    ?>
                               
                            </div>
                        </div>
                        <div class="oj_boot_row oj_boot_mt-2">
                            <div class="oj_boot_col-sm-3">
                                <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_NAME_ATTR'); ?></strong>
                            </div>
                            <div class="oj_boot_col-sm-7">
                                <?php
        if (count($attributesNames) != 0 && count($attributesNames) != 1) {
            ?>
                                        <select required class="oj_boot_form-control oj_boot_h-100" name="oj_oauth_full_name_attr" id="oj_oauth_full_name_attr">
                                            <option value="none" selected><?php echo JText::_('COM_OPENJOOMLA_OAUTH_NAME_ATTR_NOTE');?></option>
                                            <?php
                    foreach ($attributesNames as $key => $value) {
                        if ($value == $full_name_attr) {
                            $checked = "selected";
                        } else {
                            $checked = "";
                        }
                        if ($value!="") {
                            echo"<option ".$checked." value='".$value."'>".$value."</option>";
                        }
                    }
            ?>
                                        </select>
                                        <?php
        } else {
            ?>
                                        <input type="text" name="" class="oj_boot_form-control" disabled placeholder="Click on Test Configuration button above in order to get the attributes" id="">
                                        <?php
        }
    ?>
                                
                            </div>
                        </div>
                        <div class="oj_boot_row oj_boot_mt-2">
                            <div class="oj_boot_col-sm-3">
                                <strong><span class="oj_oauth_highlight">*</span><?php echo JText::_('COM_OPENJOOMLA_OAUTH_USER_NAME_ATTR'); ?></strong>
                            </div>
                            <div class="oj_boot_col-sm-7">
                                <?php
        if (count($attributesNames) != 0 && count($attributesNames) != 1) {
            ?>
                                        <select required class="oj_boot_form-control oj_boot_h-100" name="oj_oauth_user_name_attr" id="oj_oauth_user_name_attr">
                                            <option value="none" selected><?php echo JText::_('COM_OPENJOOMLA_OAUTH_USER_NAME_ATTR_NOTE');?></option>
                                            <?php
                    foreach ($attributesNames as $key => $value) {
                        if ($value == $user_name_attr) {
                            $checked = "selected";
                        } else {
                            $checked = "";
                        }
                        if ($value!="") {
                            echo"<option ".$checked." value='".$value."'>".$value."</option>";
                        }
                    }
            ?>
                                        </select>
                                        <?php
        } else {
            ?>
                                        <input type="text" name="" class="oj_boot_form-control" disabled placeholder="Click on Test Configuration button above in order to get the attributes" id="">
                                        <?php
        }
    ?>
                                
                            </div>
                        </div>
                        <div class="oj_boot_row oj_boot_mt-4">
                            <div class="oj_boot_col-sm-12 oj_boot_mt-3 oj_boot_text-right">
                                <input type="submit" name="send_query" style="margin-bottom:3%;" class="oj_boot_btn oj_boot_btn-primary p-2" value="Finish Configuration"> 
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3 " <?php echo(($progress=='step4')?'style="display:block"':'style="display:none"'); ?> id="oj_sso_url">
                <div class=" oj_boot_col-sm-12 oj_boot_mt-5">
                    <div class="oj_boot_row oj_boot_mt-3 oj_boot_mb-5">
                        <div class="oj_boot_col-sm-12 oj_boot_mb-3">
                            <?php echo JText::_('COM_OPENJOOMLA_OAUTH_LOGIN_URL_NOTE');?>
                        </div>
                        <div class="oj_boot_col-sm-3">
                            <strong><?php echo JText::_('COM_OPENJOOMLA_OAUTH_LOGIN_URL');?></strong>
                        </div>
                        <div class="oj_boot_col-sm-8">
                            <input class="oj_boot_form-control" id="loginUrl" type="text" readonly="true" value='<?php echo JURI::root() . '?openjoomlarequest=oauthredirect&app_name=' . $oj_oauth_app; ?>'>
                        </div>
                        <div class="oj_boot_col-sm-1">
                            <em class="fa fa-pull-right fa-lg fa-copy oj_copy copytooltip" onclick="copyToClipboard('#loginUrl');" style="color:red;background:#ccc;" ;>
                                <span class="copytooltiptext">Copied!</span>
                            </em>
                        </div>
                    </div>
                    <div class="oj_boot_row oj_boot_mt-3 oj_boot_mb-5">
                        <div class="oj_boot_col-sm-12">
                            <hr>
                            <h4><u>Steps to Create a Login button</u></h4>
                            <br>
                            <table class="oj_boot_table oj_boot_table-bordered oj_boot_table-striped">
                                <tr>
                                    <td class="w-15"><strong>STEP 1:</strong></td>
                                    <td>
                                        Navigate to Module Manager -Go to "Extensions" > "Site Modules" from the top menu in the administrator area.
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>STEP 2:</strong> </td>
                                    <td>
                                        Locate and Edit the Login Module-  Look for the "Login" module in the list of modules. Click on its title to edit it.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>STEP 3:</strong>
                                    </td>
                                    <td>
                                        Adjust Module Position- Check the position where the login module is displayed. Note this position as it will help you understand where the button needs to be placed.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>STEP 4:</strong>
                                    </td>
                                    <td>
                                        Add Custom HTML Module for the Button-In the Joomla admin, go to "Extensions" > "Modules" > "New" > "Custom HTML".
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>STEP 5:</strong>
                                    </td>
                                    <td>Configure the Custom HTML Module-In the "Custom HTML" module settings:</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>STEP 6:</strong>
                                    </td>
                                    <td>
                                        Set the title to a relevant name-Add your button HTML code in the module's content section. 
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>STEP 7:</strong>
                                    </td>
                                    <td>Set the Module Position-  Place this Custom HTML module in the same position as the login module or adjacent to it. Choose the appropriate module position where you want the button to appear.</td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>STEP 8:</strong>
                                    </td>
                                    <td>
                                        Assign Module to Menu Items - Configure the module assignment settings if needed to display the button on specific pages or menu items.
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>STEP 9:</strong> 
                                    </td>
                                    <td>
                                        Save Changes - Save the Custom HTML module settings.
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>STEP 10:</strong></td>
                                    <td>Check the Frontend - Visit the frontend of your Joomla website to verify that the button appears near the login button as intended.</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3 " style="display:none" id="oj_importexport_setting">
                <div class="oj_boot_col-sm-12"> 
                    <?php ojImportAndExport()?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function testConfiguration() {
            var appname = "<?php echo $appLabel; ?>";
            var winl = ( screen.width - 400 ) / 2,
            wint = ( screen.height - 800 ) / 2,
            winprops = 'height=' + 600 +
            ',width=' + 800 +
            ',top=' + wint +
            ',left=' + winl +
            ',scrollbars=1'+
            ',resizable';
            var myWindow = window.open('<?php echo JURI::root();?>' + '?openjoomlarequest=testattrmappingconfig&app=' + appname, "Test Attribute Configuration", winprops);
            var timer = setInterval(function() {   
            if(myWindow.closed) {  
                clearInterval(timer);  
                location.reload();
            }  
            }, 1); 
        }
    </script>  
    <?php
}
function attributerole()
{
    global $license_tab_link;
    $attribute = getAppDetails();
    $email = isset($attribute['email_attr'])?$attribute['email_attr']:"";
    $fullname = isset($attribute['full_name_attr'])?$attribute['full_name_attr']:"";
    $username = isset($attribute['user_name_attr'])?$attribute['user_name_attr']:"";
    ?>
    <div class="oj_boot_row m-0 p-1" style="box-shadow: 0px 0px 15px 5px lightgray;">
        <div class="oj_boot_col-sm-2 m-0 p-0" style="border-right:1px solid #001b4c">
            <div class="oj_boot_row m-0 p-0">
                <div class="oj_boot_col-sm-12 m-0 p-0">
                    <div onclick = "changeSubMenu(this , '#oj_basic_mapping')" class="oj_sub_menu oj_sub_menu_active">
                        <span>Basic Attribute's</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="oj_boot_col-sm-10">
            <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3" id="oj_basic_mapping">
                <div class="oj_boot_col-sm-12 oj_boot_mt-2" id="oj_oauth_attributemapping">
                    <div class="oj_boot_row oj_boot_mt-2">
                        <div class="oj_boot_col-sm-12">
                            <h5 class="element">
                                Map Basic User Attribute 
                            </h5>
                            <br>
                        </div>
                        <br><br>
                        <div class="oj_boot_col-sm-12">
                            <div class="oj_boot_row">
                                <div class="oj_boot_col-sm-12">
                                    <p> Configure the Basic attribute of joomla to the attribute coming from the OAuth Provider</p>
                                </div>
                            </div>
                        </div>
                        <div class="oj_boot_col-sm-12">
                            <div class="oj_boot_row">
                                <div class="oj_boot_col-sm-3">
                                    <label for=""><span class="oj_oauth_highlight">*</span>Username :</label>
                                </div>
                                <div class="oj_boot_col-sm-9">
                                    <input class="oj_boot_form-control" readonly type="text" id="oj_oauth_uname_attr" name="oj_oauth_uname_attr" value='<?php echo $username?>' placeholder="Enter the Username attribute name from oauth provider" required>
                                </div>
                            </div>
                            <div class="oj_boot_row oj_boot_mt-3">
                                <div class="oj_boot_col-sm-3">
                                    <label for=""><span class="oj_oauth_highlight">*</span>Email :</label>
                                </div>
                                <div class="oj_boot_col-sm-9">
                                    
                                    <input class="oj_boot_form-control" readonly type="text" name="oj_oauth_email_attr" value='<?php echo $email?>' placeholder="Enter the Email attribute name from oauth provider" required>
                                </div>
                            </div>
                            <div class="oj_boot_row oj_boot_mt-3">
                                <div class="oj_boot_col-sm-3">
                                    <label for="">
                                        <span class="oj_oauth_highlight">*</span>Display Name :
                                    </label>    
                                </div>
                                <div class="oj_boot_col-sm-9">
                                    
                                    <input class="oj_boot_form-control" disabled type="text"  id="oj_oauth_dname_attr" name="oj_oauth_dname_attr" value='<?php echo $fullname?>' placeholder="Enter the Username attribute name from oauth provider" value=''>
                                </div>
                            </div>
                            <div class="oj_boot_row oj_boot_mt-2">
                                <div class="oj_boot_col-sm-12 oj_boot_mt-3 oj_boot_text-right">
                                    <input type="submit" disabled style="cursor:not-allowed" name="send_query" value='<?php echo JText::_('COM_OPENJOOMLA_OAUTH_SAVE_ATTRIBUTE_MAPPING');?>' style="margin-bottom:3%;" class="oj_boot_btn oj_boot_btn-primary p-2"/>
                                </div>
                            </div>
                        </div>
                        
                    </div>  
                </div>
            </div> 
        </div>
    </div>
    <script>
       function changeSubMenu(element0,element1)
       {
            jQuery(".oj_sub_menu_active").removeClass("oj_sub_menu_active");
            jQuery(element0).addClass("oj_sub_menu_active");
            jQuery(element1).nextAll('div').css('display', 'none');
            jQuery(element1).prevAll().css('display', 'none');
            jQuery(element1).css("display", "block");
       }
    </script>
    <?php
}

function moOAuthConfiguration()
{
    global $license_tab_link;
    global $license_tab_link;
    $appArray = json_decode(getAppJson(), true);
    $app = JFactory::getApplication();
    $get = $app->input->get->getArray();
    $attribute = getAppDetails();
    $isAppConfigured = empty($attribute['client_secret']) || empty($attribute['client_id']) || empty($attribute['custom_app'])|| empty($attribute['redirecturi'])?false:true;
    if (isset($get['ojAuthAddApp']) && !empty($get['ojAuthAddApp'])) {
        configuration($appArray[$get['ojAuthAddApp']], $get['ojAuthAddApp']);
        return;
    } elseif ($isAppConfigured) {
        configuration($appArray[$attribute['appname']], $attribute['appname']);
        return;
    } else { ?>
        <div class="oj_boot_row m-0 p-1" style="box-shadow: 0px 0px 15px 5px lightgray;">
            <div class="oj_boot_col-sm-2 m-0 p-0" style="border-right:1px solid #001b4c">
                <div class="oj_boot_row m-0 p-0">
                    <div class="oj_boot_col-sm-12 m-0 p-0">
                        <div onclick = "changeSubMenu(this , '#oj_pre_configure_app')" class="oj_sub_menu oj_sub_menu_active">
                            <span>Pre-Configured Apps</span>
                        </div>
                    </div>
                </div>
                <div class="oj_boot_row m-0 p-0">
                    <div class="oj_boot_col-sm-12 m-0 p-0">
                        <div onclick = "changeSubMenu(this,'#oj_custom_app')" class="oj_sub_menu">
                            <span>Custom Application</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="oj_boot_col-sm-10">
                <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3" id="oj_pre_configure_app">
                    <?php selectAppByIcon() ;?>
                </div>
                <div class="oj_boot_row oj_boot_m-1 oj_boot_my-3" style="display:none" id="oj_custom_app">
                    <?php selectCustomApp(); ?>
                </div>
            </div>
        </div>
        <script>
        function changeSubMenu(element0,element1)
        {
                jQuery(".oj_sub_menu_active").removeClass("oj_sub_menu_active");
                jQuery(element0).addClass("oj_sub_menu_active");
                jQuery(element1).nextAll('div').css('display', 'none');
                jQuery(element1).prevAll().css('display', 'none');
                jQuery(element1).css("display", "block");
        }
        </script>
        <?php
    }
}

function ojImportAndExport()
{
    ?>
    <div class="oj_boot_row  oj_boot_mr-1  oj_boot_py-3 oj_boot_px-2 oj_tab_border" id="import_export_form">
        <div class="oj_boot_col-sm-12">
            <h3>
                Export Configuration
                <hr>
            </h3>
        </div>
        <div class="oj_boot_col-sm-12 oj_boot_mt-3">
            <div class="oj_boot_row">
                <div class="oj_boot_col-8">
                    <strong>Download Configuration: </strong>
                </div> 
                <div class="oj_boot_col-4">
                    <a href='index.php?option=com_openjoomla_oauth&view=accountsetup&task=accountsetup.exportConfiguration' class="oj_boot_btn oj_boot_btn-primary oj_boot_float-right" style='padding:2px 5px'><?php echo JText::_('COM_OPENJOOMLA_OAUTH_EXPORT_CONFIGURATION');?></a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
