[![Laravel](https://img.shields.io/badge/Laravel-5.x-orange.svg)](http://laravel.com)
[![License](http://img.shields.io/badge/license-MIT-brightgreen.svg)](https://tldrlegal.com/license/mit-license)
[![Downloads](https://img.shields.io/packagist/dt/igaster/GITHUB_ADDRESS.svg)](https://packagist.org/packages/igaster/GITHUB_ADDRESS)
[![Build Status](https://img.shields.io/travis/igaster/GITHUB_ADDRESS.svg)](https://travis-ci.org/igaster/GITHUB_ADDRESS)
[![Codecov](https://img.shields.io/codecov/c/github/igaster/GITHUB_ADDRESS.svg)](https://codecov.io/github/igaster/GITHUB_ADDRESS)

## Instructions

	1. Clone this repo
	2. Search & Replace 'PACKAGE_NAMESPACE' with the Package's Namespace
	3. Search & Replace 'GITHUB_ADDRESS' with the git hub repo url
	4. Run `composer update`
	5. Build & Test your package!
	6. Submit to Packagist / Enable [Travis](https://travis-ci.org) / Upload to gitHub

## Use in Laravel app from local repository

	1. git init/add/commit current package
	2. Edit laravel app composer.json, add:

	    "repositories": [
	        {
	            "type": "vcs",
	            "url" : "../path/to/package-folder"
	        }
	    ],

    3. Add in 'require' section:

	    "require": {
			...
	        "igaster/GITHUB_ADDRESS" : "dev-master@dev"
	    }


	4. Run on laravel app: 
		"composer update igaster/GITHUB_ADDRESS --no-scripts"

	5. git add / git commit / composer update / repeat!

