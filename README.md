# Livewire Uploading File

## Introduction

Do you have a problem for uploading file in livewire component? If yes, same with me.
If you use input with type file and implement `wire:model`, is that work for you to upload file? thats not work for now. Why this is happen? because livewire is send json data, not form data. I don't know this problem will gone in livewire future version. I research the solution for this problem and i found the solution for now. With this extensions, i hope this extensions will answer the problem.

## Installation

Install this using composer.

```composer
composer require zeroar/withfile
```

After you install, update your layouts and add `@withfileScripts` after `@livewireScripts`.

```blade
    ...

    @livewireScripts
    @withfileScripts

</body>
</html>
```

## Implementation Component

Now you can add this extension in your livewire component. To implement this, see the example below:
```php
namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Withfile\WithFile; // add this to implement

class UploadPhoto extends Component
{
use WithFile; // add this to implement
// this is target variable
// this variable will turn to array after upload file
public \$photo;

    // please implement this variable and add the variable target
    // if you not implement this, WithFile will not working
    public $fileable = [
        'photo'
    ];

    public function render() {
        return view('livewire.upload-photo');
    }

    // implement this method to edit or save the file
    public function updatedPhoto($value, $old_value)
    {
        // in this area, you can implement what you want for the file.
        // to save the file, i recommend to try this for save file in local

        $filename = $value['name'];
        // below for customize filename
        // $filename = "custom-file-name" . $value['extension'];
        Storage::disk('public')->put($filename, $this->raw($value));

        $this->photo = Storage::url($filename); // get the file url for saving to your database
    }
}
```

#### $fileable

This property is required. If you not implement this, your upload file will not working. So please implement this to your component.

#### updatedFoo($value, $old_value)

Updated method is same with livewire lifecycle hooks. But in livewire just only have one $value, in this i have add the $old_value.


## Implementation in view

In your blade file, you can implement looks like example below:

```blade
<div>
  @isset($photo)
  
  <img src="{{ $photo }}">
  
  @endisset
  
  Upload Photo
  <input type="file" wire:withfile="photo">

</div>

```

Or if you want to resize your image before send the file, below is example for this:

```blade
<div>
  @isset($photo)
  
  <img src="{{ $photo }}">
  
  @endisset
  
  Upload Photo
  <input type="file" wire:withfile="photo" with-resize-if-image with-max-width="1080" with-max-height="1080">

</div>
```

Or if you want to resize both of width and height, below is example for this:

```blade
<div>
  @isset($photo)
  
  <img src="{{ $photo }}">
  
  @endisset
  
  Upload Photo
  <input type="file" wire:withfile="photo" with-resize-if-image with-max-both="1080">

</div>
```

#### wire:withfile

Use this attribute looks like you use `wire:model`, but it's not same with `wire:model`. So, all in `wire:model` can't implemented to `wire:withfile`.

#### with-resize-if-image

This is will resize your file if you upload an image. Before file image send to your component, image will resized.

#### with-max-both

This will add the maximum of width and height. In example, if height greater than width then the height will be set to maximum and width will automatically resized.

#### with-max-width, with-max-height

Use this if you wan't to resize just height or width for the maximum of image width or height.

