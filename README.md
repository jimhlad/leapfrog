# LeapFrog for Laravel

Hate writing CRUD code from scratch? Don't want to memorize any more crazy commands?

LeapFrog is a UI-based CRUD boilerplate generator for Laravel. It allows you to quickly create files according to the Controller-Service-Model pattern.

For example, if you are creating a new "Truck" entity, it will generate or edit the following files for you:

* routes/__web.php__
* app/Models/__Truck.php__
* app/Http/Controllers/__TruckController.php__
* app/Services/__TruckService.php__
* app/Http/Requests/__TruckCreateRequest.php__
* app/Http/Requests/__TruckUpdateRequest.php__
* database/migrations/__xxxx_xx_xx_xxxxxx_create_xxxxxx_table.php__
* resources/views/__truck/index.blade.php__
* resources/views/__truck/create.blade.php__
* resources/views/__truck/edit.blade.php__
* config/forms/__truck.php__

The interface allows you to pick which files you want to create and even customize the paths to some extent. 

[Click here](https://www.jimhlad.com/) for screenshots.

## Usage

### Step 1: Install with composer

Some info here.

### Step 2: A second step

Some info here.

## License

LeapFrog is open source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

### Bug Reporting and Feature Requests

Please be as detailed as possible when submitting bug reports or feature requests.

### Disclaimer

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
