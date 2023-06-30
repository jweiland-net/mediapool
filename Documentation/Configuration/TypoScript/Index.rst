..  include:: /Includes.rst.txt


..  _typoscript:

==========
TypoScript
==========

`mediapool` needs some basic TypoScript configuration. To do so you have
to add an +ext template to either the root page of your website or to a
specific page which contains the `mediapool` plugin.

..  rst-class:: bignums

1.  Locate page

    You have to decide where you want to insert the TypoScript template. Either
    root page or page with `mediapool` plugin is OK.

2.  Create TypoScript template

    Switch to template module and choose the specific page from above in the
    pagetree. Choose `Click here to create an extension template` from the
    right frame. In the TYPO3 community it is also known as "+ext template".

3.  Add static template

    Choose `Info/Modify` from the upper selectbox and then click
    on `Edit the whole template record` button below the little table. On
    tab `Includes` locate the section `Include static (from extension)`. Use
    the search above `Available items` to search for `mediapool`. Hopefully
    just one record is visible below. Choose it, to move that record to
    the left.

4.  Save

    If you want you can give that template a name on tab "General", save
    and close it.

5.  Constants Editor

    Choose `Constant Editor` from the upper selectbox.

6.  `mediapool` constants

    Choose `PLUGIN.TX_MEDIAPOOL` from the category selectbox to show
    just `mediapool` related constants

7.  Configure constants

    Adapt the constants to your needs. We prefer to set all
    these `detailUid` and `listUid` constants. That prevents you
    from setting all these PIDs in each plugin individual.

8.  Configure TypoScript

    As constants will only allow modifying a fixed selection of TypoScript
    you also switch to `Info/Modify` again and click on `Setup`. Here you have
    the possibility to configure all `mediapool` related configuration.

View
====

..  confval:: templateRootPaths

    :type: array
    :Default: EXT:mediapool/Resources/Private/Templates/
    :Path: plugin.tx_mediapool.view.*

    You can override our Templates with your own SitePackage extension. We
    prefer to change this value in TS Constants.

..  confval:: partialRootPaths

    :type: array
    :Default: EXT:mediapool/Resources/Private/Partials/
    :Path: plugin.tx_mediapool.view.*

    You can override our Partials with your own SitePackage extension. We
    prefer to change this value in TS Constants.

..  confval:: layoutsRootPaths

    :type: array
    :Default: EXT:mediapool/Resources/Layouts/Templates/
    :Path: plugin.tx_mediapool.view.*

    You can override our Layouts with your own SitePackage extension. We
    prefer to change this value in TS Constants.
