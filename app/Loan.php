<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'borrow_date' => 'date',
        'return_date' => 'date',
    ];

    /**
     * Get the user that made the loan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the commodity being borrowed.
     */
    public function commodity()
    {
        return $this->belongsTo(Commodity::class);
    }
}
