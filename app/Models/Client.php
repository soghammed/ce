<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Client extends Model
{
    protected $fillable=['first_name', 'last_name', 'avatar', 'email'];
    use HasFactory;

    static function SaveAvatar(Request $request){
    	$img_file = $request->file('avatar');
    	$storagePath = \Storage::disk('public')->put('', $img_file);
    	$storageName = basename($storagePath);
    	$url = ltrim(\Storage::url($storagePath), '/');
    	return $url;
    }
}
