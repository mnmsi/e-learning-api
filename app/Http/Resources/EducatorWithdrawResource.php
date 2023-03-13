<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EducatorWithdrawResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        switch ($this->status) {
            case 1:
                $status = 'Approved';
                break;
            case 2:
                $status = 'Rejected';
                break;
            default:
                $status = 'Pending';
        }

        return [
            'id'              => $this->id,
            'bank_account_id' => $this->bank_account_id,
            'date'            => Carbon::parse($this->created_at)->format('d/m/y'),
            'amount'          => doubleval($this->amount),
            'status'          => $status
        ];
    }
}
