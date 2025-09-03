# Yo! <img height="32" src="https://raw.githubusercontent.com/blackcater/blackcater/main/images/Hi.gif" width="32"/>
## Hou to use?

```php
$objectToDocument = new MyClass();

(new JsonAutodoc($objectToDocument))->generate(
    "The documentation of " . $objectToDocument::class . " endpoints",
    '/path/to/file'
);

(new YamlAutodoc($objectToDocument))->generate(
    "The documentation of " . $objectToDocument::class . " endpoints",
    '/path/to/file'
);
```
<hr>

## Usage examples
<a href="https://github.com/worldWarmWorm/autodoc/blob/a8683403fa89e7c2f5921179fca08a05026927b7/tests/EndpointAutodocTest.php#L18" target="_blank">/tests/EndpointAutodocTest.php</a><br><br>
<a href="https://github.com/worldWarmWorm/autodoc/blob/a8683403fa89e7c2f5921179fca08a05026927b7/src/Api/Product/Controller/ProductController.php#L41" target="_blank">/src/Api/Product/Controller/ProductController.php</a>
<hr>

## What can a library do?
Documents methods of any class, regardless of access modifiers, in two json and yaml formats. All you need to
do is call the constructor of the `JsonAutodoc` or `YamlAutodoc` class, throw an object of the class into it, the
methods of which need  to be documented and comply with the requirement that the documented class implement the
`EndpointInterface` interface. Where can I call the constructor of the `JsonAutodoc` or `YamlAutodoc` class? It depends
on your needs. For example, I'm in my project the `JsonAutodoc` constructor was called in the constructor body of the
class, which is an API for a third-party service. This way, the documentation is reassembled every time the API
endpoint is called and is always up to date.
<hr>

##  What if my project is written on the symfony/laravel/yii framework?
The library is easily integrated. You can use this approach: if you work on any of the modern MVC
frameworks, then generate your own documentation file for each controller. Select another controller, where there will
be a list links to the documentation of all other controllers as in the example with the file index.php this library.
This way it will be possible for any of the colleagues on the project (not just the programmer) to have an idea of how
the controllers in the project or API work.
<hr>

## What if I want to use your library, but my project doesn't have composer?
Download the zip from my repository and unzip the library folder. To work with the library, you will only
need the `/src/Autodoc` folder, but it uses the namespaces specified in `/composer.json` in its files. The files may
not be located in your project. Most likely, you will need to replace the library's namespaces with your own.

## That's all
If you want to join to develop this project - you are welcome in pull requests.
If you find smth not working you are welcome to issues.

Find me in <a href="https://t.me/davydkin_valery" target="_blank">telegram</a> or <a href="mailto:world-warm-worm@ya.ru" target="_blank">email</a>.