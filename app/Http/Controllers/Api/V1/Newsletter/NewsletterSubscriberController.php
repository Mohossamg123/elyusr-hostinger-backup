<?php

namespace App\Http\Controllers\Api\V1\Newsletter;

use App\Http\Controllers\Controller;
use App\Http\Requests\Newsletter\StoreNewsletterSubscriberRequest;
use App\Http\Requests\Newsletter\UpdateNewsletterSubscriberRequest;
use App\Http\Resources\Newsletter\NewsletterSubscriberResource;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterSubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsletterSubscriber::query()->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        return NewsletterSubscriberResource::collection(
            $query->paginate($request->integer('per_page', 10))
        );
    }

    public function statistics()
    {
        return response()->json([
            'total' => NewsletterSubscriber::count(),
            'confirmed' => NewsletterSubscriber::where('status', 'confirmed')->count(),
            'pending' => NewsletterSubscriber::where('status', 'pending')->count(),
            'unsubscribed' => NewsletterSubscriber::where('status', 'unsubscribed')->count(),
            'last_30_days' => NewsletterSubscriber::where('created_at', '>=', now()->subDays(30))->count(),
        ]);
    }

    public function store(StoreNewsletterSubscriberRequest $request)
    {
        $data = $request->validated();

        if (($data['status'] ?? 'confirmed') === 'confirmed') {
            $data['confirmed_at'] = now();
        }

        $data['confirmation_token'] = $data['status'] ?? 'confirmed' === 'pending'
            ? Str::random(64)
            : null;

        $subscriber = NewsletterSubscriber::create($data);

        return new NewsletterSubscriberResource($subscriber);
    }

    public function show(NewsletterSubscriber $newsletterSubscriber)
    {
        return new NewsletterSubscriberResource($newsletterSubscriber);
    }

    public function update(
        UpdateNewsletterSubscriberRequest $request,
        NewsletterSubscriber $newsletterSubscriber
    ) {
        $data = $request->validated();

        if (($data['status'] ?? null) === 'confirmed') {
            $data['confirmed_at'] = $newsletterSubscriber->confirmed_at ?? now();
            $data['unsubscribed_at'] = null;
            $data['confirmation_token'] = null;
        }

        if (($data['status'] ?? null) === 'unsubscribed') {
            $data['unsubscribed_at'] = now();
        }

        if (($data['status'] ?? null) === 'pending') {
            $data['confirmation_token'] = $newsletterSubscriber->confirmation_token ?? Str::random(64);
            $data['confirmed_at'] = null;
            $data['unsubscribed_at'] = null;
        }

        $newsletterSubscriber->update($data);

        return new NewsletterSubscriberResource($newsletterSubscriber->fresh());
    }

    public function destroy(NewsletterSubscriber $newsletterSubscriber)
    {
        $newsletterSubscriber->delete();

        return response()->json([
            'message' => 'Deleted Successfully'
        ]);
    }
}
