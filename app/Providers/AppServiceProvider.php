<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('max_uploaded_file_size', function ($attribute, $value, $parameters, $validator) {
            
            $total_size = array_reduce($value, function ( $sum, $item ) { 
                // each item is UploadedFile Object
                $sum += filesize($item->path()); 
                return $sum;
            });

            // $parameters[0] in kilobytes
            return $total_size < $parameters[0] * 1024; 

        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
