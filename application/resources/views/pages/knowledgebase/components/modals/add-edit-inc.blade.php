<!--title-->
<div class="form-group row">
    <label class="col-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.title')) }}*</label>
    <div class="col-12">
        <input type="text" class="form-control form-control-sm" id="knowledgebase_title" name="knowledgebase_title"
            value="{{ $knowledgebase->knowledgebase_title ?? '' }}">
    </div>
</div>

<!--category-->
@if(is_numeric(request('knowledgebase_categoryid')))
<input type="hidden" name="knowledgebase_categoryid" value="{{ request('knowledgebase_categoryid') }}">
@else
<div class="form-group row">
    <label for="example-month-input"
        class="col-12 control-label  col-form-label text-left required">{{ cleanLang(__('lang.category')) }}*</label>
    <div class="col-12">
        <select class="select2-basic form-control form-control-sm" id="knowledgebase_categoryid"
            name="knowledgebase_categoryid">
            <option></option>
            @foreach($categories as $category)
            <option value="{{ $category->kbcategory_id }}" data-category-type="{{ $category->kbcategory_type }}"
                {{ runtimePreselected(@request('knowledgebase_categoryid'), $category->kbcategory_id) }}>{{
                        runtimeLang($category->kbcategory_title) }}</option>
            @endforeach
        </select>
    </div>
</div>
@endif


<!--text description-->
<div class="form-group row {{ runtimeVisibilityKBArticle('text', request('category_type')) }}" id="kb-template-options-container">
    <label class="col-12 text-left control-label col-form-label">Plantilla visual</label>
    <div class="col-12">
        <select class="form-control form-control-sm" id="knowledgebase_visual_template" name="knowledgebase_visual_template">
            <option value="">Sin plantilla (editor libre)</option>
            <option value="style1">Plantilla 1 · Azul suave</option>
            <option value="style2">Plantilla 2 · Verde suave</option>
            <option value="style3">Plantilla 3 · Violeta suave</option>
        </select>
        <div class="m-t-8">
            <label class="m-b-0 cursor-pointer">
                <input type="checkbox" id="kb_template_include_logo" checked>
                Incluir bloque de logo + título + texto
            </label>
        </div>
        <div class="m-t-8">
            <button type="button" class="btn btn-outline-info btn-sm" id="kb-apply-template-btn">Aplicar plantilla</button>
        </div>
    </div>
</div>

<div class="form-group row {{ runtimeVisibilityKBArticle('text', request('category_type')) }}" id="article-text-editor-container">
    <label
        class="col-12 text-left control-label col-form-label required">{{ cleanLang(__('lang.description')) }}*</label>
    <div class="col-12">
        <textarea class="form-control form-control-sm tinymce-textarea" rows="5" name="knowledgebase_text"
            id="knowledgebase_text">{{ $knowledgebase->knowledgebase_text ?? '' }}</textarea>
    </div>
</div>


<!--embed code-->
<div class="form-group row {{ runtimeVisibilityKBArticle('video', request('category_type')) }}" id="article-embed-code-container">
    <label class="col-12 text-left control-label col-form-label required">@lang('lang.youtube_embed_code')*</label>
    <div class="col-12">
        <textarea class="form-control form-control-sm" rows="5" name="knowledgebase_embed_code"
            id="knowledgebase_embed_code">{{ $knowledgebase->knowledgebase_embed_code ?? '' }}</textarea>
    </div>
    <div class="col-12 p-t-10">
    <div class="alert alert-info">@lang('lang.video_article_notes')</div>
    </div>
</div>

<!--notes-->
<div class="row">
    <div class="col-12">
        <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
    </div>
</div>