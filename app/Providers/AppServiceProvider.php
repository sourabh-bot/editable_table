<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
//        Form::component('check_box', '');
        \Form::macro('boot_checkbox', function ($name, $value = 1, $id= null, $class, $label){
            return '<label for="'.$name.'">'.$label.'
                <input id="'.$id.'" class="'.$class.'" name="'.$name.'" value="'.$value.'" type="checkbox">
                </label>';
        });

    }
}
