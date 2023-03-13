<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReviewListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'status'       => true,
            'total_rated'  => $this->count(),
            'sum_of_rate'  => $this->sum('rate'),
            'total_rating' => $this->calculateTotalRating(),
            'data'         => $this->collection,
        ];
    }

    public function getOnlyReview()
    {
        return [
            'total_rated'  => $this->count(),
            'sum_of_rate'  => $this->sum('rate'),
            'total_rating' => $this->calculateTotalRating(),
        ];
    }

    public function calculateTotalRating()
    {
        if ($this->count() > 0) {
            return $this->sum('rate') / $this->count();
        } else {
            return 0;
        }
    }
}
