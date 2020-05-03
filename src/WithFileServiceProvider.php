<?php
namespace Withfile;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class WithFileServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerDirectives();
    }

    protected function registerDirectives()
    {
        Blade::directive('withfileScripts', function($expression) {
            return $this->js();
        });
    }

    protected function js()
    {
        return <<<EOT
<script data-turbolinks-eval="false">
document.addEventListener('livewire:load', function() {
    document.querySelectorAll('[wire\\\:withfile]').forEach((elm) => {
        var component_name = elm.closest('[wire\\\:id]').__livewire.name;
        elm.onchange = function() {
            var files = this.files;
            var attribute = elm.getAttribute('wire:withfile');

            if (files.length > 0) {
                var file = files[0];
                var reader = new FileReader();
                var file_send = {
                    name: file.name,
                    size: file.size,
                    type: file.type,
                    extension: file.name.substr(file.name.lastIndexOf('.'), 5),
                };

                reader.addEventListener("load", function(event) {
                    file_send.content = reader.result;

                    if (elm.hasAttribute('with-resize-if-image')) {
                        var width,height;
                        if (elm.hasAttribute('with-max-width')) {
                            width = elm.getAttribute('with-max-width');
                        }
                        if (elm.hasAttribute('with-max-height')) {
                            height = elm.getAttribute('with-max-height');
                        }
                        if (elm.hasAttribute('with-max-both')) {
                            width = elm.getAttribute('with-max-both');
                            height = elm.getAttribute('with-max-both');
                        }
                        if (file.type.match('image')) {
                            var file_type = file.type;
                            var oc = document.createElement('canvas'),
                                octx = oc.getContext('2d');
                            var img = new Image();
                            img.src = reader.result;
                            img.onload = function() {
                                if (img.width > img.height) {
                                    var per = img.height / img.width;
                                    if (width !== undefined) {
                                        var new_width = img.width > width ? width : img.width;
                                    } else {
                                        var new_width = img.width;
                                    }
                                    var new_height = per * new_width;
                                } else if (img.height > img.width) {
                                    var per = img.width / img.height;
                                    if (height !== undefined) {
                                        var new_height = img.height > height ? height : img.height;
                                    } else {
                                        var new_height = img.height;
                                    }
                                    var new_width = per * new_height;
                                }
                                oc.width = new_width;
                                oc.height = new_height;
                                octx.drawImage(img, 0, 0, oc.width, oc.height);
        
                                var dataURL = oc.toDataURL(file_type);
                                file_send.content = dataURL;
                                livewire.emitTo(component_name, 'processingFileUpload', attribute, file_send);
                            };
                        }
                    } else {
                        livewire.emitTo(component_name, 'processingFileUpload', attribute, file_send);
                    }
                });
                reader.readAsDataURL(file);
            }
        }
    });
});
</script> 
EOT;
    }
}