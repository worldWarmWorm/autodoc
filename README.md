Hou to use?
$objectToDocument = new MyClass();
(new JsonAutodoc($objectToDocument))->generate(
"The documentation of " . $objectToDocument::class . " endpoints",
'/path/to/file'
);

Usage examples
of /src/Api/Product/Controller/ProductController.php:41
/tests/EndpointAutodocTest.php:16

How did the library come about?

I was working on a very old project written in native php7.4. It did not have a composer dependency manager,
which means that if you need to install a library, you need to download the source folder to the project and manually
install its dependencies. At that moment, the task was to generate documentation of the methods of one of the APIs. For these needs
, I usually used the php-swagger library, but this project did not have such an opportunity. The task was very interesting,
and after completing it, I decided to put the solution in a separate world-warm-worm/autodoc library.

What can a library do?
Documents methods of any class, regardless of access modifiers, in two json and yaml formats. All you need to
do is call the constructor of the JsonAutodoc or YamlAutodoc class, throw an object of the class into it, the methods of which need
to be documented and comply with the requirement that the documented class implement the EndpointInterface interface.
Where can I call the constructor of the JsonAutodoc or YamlAutodoc class? It depends on your needs. For example, I'm in my project
The JsonAutodoc constructor was called in the constructor body of the class, which is an API for a third-party service.
This way, the documentation is reassembled every time the API endpoint is called and is always up to date.

What if my project is written on the symfony/laravel/yii framework?
The library is easily integrated. You can use this approach: if you work on any of the modern MVC
frameworks, then generate your own documentation file for each controller. Select another controller, where there will be a list
links to the documentation of all other controllers as in the example with the file index.php this library. This way it will be
possible for any of the colleagues on the project (not just the programmer) to have an idea of how the controllers in the project or
API work.

What if I want to use your library, but my project doesn't have composer?
Download the zip from my repository and unzip the library folder. To work with the library, you will only
need the /src/Autodoc folder, but it uses the namespaces specified in /composer.json in its files. The files may not be located in
your project. Most likely, you will need to replace the library's namespaces with your own.