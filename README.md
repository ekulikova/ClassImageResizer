# ImageResizerClass

PHP libraries to resize images and bunch of images in directories recursively.

## Installing

Use this sintax for install the library:
```
include '/path/to/ImageResizer.php';
```
Because this class uses namespacing, when instantiating the object, you need to either use the fully qualified namespace:
```
$resizer = \EKulikova\ImageResizer::getResizer($full_path[, $recursive]);
```
Or alias it:

```
use \EKulikova\ImageResizer;

$resizer = ImageResizer::getResizer($full_path[, $recursive]);
```
> Note:
This library uses GD class which do not support resizing animated gif files

###Resize###

```php
$resizer = ImageResizer::getResizer('image.jpg');
$resizer -> resize($new_width, $new_height) // or other resizing function.
        -> save([$file_name]); // or ->output();
```

### 1. Instantiating the object.

At first you need "resizer" object. The library has factory static method getResizer() for this purpose.

**getResizer( $full_path[, $recursive])**

**Parameters:**
$full_path - full path to your image or directory.
$recursive - optional boolean parameter. Defaults to true.
            If $recursive is false function resizes only images in the directory and doesn't go deeper.
            Otherwise function goes through all nested directories.

**Return values**
Returns instance of SingleImageResizer for single image or instance of RecursiveImageResizer for directory.

### 2. Resizing.

Then use one of the resizing functions.

**resize( $new_width, $new_height )**
It resizes directly, without keeping ratio and returns "resizer" object back.

**resizeToHeightWidth( $new_width, $new_height [, $skip_small] )**

It resizes an image to best fit a given set of dimensions (keeping aspect ratio) and returns "resizer" object back.

**Parameters:**
$new_width, $new_height - set of new dimensions.
$skip_small - optional boolean parameter to skip images that are smaller than given dimensions. Defaults to true.

**resizeToHeight( $new_height [, $skip_small] )
resizeToWidth( $new_width [, $skip_small] )**

It resizes an image according to one dimension (keeping aspect ratio) and returns "resizer" object back.

**Parameters:**
$new_width or $new_height - new dimension.
$skip_small - optional boolean parameter to skip images that are smaller than given dimensions. Defaults to true.

### 3. Save or output the result.

**save( [ $path ] )**

**Parameters:**
$path - optional parameter, full path for saving the result after resizing. By default (without parameter) it overwrites original images. If file or directory doesn't exist, it will be create.

**output()**

Outputs the raw image stream directly. Can be used only for single image.
