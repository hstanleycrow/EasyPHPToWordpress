<h1 align="center">
  <br>
  Easy PHP To Wordpress
  <br>
</h1>

<h4 align="center">Free PHP Library to post from PHP to Wordpress</h4>

<p align="center">
  <a href="#key-features">Key Features</a> •
  <a href="#how-to-use">How To Use</a> •
  <a href="#download">Download</a> •
  <a href="#credits">Credits</a> •
  <a href="#license">License</a>
</p>

## Key Features

* Get Categories - Get a list of categories from Worpress site
* Get Tags - Get a list of tags from Worpress site
* Publish HTML content, including:
  - Featured Image
  - Category
  - Status: publish, draft, pending, etc
  - all images are uploaded to wordpress and the image URL is replaced by the Wordpress URL
  - You can include Youtube videos in the content
  - You can include Tweets in the content
* Wordpress URL Validator
* Wordpress credentials (user and application password) validator
* Easily handle errors

## How To Use

```bash
# Clone this repository
$ git clone https://github.com/hstanleycrow/EasyPHPToWordpress/

# install libraries
$ composer update

# or install using composer

$ composer require hstanleycrow/easyphptowordpress
```

```php
# define credentials
# You need a Wordpress application password https://make.wordpress.org/core/2020/11/05/application-passwords-integration-guide/
#This examples are using [DotEnv](https://github.com/vlucas/phpdotenv) to configure the credentials, but you can setup it as you want.
$wpSiteURL = $_ENV["WP_SITE_URL"];
$wpUsername = $_ENV["WP_USERNAME"];
$wpApplicationPassword = $_ENV["WP_APPLICATION_PASSWORD"];

# configure in your PHP script the timezone to the Wordpress timezone. This is important.
date_default_timezone_set("America/El_Salvador");

# create an object 
$obj = new WordpressAPI($wpSiteURL, $wpUsername, $wpApplicationPassword);
```

