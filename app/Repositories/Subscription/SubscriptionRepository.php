<?php

namespace App\Repositories\Subscription;

use App\Exceptions\Exceptions;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Env;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\StripeClient;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    public function stripePayment($data)
    {
        Stripe::setApiKey(Env::get('STRIPE_SECRET'));
        $res = Charge::create([
            "amount"      => $data['amount'] * 100,
            "currency"    => Env::get('STRIPE_CURRENCY'),
            "source"      => $this->createToken($data),
            "description" => $data['description'] ?? $data['card_holder_name'],
        ]);

        if ($res->status === 'succeeded') {
            $data['status']        = 1;
            $data['stripe_charge'] = $res->id;
            return $this->model->create($data);
        } else {
            return false;
        }
    }

    private function createToken($data)
    {
        $stripe = new StripeClient(Env::get('STRIPE_SECRET'));
        return $stripe->tokens->create([
            'card' => [
                'number'    => $data['card_number'],
                'exp_month' => $data['expiry_month'],
                'exp_year'  => $data['expiry_year'],
                'cvc'       => $data['cvc'],
            ],
        ]);
    }

    public function refund($stripeCharge)
    {
        $stripe = new StripeClient(Env::get('STRIPE_SECRET'));
        $res    = $stripe->refunds->create([
            'charge' => $stripeCharge
        ]);

        if ($res->status === 'succeeded') {
            return true;
        }

        return false;
    }
}
