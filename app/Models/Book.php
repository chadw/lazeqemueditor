<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Kyslik\ColumnSortable\Sortable;

class Book extends BaseModel
{
    use Sortable;

    protected $connection = 'eqemu';
    protected $table = 'books';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'txtfile',
        'language',
    ];

    public array $sortable = [
        'id',
        'name',
    ];

    public function item(): HasMany
    {
        return $this->hasMany(Item::class, 'filename', 'name')
            ->whereNotNull('filename')
            ->where('filename', '!=', '')
            ->select('id', 'Name', 'icon', 'filename');
    }
}
