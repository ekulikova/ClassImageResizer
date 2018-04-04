# ImageResizerClass

PHP libraries to resize images and bunch of images recursively.

ImageResizer.php is library to resize image in different ways.
RecursiveImageResizer.php uses the previous library and does the same things recursively.

##ImageResizer.php##

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

Resize
------

At first you can resize directly, without keeping ratio.
function save() has optional parameter $file_name, by default it uses original image's name:

```php
$image = new ImageResizer('image.jpg');
$image->resize($new_width, $new_height);
$image->save([$file_name]);
```

To resize an image to best fit a given set of dimensions (keeping aspect ratio)
The function has optional parameter $skip_small to skip images that are smaller than new dimension. By default the parameter is true:

```php
$image = new ImageResizer('image.jpg');
$image->resizeToHeightWidth($new_width, $new_height [, $skip_small]);
$image->save([$file_name]);
```

To resize an image according to one dimension (keeping aspect ratio):

```php
$image = new ImageResizer('image.jpg');
$image->resizeToHeight($new_height [, $skip_small]);
$image->save([$file_name]);

$image = new ImageResizer('image.jpg');
$image->resizeToWidth($new_width [, $skip_small]);
$image->save([$file_name]);
```

###RecursiveImageResizer.php###

Use this sintax for install the library:
```
include '/path/to/RecursiveImageResizer.php';
```
Because this class uses namespacing, when instantiating the object, you need to either use the fully qualified namespace:
```
$dir = new \EKulikova\RecursiveImageResizer();
```
Or alias it:

```
use \EKulikova\RecursiveImageResizer;

$dir = new RecursiveImageResizer();
```
> Note:
This library uses GD class which do not support resizing animated gif files

Resize
------

At first you can resize directly, without keeping ratio.
###Images with new dementions overwritten original images###
The function has optional bollean parameter $recursive to go recursively.
If $recursive=0 function resizes only images in the directory and doesn't go deeper.
If $recursive=1 function goes throught all nested directories.

```php
$rec = new RecursiveImageResizer($dirName);
$rec->resize($new_width, $new_height[, $recursive]);
```

To resize an image to best fit a given set of dimensions (keeping aspect ratio)
###Images with new dementions overwritten original images###
The function has optional bollean parameter $recursive to go recursively.
If $recursive=0 function resizes only images in the directory and doesn't go deeper.
If $recursive=1 function goes throught all nested directories.

```php
$rec = new RecursiveImageResizer($dirName);
$rec->resizeToHeightWidth($new_width, $new_height[, $recursive]);
```

To resize an image according to one dimension (keeping aspect ratio):
###Images with new dementions overwritten original images###
The function has optional bollean parameter $recursive to go recursively.
If $recursive=0 function resizes only images in the directory and doesn't go deeper.
If $recursive=1 function goes throught all nested directories.

```php
$rec = new RecursiveImageResizer($dirName);
$rec->resizeToHeight($new_height[, $recursive]);

$rec = new RecursiveImageResizer($dirName);
$rec->resizeToWidth($new_width, [, $recursive]);
```