### Examples
```php
#Example to validate the URL

if ($obj->validateURL()) :
    echo "URL resolve 200";
else :
    echo $obj->errorMessage();
endif;

#Example how to get Wordpress Categories list

echo "<pre>";
if ($categories = $obj->categories()) :
    print_r($categories);
else :
    echo $obj->errorMessage();
endif;
echo "</pre>";

#Example how to get Wordpress Tags list

echo "<pre>";
if ($tags = $obj->tags()) :
    print_r($tags);
else :
    echo $obj->errorMessage();
endif;
echo "</pre>";

#Example how to validate credentials

echo "<pre>";
if ($obj->validateCredentials()) :
    echo "Valid Credentials" . PHP_EOL;
else :
    echo "Credentials not valid" . PHP_EOL;
    echo $obj->errorMessage() . PHP_EOL;
endif;
echo "</pre>";

#Example to publish into WP
$content = <<<HTML
<p><b> Hey</b> this is some text for the blog post</p>
<h2><a id="user-content-documentation" class="anchor" href="#user-content-documentation" rel="nofollow noindex noopener external ugc"></a>Documentation</h2>
<p>The documentation for this library is hosted at <a href="https://simplehtmldom.sourceforge.io/docs/" rel="nofollow noindex noopener external ugc">https://simplehtmldom.sourceforge.io/docs/</a></p>
<p><img src="https://eluniverso.space/wp-content/uploads/1143px-The_Sagittarius_dwarf_galaxy_in_Gaias_all-sky_view_ESA399651.jpg" alt="Sagitarius dwarf galaxy" title="Sagitarius" >
<h2>Youtube Video</h2>
<p>https://www.youtube.com/watch?v=K4TOrB7at0Y</p>
<h2>Tweet Thread</h2>
<p>https://twitter.com/elonmusk/status/1645266104351178752?s=20</p>
<p><img src="//eluniverso.space/wp-content/uploads/Positional-Schematic-of-the-Members-of-the-HR-8799-Exoplanet-System-777x777-1.jpg" alt="HR 8799 planet orbits" title="HR 8799 planet orbits">
<br><br>
<p><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAApgAAAKYB3X3/OAAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAANCSURBVEiJtZZPbBtFFMZ/M7ubXdtdb1xSFyeilBapySVU8h8OoFaooFSqiihIVIpQBKci6KEg9Q6H9kovIHoCIVQJJCKE1ENFjnAgcaSGC6rEnxBwA04Tx43t2FnvDAfjkNibxgHxnWb2e/u992bee7tCa00YFsffekFY+nUzFtjW0LrvjRXrCDIAaPLlW0nHL0SsZtVoaF98mLrx3pdhOqLtYPHChahZcYYO7KvPFxvRl5XPp1sN3adWiD1ZAqD6XYK1b/dvE5IWryTt2udLFedwc1+9kLp+vbbpoDh+6TklxBeAi9TL0taeWpdmZzQDry0AcO+jQ12RyohqqoYoo8RDwJrU+qXkjWtfi8Xxt58BdQuwQs9qC/afLwCw8tnQbqYAPsgxE1S6F3EAIXux2oQFKm0ihMsOF71dHYx+f3NND68ghCu1YIoePPQN1pGRABkJ6Bus96CutRZMydTl+TvuiRW1m3n0eDl0vRPcEysqdXn+jsQPsrHMquGeXEaY4Yk4wxWcY5V/9scqOMOVUFthatyTy8QyqwZ+kDURKoMWxNKr2EeqVKcTNOajqKoBgOE28U4tdQl5p5bwCw7BWquaZSzAPlwjlithJtp3pTImSqQRrb2Z8PHGigD4RZuNX6JYj6wj7O4TFLbCO/Mn/m8R+h6rYSUb3ekokRY6f/YukArN979jcW+V/S8g0eT/N3VN3kTqWbQ428m9/8k0P/1aIhF36PccEl6EhOcAUCrXKZXXWS3XKd2vc/TRBG9O5ELC17MmWubD2nKhUKZa26Ba2+D3P+4/MNCFwg59oWVeYhkzgN/JDR8deKBoD7Y+ljEjGZ0sosXVTvbc6RHirr2reNy1OXd6pJsQ+gqjk8VWFYmHrwBzW/n+uMPFiRwHB2I7ih8ciHFxIkd/3Omk5tCDV1t+2nNu5sxxpDFNx+huNhVT3/zMDz8usXC3ddaHBj1GHj/As08fwTS7Kt1HBTmyN29vdwAw+/wbwLVOJ3uAD1wi/dUH7Qei66PfyuRj4Ik9is+hglfbkbfR3cnZm7chlUWLdwmprtCohX4HUtlOcQjLYCu+fzGJH2QRKvP3UNz8bWk1qMxjGTOMThZ3kvgLI5AzFfo379UAAAAASUVORK5CYII="></p>
<p><img src="https://dev.w3.org/SVG/tools/svgweb/samples/svg-files/DroidSans-Bold.svg" alt="SVG Image">
HTML;
$featureImagePath = "https://eluniverso.space/wp-content/uploads/Three-merging-galaxies-1-1024x511.jpg";
$categories = [58];
echo "<pre>";
if ($url = $obj->publishPost(
    "Title of the post",
    $content,
    $categories,
    $featureImagePath
)) :
    echo $url . PHP_EOL;
    if ($obj->hasImagesErrors()) :
        print_r($obj->imagesError());
    endif;
else :
    #echo "Credentials not valid" . PHP_EOL;
    echo $obj->errorMessage() . PHP_EOL;
endif;
echo "</pre>";

```

## Limitations
* For now, you can't add tags to the post but it is considered for future versions.

## Download

You can [download](https://github.com/hstanleycrow/EasyPHPToWordpress/) the latest version here.

## PHP Versions
I have tested this class only in this PHP versions. So, if you have an older version and do not work, let me know.
| PHP Version |
| ------------- |
| PHP 8.0 | 
| PHP 8.1 |
| PHP 8.2 |

## Credits

This software uses the following open source packages:

- [PHP Simple HTML DOM Parser](https://simplehtmldom.sourceforge.io/docs/1.9/index.html)

## Support

<a href="https://www.buymeacoffee.com/haroldcrow" target="_blank"><img src="https://www.buymeacoffee.com/assets/img/custom_images/purple_img.png" alt="Buy Me A Coffee" style="height: 41px !important;width: 174px !important;box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;-webkit-box-shadow: 0px 3px 2px 0px rgba(190, 190, 190, 0.5) !important;" ></a>

## License

MIT

---

> [www.hablemosdeseo.net](https://www.hablemosdeseo.net) &nbsp;&middot;&nbsp;
> GitHub [@hstanleycrow](https://github.com/hstanleycrow) &nbsp;&middot;&nbsp;
> Twitter [@harold_crow](https://twitter.com/harold_crow)

