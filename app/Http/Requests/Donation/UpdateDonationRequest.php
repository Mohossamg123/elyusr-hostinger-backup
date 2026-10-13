<?php

namespace App\Http\Requests\Donation;

use App\Models\CaseModel;
use App\Models\ProgramDonationPackage;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'donor_id' => [
                'required',
                'integer',
                'exists:donors,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Donation target
            |--------------------------------------------------------------------------
            */

            'program_id' => [
                'nullable',
                'integer',
                'exists:programs,id',
            ],

            'project_id' => [
                'nullable',
                'integer',
                'exists:projects,id',
            ],

            'donation_campaign_id' => [
                'nullable',
                'integer',
                'exists:donation_campaigns,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Case
            |--------------------------------------------------------------------------
            */

            'case_id' => [
                'nullable',
                'integer',
                'exists:case_models,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Donation package
            |--------------------------------------------------------------------------
            */

            'donation_package_id' => [
                'nullable',
                'integer',
                'exists:program_donation_packages,id',
            ],

            /*
            |--------------------------------------------------------------------------
            | Payment
            |--------------------------------------------------------------------------
            */

            'payment_method_id' => [
                'required',
                'integer',
                'exists:payment_methods,id',
            ],

            'payment_reference' => [
                'nullable',
                'string',
                'max:255',
            ],

            'payment_receipt' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'max:5120',
            ],

            'payment_details' => [
                'nullable',
                'array',
            ],

            /*
            |--------------------------------------------------------------------------
            | Amount
            |--------------------------------------------------------------------------
            */

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],

            /*
            |--------------------------------------------------------------------------
            | Donation status
            |--------------------------------------------------------------------------
            |
            | Kept for frontend compatibility.
            | DonationController prevents changing the status manually.
            |
            */

            'status' => [
                'nullable',
                'string',
                'in:pending,paid,failed,cancelled,refunded',
            ],

            /*
            |--------------------------------------------------------------------------
            | Pickup
            |--------------------------------------------------------------------------
            */

            'pickup_governorate' => [
                'nullable',
                'string',
                'max:255',
            ],

            'pickup_city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'pickup_address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'pickup_preferred_time' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | Legacy payment method field
            |--------------------------------------------------------------------------
            */

            'payment_method' => [
                'nullable',
                'in:cash,visa,bank',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            /*
            |--------------------------------------------------------------------------
            | Exactly one donation target
            |--------------------------------------------------------------------------
            */

            $targets = collect([
                'program_id' =>
                    $this->input('program_id'),

                'project_id' =>
                    $this->input('project_id'),

                'donation_campaign_id' =>
                    $this->input('donation_campaign_id'),
            ])->filter(
                fn ($value) => filled($value)
            );

            if ($targets->count() !== 1) {
                $validator->errors()->add(
                    'donation_target',
                    'يجب اختيار برنامج أو مشروع أو حملة تبرع واحدة فقط.'
                );

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Validate case
            |--------------------------------------------------------------------------
            */

            if ($this->filled('case_id')) {
                $case = CaseModel::find(
                    $this->input('case_id')
                );

                if (!$case) {
                    return;
                }

                if (
                    $this->filled('program_id') &&
                    (int) $case->program_id !==
                    (int) $this->input('program_id')
                ) {
                    $validator->errors()->add(
                        'case_id',
                        'الحالة المختارة لا تتبع البرنامج المحدد.'
                    );
                }

                if (
                    $this->filled('project_id') &&
                    (int) $case->project_id !==
                    (int) $this->input('project_id')
                ) {
                    $validator->errors()->add(
                        'case_id',
                        'الحالة المختارة لا تتبع المشروع المحدد.'
                    );
                }

                if (
                    $this->filled('donation_campaign_id') &&
                    (int) $case->donation_campaign_id !==
                    (int) $this->input('donation_campaign_id')
                ) {
                    $validator->errors()->add(
                        'case_id',
                        'الحالة المختارة لا تتبع حملة التبرع المحددة.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Validate donation package
            |--------------------------------------------------------------------------
            */

            if ($this->filled('donation_package_id')) {
                $package = ProgramDonationPackage::find(
                    $this->input('donation_package_id')
                );

                if (!$package) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | Package target
                |--------------------------------------------------------------------------
                */

                if (
                    $this->filled('program_id') &&
                    (int) $package->program_id !==
                    (int) $this->input('program_id')
                ) {
                    $validator->errors()->add(
                        'donation_package_id',
                        'الباقة المختارة لا تتبع البرنامج المحدد.'
                    );
                }

                if (
                    $this->filled('project_id') &&
                    (int) $package->project_id !==
                    (int) $this->input('project_id')
                ) {
                    $validator->errors()->add(
                        'donation_package_id',
                        'الباقة المختارة لا تتبع المشروع المحدد.'
                    );
                }

                if (
                    $this->filled('donation_campaign_id') &&
                    (int) $package->donation_campaign_id !==
                    (int) $this->input('donation_campaign_id')
                ) {
                    $validator->errors()->add(
                        'donation_package_id',
                        'الباقة المختارة لا تتبع حملة التبرع المحددة.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Package amount
                |--------------------------------------------------------------------------
                */

                if (
                    $package->amount !== null &&
                    (float) $this->input('amount') !==
                    (float) $package->amount
                ) {
                    $validator->errors()->add(
                        'amount',
                        'مبلغ التبرع يجب أن يطابق مبلغ الباقة المختارة.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Package status
                |--------------------------------------------------------------------------
                */

                if (!$package->is_active) {
                    $validator->errors()->add(
                        'donation_package_id',
                        'الباقة المختارة غير متاحة حاليًا.'
                    );
                }
            }
        });
    }
}