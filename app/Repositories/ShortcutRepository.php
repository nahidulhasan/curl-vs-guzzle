<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\User;
use App\Models\Shortcut;
use App\Models\ShortcutUser;
use Illuminate\Support\Facades\DB;

/**
 * Class ShortcutRepository
 * @package App\Repositories
 */
class ShortcutRepository extends BaseRepository
{

    /**
     * @var Shortcut
     */

    protected $model;


    /**
     * ShortcutRepository constructor.
     * @param Shortcut $model
     */
    public function __construct(Shortcut $model)
    {
        $this->model = $model;
    }


    /**
     * Retrieve shortcut list
     *
     * @return mixed
     */
    public function getAllShortcut()
    {
        return $this->model::get();
    }

    /**
     * Retrieve shortcut list for specific user
     *
     * @param $user_id
     * @return mixed
     */
    public function getShortcutWithUser($user_id)
    {
        $shortcut_count = ShortcutUser::where('user_id', $user_id)->count();

        $connection_type = Customer::connectionType(Customer::find($user_id));

        $shortcut_default = $this->model->where(function ($q) use ($connection_type) {
            $q->where('customer_type', $connection_type)->orWhere('customer_type', 'ALL');
        })->orderBy('display_order', 'ASC')->get();

        if ($shortcut_count > 0) {
            $shortcuts = $this->model::where('user_id', $user_id)->where(function ($q) use ($connection_type) {
                $q->where('customer_type', $connection_type)->orWhere('customer_type', 'ALL');
            })
                ->join('shortcut_user', 'shortcuts.id', '=', 'shortcut_user.shortcut_id')
                ->select(
                    'shortcuts.id',
                    'title',
                    'icon',
                    'shortcuts.component_identifier',
                    'shortcut_user.sequence',
                    'shortcuts.is_default',
                    'shortcuts.dial_number',
                    'shortcuts.other_info',
                    'shortcut_user.is_enable'
                )
                ->orderby('sequence')
                ->get();

            return $shortcut_default->merge($shortcuts);
        }

        return $shortcut_default;
    }


    /**
     * Fetch default shortcut
     *
     * @return mixed
     */
    public function getDefaultShortcut()
    {
        return $this->model::where('is_default', 1)->get();
    }


    /**
     * Calculate shortcut limit
     *
     * @return mixed
     */
    public function getShortcutLimit()
    {
        $limit = env('SHORTCUT_LIMIT');
        $count_default_shortcut = $this->getDefaultShortcut()->count();

        $final_limit = $limit + $count_default_shortcut;

        return $final_limit;
    }


    /**
     * Added  multiple shortcut to user profile
     *
     * @param $shortcuts
     * @param $user_id
     * @return bool
     */
    public function addMultipleShortcutToUserProfile($shortcuts, $user_id)
    {
        $this->attachMultipleShortcutToUserProfile($shortcuts, $user_id);

        return true;
    }


    /**
     * Attach multiple shortcut to user profile
     *
     * @param $shortcuts
     * @param $user_id
     */
    public function attachMultipleShortcutToUserProfile($shortcuts, $user_id): void
    {
        $shortcut_list = [];

        $user = Customer::find($user_id);

        $sequence = ShortcutUser::where('user_id', $user_id)->max('sequence');

        foreach ($shortcuts as $key => $value) {
            $shortcut_list[$value] = ['sequence' => ($sequence + $key + 1), 'is_enable' => 1];
        }

        $user->shortcuts()->sync($shortcut_list);
    }


    /**
     * Attach shortcut to user profile
     *
     * @param $shortcut_id
     * @param $user_id
     */
    public function attachShortcutToUserProfile($shortcut_id, $user_id): void
    {
        $shortcut = $this->model::find($shortcut_id);

        $sequence = ShortcutUser::where('user_id', $user_id)->max('sequence');

        $user = User::find($user_id);

        $shortcut->users()->attach($user, ['sequence' => $sequence + 1]);
    }


    /**
     * Check Shortcut limit
     *
     * @param $request
     * @return bool
     */
    public function checkShortcutLimit($request)
    {
        $shortcut_limit = env('SHORTCUT_LIMIT');

        $shortcut_count = count($request->shortcut_id);

        if ($shortcut_count > $shortcut_limit) {
            return  true;
        }

        return false;
    }


    /**
     * Count shortcut for specific users
     *
     * @param $user_id
     * @return mixed
     */
    public function getCurrentShortcutCount($user_id)
    {
        $shortcut_count = ShortcutUser::where('user_id', $user_id)->count();

        return $shortcut_count;
    }


    /**
     * Remove shortcut from user profile
     *
     * @param $request
     * @return string
     */
    public function removeShortcutFromUserProfile($request)
    {
        $user_id = $request->input('user_id');

        $shortcut_id = $request->input('shortcut_id');

        $shortcut = $this->model::find($shortcut_id);

        $user = User::find($user_id);

        $shortcut->users()->detach($user);
    }


    /**
     * Remove shortcut
     *
     * @param $user_id
     * @param $shortcut_id
     */
    public function removeMultipleShortcutFromUserProfile($user_id, $shortcut_id)
    {
        DB::table('shortcut_user')-> where('user_id', $user_id)
                           -> whereIn('shortcut_id', $shortcut_id)->delete();
    }


    /**
     * Check it is already added or not
     *
     * @param $user_id
     * @param $shortcutIds
     * @return mixed
     */
    public function checkExistOrNot($user_id, $shortcutIds)
    {
        $sql = ShortcutUser::where('user_id', $user_id);

        if (is_array($shortcutIds)) {
            $sql->whereIn('shortcut_id', $shortcutIds);
        }

        $sql->where('shortcut_id', $shortcutIds);

        $shortcut =   $sql->first();

        return $shortcut;
    }


    /**
     * Check it is default shortcut
     *
     * @param $shortcutId
     * @return mixed
     */
    public function checkDefaultShortcut($shortcutId)
    {
        $shortcut = $this->model::where('id', $shortcutId)
                            ->where('is_default', 1)
                             ->first();

        if ($shortcut) {
            return true;
        }

        return false;
    }


    /**
     * Arrange shortcut
     *
     * @param $user_id
     * @param $shortcuts
     * @return string
     */
    public function arrangeShortcut($user_id, $shortcuts)
    {
        foreach ($shortcuts as $key => $shortcut_id) {
            $sequence = $key + 1;
            $update["sequence"] = $sequence;
            $update["shortcut_id"] = $shortcut_id;

            ShortcutUser::where('user_id', $user_id)
                        ->where('shortcut_id', $shortcut_id)->update($update);
        }
    }
}
