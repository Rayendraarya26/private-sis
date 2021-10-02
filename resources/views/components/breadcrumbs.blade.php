<ol class="mb-0 breadcrumb">
    @if(isset($breadcrumbs))
        @foreach($breadcrumbs as $b)
            @if($loop->last)
                <li class="active breadcrumb-item">{{$b->title}}</li>
            @else
                <li class="breadcrumb-item"><a href="{{$b->link}}">{{$b->title}}</a></li>
            @endif
        @endforeach
    @endif
</ol>
