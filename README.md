# LeapFrog for Laravel

Hate writing CRUD code from scratch? Don't want to memorize any more crazy commands?

LeapFrog is a UI-based CRUD boilerplate generator for Laravel. It allows you to quickly create files according to the Controller-Service-Model pattern. Simply point your browser to __your-project.dev/leapfrog__ and go!

For example, if you are creating a new "Truck" entity, it will generate or edit the following files for you:

* routes/__web.php__
* app/Models/__Truck.php__
* app/Http/Controllers/__TruckController.php__
* app/Services/__TruckService.php__
* app/Http/Requests/__TruckCreateRequest.php__
* app/Http/Requests/__TruckUpdateRequest.php__
* database/migrations/__xxxx_xx_xx_xxxxxx_create_trucks_table.php__
* resources/views/__truck/index.blade.php__
* resources/views/__truck/create.blade.php__
* resources/views/__truck/edit.blade.php__
* config/forms/__truck.php__

The interface allows you to pick which files you want to create and even customize the paths (to some extent). 

[Click here](https://www.jimhlad.com/leapfrog/screenshots) for screenshots.

## Prerequisites

This package depends on two other separately managed packages being installed:

* [Laravel 5 Extended Generators](https://github.com/laracasts/Laravel-5-Generators-Extended) - An awesome migration generator package by Jeffrey Way
* [FormMaker](https://github.com/YABhq/Formmaker) - Another awesome form generator package by Yab Inc.

Please see the above GitHub pages for instructions on installing these packages.

NOTE: You may need the __dev-master__ version of Laravel 5 Extended Generators if you receive an error while trying to create migration files.

## Usage

### Step 1: Install with composer

Install the package using composer:

`composer require jimhlad/leapfrog --dev`

Add the following to the __config/app.php__ in the providers array:

`JimHlad\LeapFrog\LeapFrogServiceProvider::class`

Publish the assets by running:

`php artisan vendor:publish --provider="JimHlad\LeapFrog\LeapFrogServiceProvider"`

### Step 2: Update RouteServiceProvider

Update app/Providers/RouteServiceProvider.php to include the __routes/leapfrog.php__ file by changing:

```php
->group(base_path('routes/web.php'));
```

to 

```php
->group(function() {
    require base_path('routes/web.php');
    require base_path('routes/leapfrog.php');
});
```

NOTE: You should modify __routes/leapfrog.php__ to ensure that these routes are _only accessible within your local environment_. This package is for development purposes only.

### Step 3: Create app layout

The views generated by this package assume the existence of a __views/layouts/app.blade.php__ file. We can generate this by running the standard:

`php artisan make:auth`

### That's it!

You should now be able to point your browser to __your-project.dev/leapfrog__ to see the LeapFrog dashboard.

## Author

If you have any questions please feel free to reach out to me (Jim Hlad) on Twitter: [@jimhlad](https://twitter.com/jimhlad)

## License

LeapFrog is open source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Bug Reporting and Feature Requests

Please be as detailed as possible when submitting bug reports or feature requests.

### Disclaimer

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
