@if(isset($categories))
<style>
    .kb-visibility-tag {
        display: inline-block;
        margin-bottom: 14px;
        border-radius: 14px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .kb-visibility-tag-public {
        background-color: #e6f7ee;
        border-color: #9fdfbe;
        color: #127a4a;
    }

    .kb-visibility-tag-private {
        background-color: #ffecec;
        border-color: #f5b5b5;
        color: #b4232a;
    }
</style>
@foreach($categories as $category)
<!--each category-->
<div class="col-sm-12 col-md-4 col-lg-3" id="category_{{ $category->kbcategory_id ?? '' }}">
    <div class="card kb-category">
        <div class="card-body">
            <!--visibility-->
            @if(auth()->user()->type == 'team')
            <span class="label label-with-icon kb-visibility-tag {{ $category->kbcategory_visibility == 'team' ? 'kb-visibility-tag-private' : 'kb-visibility-tag-public' }}" title="{{ runtimeLang($category->kbcategory_visibility) }}">
                <i class="sl-icon-eye"></i>
                {{ $category->kbcategory_visibility == 'team' ? cleanLang(__('lang.private')) : cleanLang(__('lang.public')) }}
            </span>
            @endif
            <!--category icon-->
            <div class="kb-category-icon"><span><i class="{{ $category->kbcategory_icon ?? 'sl-icon-docs' }}"></i></span></div>
            <!--title-->
            <h5 class="card-title">{{ $category->kbcategory_title ?? '' }}</h5>
            <!--description-->
            <div class="card-text">{!! clean($category->kbcategory_description ?? '---') !!}</div>
            <a href="/kb/articles/{{ $category->kbcategory_slug }}" class="btn btn-sm btn-rounded-x btn-outline-info">{{ cleanLang(__('lang.see_articles')) }}</a>
        </div>
    </div>
</div>
<!--each category-->
@endforeach
@endif