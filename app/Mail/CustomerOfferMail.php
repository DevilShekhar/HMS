<?php

namespace App\Mail;

use App\Models\CustomerOffer;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomerOfferMail extends Mailable
{
    use SerializesModels;

    public $customer;

    public $offer;

    public $restaurant;

    public function __construct(User $customer, CustomerOffer $offer)
    {
        $this->customer = $customer;
        $this->offer = $offer;
        $this->restaurant = Restaurant::query()->find($offer->restaurant_id);
    }

    public function build()
    {
        return $this
            ->subject($this->offer->title)
            ->view('emails.customer-offer');
    }
}
