# TYPO3 Extension `mediapool`

[![Packagist][packagist-logo-stable]][extension-packagist-url]
[![Latest Stable Version][extension-build-shield]][extension-ter-url]
[![Total Downloads][extension-downloads-badge]][extension-packagist-url]
[![Monthly Downloads][extension-monthly-downloads]][extension-packagist-url]
[![TYPO3 13.4][TYPO3-shield]][TYPO3-13-url]

![Build Status](https://github.com/jweiland-net/mediapool/actions/workflows/ci.yml/badge.svg)

Embed your favorite YouTube Videos and Playlists. Import description, title and more by just pasting the YouTube link.

## 1 Features

* Show playlist with all of your favorite videos
* Show media from a sys_file_collection as gallery

## 2 Usage

### 2.1 Installation

#### Installation using Composer

The recommended way to install the extension is using Composer.

Run the following command within your Composer based TYPO3 project:

```
composer require jweiland/mediapool
```

#### Installation as extension from TYPO3 Extension Repository (TER)

Download and install `mediapool` with the extension manager module.

### 2.2 Minimal setup

1) Include the static TypoScript of the extension.
2) Create a playlist record. Attach YouTube playlist URI and save.
3) Create a plugin on a page and select at least the sysfolder as startingpoint.

<!-- MARKDOWN LINKS & IMAGES -->

[extension-build-shield]: https://poser.pugx.org/jweiland/mediapool/v/stable.svg?style=for-the-badge

[extension-downloads-badge]: https://poser.pugx.org/jweiland/mediapool/d/total.svg?style=for-the-badge

[extension-monthly-downloads]: https://poser.pugx.org/jweiland/mediapool/d/monthly?style=for-the-badge

[extension-ter-url]: https://extensions.typo3.org/extension/mediapool/

[extension-packagist-url]: https://packagist.org/packages/jweiland/mediapool/

[packagist-logo-stable]: https://img.shields.io/badge/--grey.svg?style=for-the-badge&logo=packagist&logoColor=white

[TYPO3-13-url]: https://get.typo3.org/version/13

[TYPO3-shield]: https://img.shields.io/badge/TYPO3-13.4-green.svg?style=for-the-badge&logo=typo3
