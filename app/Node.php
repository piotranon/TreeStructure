<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\User;

class Node extends Model
{
    protected $fillable = [
        'owner_id',//user id
        'parent_id',//parent node id
        'name',//node name
        'order',//order in subtree
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parentNode(): BelongsTo
    {
        return $this->belongsTo(Node::class);
    }

    public function childNodes(): HasMany
    {
        return $this->hasMany(Node::class,'parent_id','id');
        // return $this->hasMany(Node::class)->with(['childNodes']);
    }
}
