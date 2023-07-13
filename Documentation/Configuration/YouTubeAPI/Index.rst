..  include:: /Includes.rst.txt


..  _youtube_api_key:

===============
YouTube API Key
===============

`mediapool` needs a YouTube API key to be allowed to retrieve the
playlist and video information. Please configure that key in
extension settings of `mediapool`.

..  rst-class:: bignums

1.  Visit Google Cloud Console

    Open following link in a browser, where you are already registered with
    your Google Email address: https://console.cloud.google.com

2.  Choose Project

    At the upper left, you can switch to the desired project or you can
    can create a new project

3.  Activate YouTube API

    Open the main menu on the left (burger menu) and
    choose "APIs and Services". Choose "Activate API and Services" button
    from the upper right. On new page I prefer to use the search.
    Search for "YouTube Data API v3". Select the result and click on
    "activate".

4.  Create API Key

    After activation open the main menu "APIs and Services" (don't click)
    and choose "registration data" from sub-menu. Choose "Create new
    registration data" from the upper border.

5.  Security

    We prefer to secure your new API key by a protection rule like server
    IP address.

6.  Configure `mediapool`

    Open extension settings of `mediapool` and apply the API key.
