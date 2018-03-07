# ImageResizerClass

PHP library to resize images and bunch of images.

Use this sintax for install the library:
```
include '/path/to/ImageResizer.php';
```
Because this class uses namespacing, when instantiating the object, you need to either use the fully qualified namespace:
```
$image = new \EKulikova\ImageResizer();
```
Or alias it:

```
use \EKulikova\ImageResizer;

$image = new ImageResizer();
```
> Note:
This library uses GD class which do not support resizing animated gif files
