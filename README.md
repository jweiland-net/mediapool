# TYPO3 Extension `mediapool`

[![TYPO3 13.4][TYPO3-shield]][TYPO3-13-url]
![Build Status](https://github.com/jweiland-net/mediapool/workflows/CI/badge.svg)

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

[TYPO3-13-url]: https://get.typo3.org/version/13

[TYPO3-shield]: https://img.shields.io/badge/TYPO3-13.4-green.svg?style=for-the-badge&logo=typo3
