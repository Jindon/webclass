<?php

namespace App\Policies;

use App\Models\Section;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class SectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Section  $section
     * @return mixed
     */
    public function manage(Admin $admin, Section $section)
    {
        return $admin->institute_id == $section->institute_id;
    }

}
