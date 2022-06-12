<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Dyrynda\Database\Casts\EfficientUuid;
use Dyrynda\Database\Support\GeneratesUuid;

class SettingStore extends Model implements HasMedia
{
    use GeneratesUuid;
    use HasFactory;
    use InteractsWithMedia;    

    
    public function registerMediaCollections() : void {
        $this
          ->addMediaCollection('logo')
          ->singleFile();

        $this
          ->addMediaCollection('favicon')
          ->singleFile();
      }
  
      public function registerMediaConversions(Media $media = null) : void {
          $this
            ->addMediaConversion('logo')
            ->width(512)
            ->height(512)
            ->performOnCollections('logo');
            
          $this
            ->addMediaConversion('favicon')
            ->width(512)
            ->height(512)
            ->performOnCollections('favicon');
      }
}
