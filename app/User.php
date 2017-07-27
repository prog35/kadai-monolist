<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];
    
    
    public function items()
    {
        return $this->belongsToMany(Item::class)->withPivot('type')->withTimestamps();
    }
    
    public function want_items()
    {
        return $this->items()->where('type', 'want');
    }
    
    public function want($itemId)
    {
        $exist = $this->is_wanting($itemId);
        
        if ($exist) {
            return false;
        } else {
            $this->items()->attach($itemId, ['type' => 'want']);
            return true;
        }
    }
    
    public function dont_want($itemId)
    {
        $exist = $this->is_wanting($itemId);
        
        if ($exist) {
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'want'", [\Auth::user()->id, $itemId]);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_wanting($itemIdOrCode)
    {
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->want_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_Code_exists = $this->want_items()->where ('code', $itemIdOrCode)->exists();
            return $item_Code_exists;
        }
    }
    
    public function have_items()
    {
        return $this->items()->where('type', 'have');
    }
    
    public function have($itemId) 
    {
         $exist = $this->is_having($itemId);
        
        if ($exist) {
            return false;
        } else {
            $this->items()->attach($itemId, ['type' => 'have']);
            return true;
        }   
    }
    
    public function dont_have($itemId)
    {
        $exist = $this->is_having($itemId);
        
        if ($exist) {
            \DB::delete("DELETE FROM item_user WHERE user_id = ? AND item_id = ? AND type = 'have'", [\Auth::user()->id, $itemId]);
            return true;
        } else {
            return false;
        }
    }
    
    public function is_having($itemIdOrCode)
    {
        if (is_numeric($itemIdOrCode)) {
            $item_id_exists = $this->have_items()->where('item_id', $itemIdOrCode)->exists();
            return $item_id_exists;
        } else {
            $item_Code_exists = $this->have_items()->where('code', $itemIdOrCode)->exists();
            return $item_Code_exists;
        }
    }
}
