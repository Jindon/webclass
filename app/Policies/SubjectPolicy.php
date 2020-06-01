<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Subject  $subject
     * @return mixed
     */
    public function manage(Admin $admin, Subject $subject)
    {
        return $admin->institute_id == $subject->institute_id;
    }
}
