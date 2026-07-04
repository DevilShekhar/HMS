<?php

namespace App\Http\Controllers;

use App\Mail\CustomerOfferMail;
use App\Models\CustomerOffer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CustomerOfferController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $offers = CustomerOffer::query();

        if ($user->role == 'owner') {

            $offers->where(
                'restaurant_id',
                $user->restaurant_id
            );

        } elseif ($user->role == 'branch_manager') {

            $offers->where(
                'restaurant_id',
                $user->restaurant_id
            )->where(
                'branch_id',
                $user->branch_id
            );

        }

        $offers = $offers
            ->latest()
            ->paginate(10);

        return view(
            'admin.customer-offers.index',
            compact('offers')
        );
    }

    public function create()
    {
        $user = Auth::user();

        $customerCount = User::query()
            ->where('role', 'customer')
            ->when($user->restaurant_id, function ($q) use ($user) {
                $q->where('restaurant_id', $user->restaurant_id);
            })
            ->when($user->branch_id, function ($q) use ($user) {
                $q->where('branch_id', $user->branch_id);
            })
            ->count();

        return view('admin.customer-offers.create', compact('customerCount'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'title' => [
                'required',
                Rule::unique('customer_offers')->where(function ($query) use ($request) {
                    return $query->where('category', $request->category);
                }),
            ],
            'description' => 'required',
            'category' => 'required',
        ], [
            'title.unique' => 'This title already exists in the selected category.',
        ]);

        CustomerOffer::create([
            'restaurant_id' => Auth::user()->restaurant_id,
            'branch_id' => Auth::user()->branch_id,
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'status'        => 1,
        ]);

        // Branch Manager
        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.customer-offers.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'Customer Offers created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.customer-offers.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'Customer Offers successfully.');
        }
    }

    public function edit($restaurant, $branch = null, ?CustomerOffer $customerOffer = null)
    {
        // if branch is missing Laravel sends CustomerOffer as second argument
        if ($customerOffer === null && $branch instanceof CustomerOffer) {
            $customerOffer = $branch;
            $branch = null;
        }

        return view(
            'admin.customer-offers.edit',
            [
                'offer' => $customerOffer,
            ]
        );
    }

    public function update(Request $request, CustomerOffer $customerOffer)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable',
        ]);

        $customerOffer->update([
            'title' => $request->title,
            'description' => $request->description,
            'category' => $request->category,
            'status' => $request->has('status'),
        ]);

        if (Auth::user()->branch_id) {

            return redirect()
                ->route('branch.customer-offers.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                    'branch' => Auth::user()->branch?->slug,
                ])
                ->with('success', 'Customer Offers created successfully.');
        }

        // Owner
        if (Auth::user()->role === 'owner') {
            return redirect()
                ->route('restaurant.customer-offers.index', [
                    'restaurant' => Auth::user()->restaurant?->slug,
                ])
                ->with('success', 'Customer Offers successfully.');
        }
    }

    public function show($restaurant, $branch = null, ?CustomerOffer $customerOffer = null)
    {
        // if branch is missing we can sensd CustomerOffer as second argument
        if ($customerOffer === null && $branch instanceof CustomerOffer) {
            $customerOffer = $branch;
            $branch = null;
        }
        $offer = $customerOffer;

        return view(
            'admin.customer-offers.show',
            compact('offer')
        );
    }

    public function destroy($restaurant, $branch = null, ?CustomerOffer $customerOffer = null)
    {
        // if branch is missing Laravel sends CustomerOffer as second argument
        if ($customerOffer === null && $branch instanceof CustomerOffer) {
            $customerOffer = $branch;
            $branch = null;
        }
        $customerOffer->update([
            'status' => 0,
        ]);

        return back()->with(
            'success',
            'Offer deleted successfully.'
        );
    }

    public function registeredCustomers()
    {
        $user = Auth::user();

        $query = Order::query()
            ->select(
                'customer_name',
                'mobile_number',
                'email',
                'birth_date',
                'anniversary_date',
                DB::raw('COUNT(id) as total_visits'),
                DB::raw('MAX(created_at) as last_visit')
            )
            ->whereNotNull('mobile_number');

        // restaurant wise
        if ($user->restaurant_id) {
            $query->where(
                'restaurant_id',
                $user->restaurant_id
            );
        }

        // branch wise
        if ($user->branch_id) {
            $query->where(
                'branch_id',
                $user->branch_id
            );
        }

        $customers = $query
            ->groupBy(
                'customer_name',
                'mobile_number',
                'email',
                'birth_date',
                'anniversary_date'
            )
            ->orderByDesc('last_visit')
            ->get();

        // attach registered customer id if exists
        $customers = $customers->map(function ($customer) {

            $registeredUser = User::query()->where('phone', $customer->mobile_number)
                ->where('role', 'customer')
                ->first();

            $customer->id = $registeredUser?->id;

            // If registered user has details, prefer them
            if ($registeredUser) {

                $customer->birth_date = $registeredUser->birth_date
                    ?? $customer->birth_date;

                $customer->anniversary_date = $registeredUser->anniversary_date
                    ?? $customer->anniversary_date;

                $customer->email = $registeredUser->email
                    ?? $customer->email;
            }

            return $customer;
        });

        return view(
            'admin.customer-offers.user-list',
            compact('customers')
        );
    }

    public function sendOffer(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required',
            'category' => 'required|in:birthday,anniversary,other',
            'other_description' => 'required|string|min:10',
        ]);

        $customer = User::query()->where('phone', $request->mobile_number)
            ->where('role', 'customer')
            ->first();

        if (! $customer) {
            $order = Order::query()->where('mobile_number', $request->mobile_number)
                ->latest()
                ->first();

            if (! $order || ! $order->email) {
                return back()->with('error', 'Customer email not available.');
            }

            $customer = new User([
                'name' => $order->customer_name,
                'phone' => $order->mobile_number,
                'email' => $order->email,
                'birth_date' => $order->birth_date,
                'anniversary_date' => $order->anniversary_date,
            ]);
        }

        $message = $request->description;

        if ($request->category !== 'other') {
            $offer = CustomerOffer::query()->where('category', $request->category)
                ->where('status', 1)
                ->latest()
                ->first();

            if ($offer) {
                $message = $offer->description;
            }
        }

        if (empty($message)) {
            return back()->with('error', 'Offer message is required.');
        }

        // Send Email
        Mail::to($customer->email)->send(new CustomerOfferMail($customer, $message, $request->category));

        return redirect()->back()->with('success', 'Offer sent successfully!');
    }
}
