..  include:: /Includes.rst.txt


..  _changelog:

=========
ChangeLog
=========

Version 5.1.1
=============

*   [BUGFIX] Add missing plugins to UpgradeWizard to migrate to CType

Version 5.1.0
=============

*   [TASK] New UpgradeWizard for converting old list_type plugins to CType
*   [REFACTORING] Refactored path for upgrade wizard from Classes/Upgrade to Classes/Update

Version 5.0.10
==============

*   Fixed error related to accessing invalid settings if it is not set

Version 5.0.9
=============

*   Add missing TABLE const to VideoRecordService

Version 5.0.8
=============

*   Add func tests for YouTubeVideoImporter

Version 5.0.7
=============

*   Replace FlashMessageService with MessageHelper

Version 5.0.6
=============

*   Test data array before array_merge

Version 5.0.5
=============

*   Add missing FlashMessageService

Version 5.0.4
=============

*   Use static instead of self in AbstractImport"

Version 5.0.3
=============

*   Add UpgradeWizard to migrate from switchableControllerActions to CType

Version 5.0.2
=============

*   Add UpgradeWizard to apply `uid` column to `tx_mediapool_playlist_video_mm`

Version 5.0.1
=============

*   Do not mark PlaylistService as readonly

Version 5.0.0
=============

*   Add TYPO3 13 compatibility
*   Remove TYPO3 11/12 compatibility
*   Declare PHP classes readonly where possible
*   Replace StandaloneView with ViewFactory
*   Add PHP 8.4 to runScript.sh and test also with PHP 8.4
*   Migrate to Site Sets

Version 4.0.1
=============

*   Prevent checking API key on any kind of datahandler process

Version 4.0.0
=============

*   Remove TYPO3 10 compatibility
*   Solve deprecations with TYPO3 11
*   Add TYPO3 12 compatibility

Version 3.0.0
=============

*   Remove TYPO3 9 compatibility
*   Add TYPO3 11 compatibility
*   Add LICENSE, .editorconfig, .gitignore, .gitattributes files
*   Update README.md
*   Remove ObjectManager where possible
*   Remove annotations where possible
*   Add Category model and CategoryRepository
*   Use TYPO3 RequestFactory instead of Guzzle Client directly
*   Update VideoPlatformUtility. It returns the target classes now.
*   Use Dependency Injection where possible. Services.yaml
*   Implement better error handling. Missing YouTube API key
