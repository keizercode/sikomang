<?php

use App\Models\Menu;
use App\Models\AccessMenu;
use Illuminate\Support\Str;

if (!function_exists('encode_id')) {
    function encode_id(?string $val = ''): string
    {
        $params = ['val' => $val];
        return rtrim(base64_encode(serialize($params)), "=");
    }
}

if (!function_exists('decode_id')) {
    function decode_id(?string $val = ''): mixed
    {
        $secure = unserialize(base64_decode($val));
        return $secure ? $secure['val'] : null;
    }
}

if (!function_exists('permission')) {
    function permission($access, $key, string $method = 'menu', bool $view = false): mixed
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        // Super admin has all access
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($method == 'module') {
            if (is_array($access)) {
                $model = AccessMenu::where('module', 'LIKE', "{$key}%")
                    ->where('ms_group_id', $user->ms_group_id)
                    ->first();

                if (!$model) {
                    return $view ? false : abort(401);
                }

                $query = count(array_intersect((array)$access, (array)$model->toArray()));
            } else {
                $query = AccessMenu::where($access, true)
                    ->where('module', 'LIKE', $key . '%')
                    ->where('ms_group_id', $user->ms_group_id)
                    ->count();

                if ($query == 0 && !$view) {
                    return abort(401);
                }
            }
        } else {
            $query = AccessMenu::where($access, true)
                ->where('ms_menu_id', $key)
                ->where('ms_group_id', $user->ms_group_id)
                ->count();
        }

        return $query > 0;
    }
}

if (!function_exists('access')) {
    function access($access, $key): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return true;
        }

        $query = AccessMenu::where($access, 1)
            ->where('module', $key)
            ->where('ms_group_id', $user->ms_group_id)
            ->count();

        return $query > 0;
    }
}

if (!function_exists('renderMenu')) {
    function renderMenu(): string
    {
        if (!auth()->check()) {
            return '';
        }

        $user = auth()->user();
        $parent = Menu::where('status', true)
            ->where('menu_type', 'sidebar')
            ->where('parent_id', 0)
            ->orderBy('ordering', 'ASC')
            ->get();

        $html = '';

        foreach ($parent as $p1) {
            $child2 = Menu::where('status', true)
                ->where('menu_type', 'sidebar')
                ->where('parent_id', $p1->MsMenuId)
                ->orderBy('ordering', 'ASC')
                ->get();

            $access1 = permission('is_read', $p1->MsMenuId, 'menu', true);

            if (!$access1 && !$user->isSuperAdmin()) {
                continue;
            }

            $ch1 = count($child2) > 0 ? 'has-arrow' : '';
            $collapse1 = count($child2) > 0 ? 'href="javascript: void(0);"' : 'href="' . url($p1->url) . '"';
            $active = request()->is(ltrim($p1->url, '/') . '*') ? 'mm-active' : '';

            $html .= '<li class="' . $active . '">
                <a ' . $collapse1 . ' class="' . $ch1 . '">
                <i class="' . $p1->menu_icons . ' icon nav-icon"></i>
                <span>' . e($p1->title) . '</span>';

            if (count($child2) > 0) {
                $html .= '<span class="menu-arrow"></span>';
                $html .= '</a>';
                $html .= '<ul class="sub-menu" aria-expanded="false">';

                foreach ($child2 as $p2) {
                    $access2 = permission('is_read', $p2->MsMenuId, 'menu', true);

                    if (!$access2 && !$user->isSuperAdmin()) {
                        continue;
                    }

                    $active2 = request()->is(ltrim($p2->url, '/') . '*') ? 'mm-active' : '';
                    $html .= '<li class="' . $active2 . '">
                        <a href="' . url($p2->url) . '">
                        <i class="' . $p2->menu_icons . ' icon nav-icon"></i>
                        <span>' . e($p2->title) . '</span>
                        </a>
                    </li>';
                }

                $html .= '</ul>';
            } else {
                $html .= '</a>';
            }

            $html .= '</li>';
        }

        return $html;
    }
}

if (!function_exists('dateTime')) {
    function dateTime($date): string
    {
        return date('d-m-Y H:i:s', strtotime($date ?? 'now'));
    }
}

if (!function_exists('activeMenuClass')) {
    function activeMenuClass($route): bool
    {
        return str_is($route, request()->route()->getName());
    }
}
