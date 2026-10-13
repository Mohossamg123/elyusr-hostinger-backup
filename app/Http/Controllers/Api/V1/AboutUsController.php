<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAboutUsPageRequest;
use App\Http\Requests\UpdateAboutUsSectionRequest;
use App\Models\AboutUsPage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AboutUsController extends Controller
{
    private array $sections = [
        'hero',
        'stats',
        'programs',
        'trust',
        'why-us',
        'values',
        'story',
        'vision-2030',
        'closing',
    ];

    private function sectionKey(string $section): string
    {
        return match ($section) {
            'why-us' => 'why_us',
            'vision-2030' => 'vision_2030',
            default => $section,
        };
    }

    private function page(): AboutUsPage
    {
        return AboutUsPage::firstOrFail();
    }

    private function responseData(AboutUsPage $page): array
    {
        return array_merge(
            ['id' => $page->id],
            $page->content ?? [],
            ['is_active' => $page->is_active]
        );
    }

    public function show(): JsonResponse
    {
        $page = AboutUsPage::where('is_active', true)->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'About Us page not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->responseData($page),
        ]);
    }

    public function adminShow(): JsonResponse
    {
        $page = $this->page();

        return response()->json([
            'success' => true,
            'data' => $this->responseData($page),
        ]);
    }

    public function section(string $section): JsonResponse
    {
        abort_unless(in_array($section, $this->sections, true), 404);

        $page = AboutUsPage::where('is_active', true)->first();

        if (!$page) {
            return response()->json([
                'success' => false,
                'message' => 'About Us page not found',
            ], 404);
        }

        $key = $this->sectionKey($section);

        return response()->json([
            'success' => true,
            'data' => [
                'section' => $section,
                'content' => $page->content[$key] ?? null,
            ],
        ]);
    }

    public function update(UpdateAboutUsSectionRequest $request, string $section): JsonResponse
    {
        abort_unless(in_array($section, $this->sections, true), 404);

        $page = DB::transaction(function () use ($request, $section) {
            $page = $this->page();

            $content = $page->content ?? [];
            $key = $this->sectionKey($section);

            $newSection = $request->validated('content');

            /*
             * Support image uploads for the Hero section.
             */
            if ($section === 'hero') {
                if ($request->hasFile('image')) {
                    if (!empty($content[$key]['image'])) {
                        Storage::disk('public')->delete($content[$key]['image']);
                    }

                    $newSection['image'] = $request->file('image')
                        ->store('about-us/hero', 'public');
                } elseif (array_key_exists('image', $newSection)) {
                    $newSection['image'] = $newSection['image'];
                } else {
                    $newSection['image'] = $content[$key]['image'] ?? null;
                }
            }

            /*
             * Support icons/images inside repeatable sections.
             */
            $repeatableSections = [
                'stats',
                'programs',
                'trust',
                'why-us',
                'values',
                'vision-2030',
            ];

            if (in_array($section, $repeatableSections, true) && is_array($newSection)) {
                foreach ($newSection as $index => &$item) {
                    if (!is_array($item)) {
                        continue;
                    }

                    if (isset($item['image']) && is_string($item['image'])) {
                        // Keep supplied existing image path.
                    }

                    if (isset($item['icon']) && is_string($item['icon'])) {
                        // Keep supplied existing icon path.
                    }
                }
                unset($item);
            }

            $content[$key] = $newSection;

            $page->update([
                'content' => $content,
                'is_active' => $request->boolean(
                    'is_active',
                    $page->is_active
                ),
            ]);

            return $page->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => 'About Us section updated successfully',
            'data' => [
                'section' => $section,
                'content' => $page->content[$this->sectionKey($section)] ?? null,
            ],
        ]);
    }

    public function updatePage(UpdateAboutUsPageRequest $request): JsonResponse
    {
        $page = DB::transaction(function () use ($request) {
            $page = $this->page();

            $page->update([
                'content' => $request->validated('content'),
                'is_active' => $request->boolean(
                    'is_active',
                    $page->is_active
                ),
            ]);

            return $page->fresh();
        });

        return response()->json([
            'success' => true,
            'message' => 'About Us page updated successfully',
            'data' => $this->responseData($page),
        ]);
    }
}
