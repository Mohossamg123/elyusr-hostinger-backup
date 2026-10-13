<?php

namespace App\Http\Controllers\Api\V1\Faq;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFaqCategoryRequest;
use App\Http\Requests\UpdateFaqCategoryRequest;
use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;
use Illuminate\Http\Request;

class FaqCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = FaqCategory::query()
            ->withCount('faqs')
            ->when(
                $request->has('is_active'),
                fn ($query) => $query->where('is_active', $request->boolean('is_active'))
            )
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return FaqCategoryResource::collection($categories);
    }

    public function store(StoreFaqCategoryRequest $request)
    {
        $category = FaqCategory::create($request->validated());

        return response()->json([
            'message' => 'تم إنشاء تصنيف الأسئلة الشائعة بنجاح',
            'data' => new FaqCategoryResource($category),
        ], 201);
    }

    public function show(FaqCategory $faqCategory)
    {
        $faqCategory->loadCount('faqs');

        return new FaqCategoryResource($faqCategory);
    }

    public function update(
        UpdateFaqCategoryRequest $request,
        FaqCategory $faqCategory
    ) {
        $faqCategory->update($request->validated());

        return response()->json([
            'message' => 'تم تحديث تصنيف الأسئلة الشائعة بنجاح',
            'data' => new FaqCategoryResource($faqCategory),
        ]);
    }

    public function destroy(FaqCategory $faqCategory)
    {
        $faqCategory->delete();

        return response()->json([
            'message' => 'تم حذف تصنيف الأسئلة الشائعة بنجاح',
        ]);
    }
}