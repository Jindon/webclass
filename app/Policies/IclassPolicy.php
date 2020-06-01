<?php

namespace App\Policies;

use App\Models\Iclass;
use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class IclassPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can manage the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Iclass  $iclass
     * @return mixed
     */
    public function manage(Admin $admin, Iclass $iclass)
    {
        return $admin->institute_id == $iclass->institute_id;
    }
}
