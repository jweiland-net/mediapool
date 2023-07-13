..  include:: /Includes.rst.txt


..  _changelog:

=========
ChangeLog
=========

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
