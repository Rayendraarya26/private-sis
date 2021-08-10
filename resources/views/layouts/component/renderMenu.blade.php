@foreach($children as $menu)
    <li class="dt-side-nav__item" style="padding-left: 20px">
        <a href="{{ count($menu->children) ? 'javascript:void(0)' : action($menu->action_controller) }}"
           class="dt-side-nav__link {{count($menu->children) ? 'dt-side-nav__arrow' :''}}"
           style="padding-left: 50px;position: relative;"
           title="{{$menu->menu_name}}">
            <i class="icon {{$menu->menu_icon}}"></i>
            <span class="dt-side-nav__text">{{$menu->menu_name}}</span>
        </a>

        @if(count($menu->children))
            <ul class="dt-side-nav__sub-menu">
                @include('layouts.component.renderMenu',['children' => $menu->children])
            </ul>
        @endif
    </li>
@endforeach



