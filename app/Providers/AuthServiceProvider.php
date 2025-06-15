<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\ProjectForm;
use App\Models\Profile;
use App\Policies\ProjectFormPolicy;
use App\Policies\ProfilePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * خريطة الموديلات إلى السياسات.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        ProjectForm::class => ProjectFormPolicy::class,
        Profile::class     => ProfilePolicy::class, 
    ];

    /**
     * تسجيل السياسات.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // أي تعريفات إضافية على Gate إن رغبت
        // Gate::define('update-profile', [ProfilePolicy::class, 'update']);
    }
}
