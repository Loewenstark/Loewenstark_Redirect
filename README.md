Loewenstark_Redirect
=====================
- Redirect unvisible configurable child product URLs to parent with params

- Full url:

```
http://localhost:8888/magento_modultest/index.php/test-konfig-prodult.html?gclid=4343#92=5
```

- Params + conf.
```
gclid=4343#92=5
```

Installation Instructions
-------------------------
1. Install the extension via GitHub, and deploy with modman.
2. Clear the cache, logout from the admin panel and then login again.
3. Setup at System -> Configuration -> Löwenstark Redirect -> Redirect.

Uninstallation
--------------
1. Remove all extension files from your Magento installation OR
2. Modman remove Loewenstark_Redirect & modman clean

ToDO & Fix Me:
------------
- Redirect from disabled product to last product category
- Redirect fake url to home page

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/adamvarga).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Adam Varga