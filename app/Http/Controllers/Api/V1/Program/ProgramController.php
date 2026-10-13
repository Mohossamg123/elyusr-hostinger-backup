<?php

namespace App\Http\Controllers\Api\V1\Program;

use App\Http\Controllers\Controller;
use App\Http\Requests\Program\StoreProgramRequest;
use App\Http\Requests\Program\UpdateProgramRequest;
use App\Http\Resources\ProgramResource;
use App\Models\Program;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProgramController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Index
    |--------------------------------------------------------------------------
    | جلب البرامج
    */

    public function index(Request $request)
    {
        $query = Program::with([
            'axis',
            'seasons',

            'donationPackages' => function ($query) {
                $query->orderBy('sort_order');
            },

            'cases',
        ])->latest();

        if ($request->filled('axis_id')) {
            $query->where(
                'axis_id',
                $request->axis_id
            );
        }

        if ($request->filled('season_id')) {
            $query->whereHas(
                'seasons',
                function ($q) use ($request) {
                    $q->where(
                        'seasons.id',
                        $request->season_id
                    );
                }
            );
        }

        if ($request->has('is_active')) {
            $query->where(
                'is_active',
                (bool) $request->is_active
            );
        }

        if ($request->filled('status')) {
            $query->where(
                'status',
                $request->status
            );
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where(
                    'name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'name_ar',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'name_en',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'slug',
                    'like',
                    "%{$search}%"
                );
            });
        }

        return ProgramResource::collection(
            $query->paginate(10)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    | إنشاء برنامج
    |--------------------------------------------------------------------------
    */

    public function store(
        StoreProgramRequest $request
    ) {
        $data = $request->validated();

        $seasonIds = $data['season_ids'] ?? [];

        unset($data['season_ids']);

        $data['status'] =
            $data['status'] ?? 'draft';

        $data['name'] =
            $data['title_ar']
            ?? $data['name_ar']
            ?? 'Program';

        $data['description'] =
            $data['description_ar']
            ?? $data['description_en']
            ?? null;

        $data['name_ar'] =
            $data['name_ar']
            ?? $data['title_ar']
            ?? null;

        $data['name_en'] =
            $data['name_en']
            ?? $data['title_en']
            ?? null;

        if ($request->hasFile('image')) {
            $data['image'] =
                $request->file('image')
                    ->store(
                        'programs',
                        'public'
                    );
        }

        unset(
            $data['title_ar'],
            $data['title_en']
        );

        $program = Program::create($data);

        if (!empty($seasonIds)) {
            $program
                ->seasons()
                ->sync($seasonIds);
        }

        // Activity Log
        app(ActivityLogService::class)->created(
            $program,
            'إنشاء برنامج جديد: ' . $program->name,
            [
                'name' => $program->name,
                'name_ar' => $program->name_ar,
                'name_en' => $program->name_en,
                'status' => $program->status,
                'season_ids' => $seasonIds,
            ]
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Program created successfully',
            'data' => new ProgramResource(
                $program->load([
                    'axis',
                    'seasons',
                    'donationPackages',
                    'cases',
                ])
            ),
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    | جلب برنامج واحد
    |--------------------------------------------------------------------------
    */

    public function show(
        Program $program
    ) {
        return new ProgramResource(
            $program->load([
                'axis',
                'seasons',
                'donationPackages' => function ($query) {
                    $query->orderBy(
                        'sort_order'
                    );
                },
                'cases',
            ])
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    | تعديل البرنامج
    |--------------------------------------------------------------------------
    */

    public function update(
        UpdateProgramRequest $request,
        Program $program
    ) {
        $data = $request->validated();

        $seasonIds =
            $data['season_ids'] ?? null;

        unset($data['season_ids']);

        $data['status'] =
            $data['status'] ?? 'draft';

        if (isset($data['title_ar'])) {
            $data['name_ar'] =
                $data['title_ar'];

            $data['name'] =
                $data['title_ar'];

            unset($data['title_ar']);
        }

        if (isset($data['title_en'])) {
            $data['name_en'] =
                $data['title_en'];

            unset($data['title_en']);
        }

        if (isset($data['description_ar'])) {
            $data['description'] =
                $data['description_ar'];
        }

        if ($request->hasFile('image')) {
            if ($program->image) {
                Storage::disk('public')
                    ->delete(
                        $program->image
                    );
            }

            $data['image'] =
                $request->file('image')
                    ->store(
                        'programs',
                        'public'
                    );
        }

        // Save old values before update
        $oldData = [
            'name' => $program->name,
            'name_ar' => $program->name_ar,
            'name_en' => $program->name_en,
            'status' => $program->status,
            'image' => $program->image,
            'season_ids' => $program->seasons()
                ->pluck('seasons.id')
                ->values()
                ->all(),
        ];

        $program->update($data);

        if ($seasonIds !== null) {
            $program
                ->seasons()
                ->sync($seasonIds);
        }

        // Activity Log
        app(ActivityLogService::class)->updated(
            $program,
            'تعديل البرنامج: ' . $program->name,
            [
                'old' => $oldData,
                'new' => [
                    'name' => $program->name,
                    'name_ar' => $program->name_ar,
                    'name_en' => $program->name_en,
                    'status' => $program->status,
                    'image' => $program->image,
                    'season_ids' => $seasonIds !== null
                        ? $seasonIds
                        : $oldData['season_ids'],
                ],
            ]
        );

        return response()->json([
            'success' => true,
            'message' =>
                'Program updated successfully',
            'data' => new ProgramResource(
                $program
                    ->fresh()
                    ->load([
                        'axis',
                        'seasons',
                        'donationPackages' => function ($query) {
                            $query->orderBy(
                                'sort_order'
                            );
                        },
                        'cases',
                    ])
            ),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Destroy
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Program $program
    ) {
        // Save information before deletion
        $programName = $program->name;
        $programId = $program->id;

        if ($program->image) {
            Storage::disk('public')
                ->delete(
                    $program->image
                );
        }

        // Activity Log
        app(ActivityLogService::class)->deleted(
            $program,
            'حذف البرنامج: ' . $programName,
            [
                'deleted_program_id' => $programId,
                'deleted_program_name' => $programName,
                'status' => $program->status,
            ]
        );

        $program->delete();

        return response()->json([
            'success' => true,
            'message' =>
                'Program deleted successfully',
        ]);
    }
}