<?php

namespace App\Http\Controllers\Api\V1\Faq;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaqRequest;
use App\Http\Requests\UpdateFaqRequest;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::query()
            ->with('category')
            ->when(
                $request->filled('faq_category_id'),
                fn ($query) => $query->where(
                    'faq_category_id',
                    $request->faq_category_id
                )
            )
            ->when(
                $request->has('is_active'),
                fn ($query) => $query->where(
                    'is_active',
                    $request->boolean('is_active')
                )
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return FaqResource::collection($faqs);
    }

    public function store(StoreFaqRequest $request)
    {
        $faq = Faq::create($request->validated());

        $faq->load('category');

        return response()->json([
            'message' => 'تم إنشاء السؤال الشائع بنجاح',
            'data' => new FaqResource($faq),
        ], 201);
    }

    public function show(Faq $faq)
    {
        $faq->load('category');

        return new FaqResource($faq);
    }

    public function update(
        UpdateFaqRequest $request,
        Faq $faq
    ) {
        $faq->update($request->validated());

        $faq->load('category');

        return response()->json([
            'message' => 'تم تحديث السؤال الشائع بنجاح',
            'data' => new FaqResource($faq),
        ]);
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return response()->json([
            'message' => 'تم حذف السؤال الشائع بنجاح',
        ]);
    }
}